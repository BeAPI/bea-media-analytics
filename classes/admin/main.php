<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\Helper\Helper;
use BEA\Media_Analytics\Singleton;
use BEA\Media_Analytics\Helper\Post;
use BEA\Media_Analytics\Helper\Option;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		// Indexation
		add_filter( 'bea.media_analytics.post.index', [ $this, 'add_media_from_post_content' ], 10, 2 );
		add_filter( 'bea.media_analytics.post.index', [ $this, 'add_media_from_post_thumbnail' ], 10, 2 );
		add_filter( 'bea.media_analytics.post.index', [ $this, 'add_media_from_post_acf_fields' ], 10, 2 );

		add_filter( 'bea.media_analytics.option.index', [ $this, 'add_media_from_option_acf_fields' ], 10, 2 );

		// Indexation for post content
		add_filter( 'bea.media_analytics.helper.get_media.post_content', [ $this, 'get_media_from_text' ], 10, 2 );
		add_filter( 'bea.media_analytics.helper.get_media.post_content', [ $this, 'get_media_from_links' ], 10, 2 );
		add_filter( 'bea.media_analytics.helper.get_media.post_content', [ $this, 'get_media_from_shortcode_gallery' ], 10, 2 );
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
		 * @param array  $found_medias Array of found images id.
		 * @param string $post_content Post content.
		 */
		$found_medias = apply_filters( 'bea.media_analytics.helper.get_media.post_content', [], $post_content );
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
		return Helper::merge_old_with_new( $media_ids, Post::get_instance()->get_media_from_acf_fields( $post_id ), 'acf' );
	}

	/**
	 * Get post's acf fields
	 *
	 * @param array $media_ids
	 * @param int   $post_id
	 *
	 * @author Amaury BALMER
	 * @since  2.1.0
	 *
	 * @return array
	 */
	public function add_media_from_option_acf_fields( $media_ids, $post_id ) {
		return Helper::merge_old_with_new( $media_ids, Option::get_instance()->get_media_from_acf_fields( $post_id ), 'acf-option' );
	}

	/**
	 * Get media ids from text
	 *
	 * @param array  $media_ids
	 * @param string $post_content
	 *
	 * @since  1.0.0
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
	 * @param array  $media_ids
	 * @param string $post_content
	 *
	 * @since  1.0.0
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
	 * @param array  $media_ids
	 * @param string $post_content
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function get_media_from_shortcode_gallery( $media_ids, $post_content ) {
		return array_merge( $media_ids, Post::get_media_from_shortcode_gallery( $post_content ) );
	}
}