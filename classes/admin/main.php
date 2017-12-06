<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\Helper\Helper;
use BEA\Find_Media\Singleton;
use BEA\Find_Media\Helper\Post;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	private $_acf_fields = array();

	protected function init() {
		// Indexation
		add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_content' ], 10, 2 );
		add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_thumbnail' ], 10, 2 );
		add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_acf_fields' ], 10, 2 );

		// Indexation for post content
		add_filter( 'bea.find_media.helper.get_media.post_content', [ $this, 'get_media_from_text' ], 10, 2 );
		add_filter( 'bea.find_media.helper.get_media.post_content', [ $this, 'get_media_from_links' ], 10, 2 );
		add_filter( 'bea.find_media.helper.get_media.post_content', [ $this, 'get_media_from_shortcode_gallery' ], 10, 2 );
	}

	/**
	 * Parse the given post's content to get used images' ids
	 *
	 * @param array $media_ids
	 * @param int   $post_id
	 *
	 * @author Maxime CULEA
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function add_media_from_post_content( $media_ids, $post_id ) {
		$post_content = get_post( $post_id )->post_content;
		if ( empty( $post_content ) ) {
			return $media_ids;
		}

		/**
		 * From post content, get image ids
		 *
		 * @since 1.0.0
		 *
		 * @param array $found_medias Array of found images id.
		 * @param string $post_content Post content.
		 */
		$found_medias = apply_filters( 'bea.find_media.helper.get_media.post_content', [], $post_content );
		if ( empty( $found_medias ) ) {
			return $media_ids;
		}

		return Helper::merge_old_with_new( $media_ids, $found_medias, 'post_content' );
	}

	/**
	 * Get post's thumbnail id
	 *
	 * @param array $media_ids
	 * @param int   $post_id
	 *
	 * @author Maxime CULEA
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function add_media_from_post_thumbnail( $media_ids, $post_id ) {
		$thumb_id = get_post_thumbnail_id( $post_id ) ?: 0;
		if ( empty( $thumb_id ) ) {
			return $media_ids;
		}

		return Helper::merge_old_with_new( $media_ids, [ $thumb_id ], 'post_thumbnail' );
	}

	/**
	 * Get post's acf fields
	 *
	 * @param array $media_ids
	 * @param int   $post_id
	 *
	 * @author Amaury BALMER
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function add_media_from_post_acf_fields( $media_ids, $post_id ) {
		// ACF is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return $media_ids;
		}

		$new_post = get_post( $post_id );
		if ( false === $new_post || is_wp_error( $new_post ) ) {
			return $media_ids;
		}

		if ( 'attachment' === $new_post->post_type ) {
			// get field groups
			$groups = acf_get_field_groups( array( 'attachment' => $new_post->ID ) );
		} else {
			$groups = acf_get_field_groups( array( 'post_type' => $new_post->post_type ) );
		}

		// No groups, no fields...
		if ( empty( $groups ) ) {
			return $media_ids;
		}

		$fields = array();
		foreach ( $groups as $group ) {
			$fields += (array) acf_get_fields( $group );
		}

		// Get only fields with medias
		$this->_acf_fields = array();
		$this->prepare_acf_fields( $fields );

		// Loop on each field
		$found_medias = array();
		foreach ( (array) $this->_acf_fields as $field ) {
			$value = get_post_meta( $post_id, $field['name'], true );
			if ( empty( $value ) ) {
				continue;
			}

			$found_medias = array_merge( (array) $value, $found_medias );
		}

		// Keep only valid ID, remove Zero values
		$found_medias = array_map( 'intval', $found_medias );
		$found_medias = array_filter( $found_medias );

		return Helper::merge_old_with_new( $media_ids, $found_medias, 'acf' );
	}

	/**
	 * Extract from group fields only ACF field with ID database reference (recursive !)
	 *
	 * @param array $fields
	 */
	private function prepare_acf_fields( $fields ) {
		foreach ( (array) $fields as $field ) {
			if (in_array($field['type'], array('flexible_content') ) ) { // Flexible is recursive structure with layouts
				foreach( $field['layouts'] as $layout_field ) {
					$this->prepare_acf_fields( $layout_field['sub_fields'] );
				}
			} elseif (in_array($field['type'], array('repeater') ) ) { // Repeater is recursive structure
				$this->prepare_acf_fields( $field['sub_fields'] );
			} elseif ( in_array($field['type'], array('image', 'gallery', 'post_object', 'relationship', 'file', 'page_link') ) ) {
				$this->_acf_fields[ $field['key'] ] = $field;
			}
		}
	}

	/**
	 * Get media ids from text
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @since 1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function get_media_from_text( $media_ids, $post_content ) {
		return array_merge( $media_ids, Post::get_media_from_text( $post_content ) );
	}

	/**
	 * Get media ids from links
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @since 1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function get_media_from_links( $media_ids, $post_content ) {
		return array_merge( $media_ids, Post::get_media_from_links( $post_content ) );
	}

	/**
	 * Get media ids from links
	 *
	 * @param array $media_ids
	 * @param string $post_content
	 *
	 * @since 1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function get_media_from_shortcode_gallery( $media_ids, $post_content ) {
		return array_merge( $media_ids, Post::get_media_from_shortcode_gallery( $post_content ) );
	}
}