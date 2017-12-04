<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\DB;
use BEA\Find_Media\Singleton;

class Post {
	use Singleton;

	protected function init() {
		add_action( 'save_post', [ $this, 'index_post' ], 20, 3 );
		add_action( 'delete_post', [ $this, 'delete_post' ] );
	}

	/**
	 * On save post, index post's media
	 *
	 * @author Maxime CULEA
	 * @since 1.0.0
	 */
	public function index_post( $post_id, $post, $update ) {
		if ( in_array( $post->post_status, [ 'trash', 'auto-draft', 'inherit' ] ) ) {
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
		 * @param int $post_id Post ID.
		 */
		$image_ids = apply_filters( 'bea.find_media.post.index', [], $post_id );
		if ( empty( $image_ids ) ) {
			return;
		}

		DB::insert( $image_ids, $post_id, 'post' );
	}

	/**
	 * On post delete, delete all associated data
	 *
	 * @author Maxime CULEA
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	public function delete_post( $post_id ) {
		DB::delete_all_object_id( $post_id, 'post' );
	}
}

// TEST
add_filter( 'bea.find_media.post.index', function ( $media_ids ) {
	return $media_ids;
	$media_ids[43] = [ 'post_content', 'post_thumbnail', 'acf' ];

	return $media_ids;
} );