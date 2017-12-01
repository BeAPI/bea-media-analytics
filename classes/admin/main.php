<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\Helper\Helper;
use BEA\Find_Media\Singleton;

class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		// Indexation
		add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_text' ], 10, 2 );
		//add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_acf_fields' ], 10, 2 );
		//add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_meta' ], 10, 2 );
		//add_filter( 'bea.find_media.post.index', [ $this, 'add_media_from_post_thumbnail' ], 10, 2 );
	}

	/**
	 *
	 * @param $media_ids
	 * @param $post_id
	 */
	public function add_media_from_text( $media_ids, $post_id ) {
		$post_content = get_post( $post_id )->post_content;
		if ( empty( $post_content ) ) {
			return $media_ids;
		}

		$found_medias = \BEA\Find_Media\Helper\Post::get_media_from_text( $post_content );
		if ( empty( $found_medias ) ) {
			return $media_ids;
		}

		return Helper::merge_old_with_new( $media_ids, $found_medias, 'post_content' );
	}

	public function add_media_from_post_acf_fields( $media_ids, $post_id ) {

	}

	public function add_media_from_post_meta( $media_ids, $post_id ) {

	}

	public function add_media_from_post_thumbnail( $media_ids, $post_id ) {

	}
}