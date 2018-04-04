<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Helpers;
use BEA\Media_Analytics\Singleton;

class Option {
	use Singleton;

	protected function init() {
		add_action( 'acf/save_post', [ __CLASS__, 'acf_save_post' ], 5000, 3 );
	}

	/**
	 * On save option, index option's media
	 *
	 * @author Amaury balmer
	 * @since  2.1.0
	 *
	 * @param $post_id
	 */
	public static function acf_save_post( $post_id ) {
		if ( $post_id !== 'options' ) {
			return;
		}

		$pages = acf_options_page()->get_pages();
		foreach ( $pages as $page ) {
			// Save only current page data
			if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) {
				if ( $page['menu_slug'] != $_GET['page'] ) {
					continue;
				}
			}

			self::index_page_option( $page['menu_slug'] );
		}
	}

	/**
	 * Index medias for a ACF option page
	 *
	 * @author Amaury balmer
	 * @since  2.1.0
	 *
	 * @param string $page_menu_slug
	 */
	public static function index_page_option( $page_menu_slug ) {
		/**
		 * Fires once a post has been saved.
		 *
		 * Get images from multiple sources to index against post
		 *
		 * @since 2.1.0
		 *
		 * @param array  $image_ids      Array of images id.
		 * @param string $page_menu_slug ACF menu slug.
		 */
		$image_ids = apply_filters( 'bea.media_analytics.option.index', [], $page_menu_slug );
		if ( empty( $image_ids ) ) {
			return;
		}

		// Validate image IDs
		$image_ids = Helpers::check_image_ids( $image_ids );

		DB::insert( $image_ids, $page_menu_slug, 'acf-option' );
	}

}