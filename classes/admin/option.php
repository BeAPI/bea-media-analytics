<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Helpers;
use BEA\Media_Analytics\Singleton;

class Option {
	use Singleton;

	protected function init() {
		add_action( 'acf/save_post', [ __CLASS__, 'index_option' ], 20, 3 );
	}

	/**
	 * On save option, index post's media
	 *
	 * @author Maxime CULEA
	 * @since  1.0.0
	 *
	 * @param $post_id
	 */
	public static function index_option( $post_id ) {
		if ( $post_id !== 'options' ) {
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
		$image_ids = apply_filters( 'bea.media_analytics.option.index', [], $post_id );
		if ( empty( $image_ids ) ) {
			return;
		}

		// Validate image IDs
		$image_ids = Helpers::check_image_ids( $image_ids );

		// Get unique ID for each option page
		$screen = get_current_screen();

		DB::insert( $image_ids, $screen->id, 'post' );
	}

}