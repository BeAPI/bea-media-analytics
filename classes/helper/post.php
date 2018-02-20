<?php namespace BEA\Media_Analytics\Helper;

use BEA\Media_Analytics\Singleton;

class Post extends Helper {

	use Singleton;

	private $_acf_fields = array();
	private $_found_medias = array();

	/**
	 * Parse post's ACF fields to get media ids
	 *
	 * @param int $post_id
	 *
	 * @author Amaury BALMER
	 * @since  1.0.0
	 *
	 * @return array Media ids
	 */
	public function get_media_from_acf_fields( $post_id ) {
		// ACF PRO is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		$new_post = get_post( $post_id );
		if ( false === $new_post || is_wp_error( $new_post ) ) {
			return [];
		}

		// Get only fields with medias
		$this->_acf_fields   = array();
		$this->_found_medias = array();

		// Get media possible fields
		$this->recursive_get_post_media_fields( get_field_objects( $post_id ) );

		// Use media fields to get media ids
		$this->recursive_get_post_medias( get_fields( $post_id ) );

		// Keep only valid ID && remove zero values
		return array_filter( array_map( 'intval', $this->_found_medias ) );
	}
	/**
	 * Recursive way to extract all possible fields for a post
	 * TODO : Maybe better save this fields somewhere (one time)
	 *
	 * @since 2.0.4
	 *
	 * @author Maxime CULEA
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_media_fields( $fields ) {
		if ( empty( $fields ) ) {
			return;
		}

		foreach ( (array) $fields as $key => $field ) {
			if ( in_array( $field['type'], array( 'flexible_content' ) ) ) {
				// Flexible is recursive structure with sub_fields into layouts
				foreach ( $field['layouts'] as $layout_field ) {
					$this->recursive_get_post_media_fields( $layout_field['sub_fields'] );
				}
			} elseif ( in_array( $field['type'], [ 'repeater', 'clone', 'group' ] ) ) {
				// Repeater is recursive structure with sub_fields
				$this->recursive_get_post_media_fields( $field['sub_fields'] );
			} elseif ( in_array( $field['type'], [
				'image',
				'gallery',
				'post_object',
				'relationship',
				'file',
				'page_link'
			] ) ) {
				// All type of ACF Fieds which involve media
				$this->_acf_fields[ $field['key'] ] = $field['name'];
			}
		}
	}

	/**
	 * From media fields, get media ids
	 *
	 * @since 2.0.4
	 *
	 * @author Maxime CULEA
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_medias( $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( is_array( $field ) ) {
				// If not final key => field, recursively relaunch
				$this->recursive_get_post_medias( $field );
			}

			if ( empty( $field ) || is_array( $field ) || ! in_array( $key, $this->_acf_fields ) ) {
				// Go to next one if empty, array (already recursively relaunched) and the key is not a media field
				continue;
			}

			// Save the media ID
			$this->_found_medias = array_merge( $this->_found_medias, (array) $field );
		}
	}
}