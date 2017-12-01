<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\DB;
use BEA\Find_Media\Helper\Post;
use BEA\Find_Media\Singleton;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	public function init() {
		add_action( 'save_post', [ $this, 'index_post' ], 20, 3 );
		add_action( 'delete_post', [ $this, 'delete_post' ] );
	}

	/**
	 * On save post, index post's media
	 */
	public function index_post( $post_id, $post, $update ) {
		if ( in_array( $post->post_status, [ 'trash', 'auto-draft', 'inherit' ] ) ) {
			return;
		}

		$media_ids = Post::get_media_from_text( $post_id );
		array_push( $media_ids, Post::get_media_from_post_acf_fields( $post_id ) );
		array_push( $media_ids, Post::get_media_from_post_meta( $post_id ) );
		array_push( $media_ids, Post::get_media_from_post_thumbnail( $post_id ) );

		DB::insert( apply_filters( 'bea.find_media.post.index', $media_ids, $post_id ), $post_id, 'post' );
	}

	public function delete_post( $post_id ) {
		DB::delete_all( $post_id, 'post' );
	}
}

// TEST
add_filter( 'bea.find_media.post.index', function ( $media_ids ) {
	return $media_ids;
	$media_ids[43] = [ 'post_content', 'post_thumbnail', 'acf' ];
	return $media_ids;
} );