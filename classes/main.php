<?php namespace BEA\Media_Analytics;

use \BEA\Media_Analytics\Admin\Post;

class Main {
	use Singleton;

	protected function init() {
		add_action( 'init', [ $this, 'init_translations' ] );

		// JS i18n
		add_action( 'admin_enqueue_scripts', [ $this, 'localize_scripts' ], 40 );
	}

	/**
	 * Format the data for multisite purpose:
	 *
	 * {blog_id} : [
	 *      {object_type} : [
	 *          {media_id} : [
	 *              {object_id} : [ {type}, {type} ]
	 *          ]
	 *      ]
	 * ]
	 *
	 * @param array $data
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function format_indexed_values_ms( $data ) {
		if ( empty( $data ) ) {
			return [];
		}

		$out = [];
		foreach ( $data as $blog_id => $_d ) {
			$out[ $blog_id ] = $_d;
		}

		return $out;
	}

	/**
	 * Localize plugin strings, especially for i18n
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 */
	public function localize_scripts() {
		$strings = [
			'i18n' => [
				'time_singular'   => __( 'time', 'bea-media-analytics' ),
				'time_plural'     => __( 'times', 'bea-media-analytics' ),
				'warning_confirm' => _x( "This media is currently used %s. Are you sure you want to delete it ?\nThis action is irreversible !\n«Cancel» to stop, «OK» to delete.", 'Popup for confirmation media delete. %s will display the number with the singular / plural string (time/times).', 'bea-media-analytics' ),
			]
		];

		/**
		 * Filter strings for localize scripts usage
		 *
		 * @since 1.0.1
		 *
		 * @param array $strings
		 */
		$strings = apply_filters( 'bea.media_analytics.main.localize_scripts', $strings );
		wp_localize_script( 'bea-media-analytics', 'bea_media_analytics', $strings );
	}

	/**
	 * Manage to index all contents for the current site
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 */
	public function force_indexation() {
		$contents_q = new \WP_Query( [
			'no_found_rows'  => true,
			'nopaging'       => true,
			'post_type'      => 'any',
		] );

		foreach ( $contents_q->posts as $_post ) {
			Post::index_post( $_post->ID, $_post, true );
		}
	}

	public function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-media-analytics', false, BEA_MEDIA_ANALYTICS_PLUGIN_DIRNAME . '/languages' );
	}
}