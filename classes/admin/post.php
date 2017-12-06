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

		// Make an image IDs validation
		$image_ids = array_filter( $image_ids, [ $this, 'check_image_id' ], ARRAY_FILTER_USE_KEY );

		DB::insert( $image_ids, $post_id, 'post' );
	}

	/**
	 * Check image validity vs DB
	 *
	 * @param integer $image_id
	 *
	 * @return bool
	 */
	public function check_image_id( $image_id ) {
		if ( 0 === (int) $image_id ) {
			return false;
		}

		$object = get_post($image_id);
		if ( false == $object || is_wp_error($object) ) {
			return false;
		}

		if ( $object->post_type !== 'attachment' ) {
			return false;
		}

		return true;
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