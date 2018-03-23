<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Helpers;
use BEA\Media_Analytics\Singleton;

class Post {
	use Singleton;

	protected function init() {
		add_action( 'save_post', [ __CLASS__, 'index_post' ], 20, 3 );

		add_action( 'delete_post', [ $this, 'delete_post' ] );
	}

	/**
	 * On save post, index post's media
	 *
	 * @author Maxime CULEA
	 * @since  1.0.0
	 */
	public static function index_post( $post_id, $post, $update ) {
		if ( in_array( $post->post_status, [ 'trash', 'auto-draft', 'inherit' ] ) ) {
			return;
		}

		// Avoid indexation for some post types
		if ( in_array( $post->post_type, [ 'acf-field-group', 'acf-field' ] ) ) {
			return;
		}

		/**
		 * Fires once a post has been saved.
		 *
		 * Get images from multiple sources to index against post
		 *
		 * @since 1.0.0
		 *
		 * @param array $image_ids Array of images id.
		 * @param int   $post_id   Post ID.
		 */
		$image_ids = apply_filters( 'bea.media_analytics.post.index', [], $post_id );
		if ( empty( $image_ids ) ) {
			return;
		}

		// Validate image IDs
		$image_ids = Helpers::check_image_ids( $image_ids );

		DB::insert( $image_ids, $post_id, 'post' );
	}

	/**
	 * On post delete, delete all associated data
	 *
	 * @author Maxime CULEA
	 * @since  1.0.0
	 *
	 * @param $post_id
	 */
	public function delete_post( $post_id ) {
		DB::delete_all_object_id( $post_id, 'post' );
	}
}