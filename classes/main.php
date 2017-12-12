<?php namespace BEA\Find_Media;

use \BEA\Find_Media\Admin\Post;

class Main {
	use Singleton;

	protected function init() {
		add_filter( 'bea.find_media.db.get_data', [ __CLASS__, 'format_indexed_values' ], 100 );
		add_action( 'init', [ $this, 'init_translations' ] );

		// JS i18n
		add_action( 'admin_enqueue_scripts', [ $this, 'localize_scripts' ], 40 );
	}

	/**
	 * Format the data :
	 *
	 * {object_type} : [
	 *      {media_id} : [
	 *          {object_id} : [ {type}, {type} ]
	 *      ]
	 * ]
	 *
	 * @param array $data
	 *
	 * @since  1.0.O
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function format_indexed_values( $data ) {
		if ( empty( $data ) ) {
			return [];
		}

		$out = [];
		foreach ( $data as $_d ) {
			if ( isset( $out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ] ) && in_array( $_d->type, $out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ] ) ) {
				// Already exists
				continue;
			}

			if ( empty( $out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ] ) ) {
				// First value
				$out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ][] = $_d->type;
			} else {
				// Adding to the others
				array_push( $out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ], $_d->type );
			}
		}

		return $out;
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
			$out[ $blog_id ] = self::format_indexed_values( $_d );
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
				'time_singular'   => __( 'time', 'bea-find-media' ),
				'time_plural'     => __( 'times', 'bea-find-media' ),
				'warning_confirm' => _x( "This media is currently used %s. Are you sure you want to delete it ?\nThis action is irreversible !\n«Cancel» to stop, «OK» to delete.", 'Popup for confirmation media delete. %s will display the number with the singular / plural string (time/times).', 'bea-find-media' ),
			]
		];

		/**
		 * Filter strings for localize scripts usage
		 *
		 * @since 1.0.1
		 *
		 * @param array $strings
		 */
		$strings = apply_filters( 'bea.find_media.main.localize_scripts', $strings );
		wp_localize_script( 'bea-find-media', 'bea_find_media', $strings );
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
		load_plugin_textdomain( 'bea-find-media', false, BEA_FIND_MEDIA_PLUGIN_DIRNAME . '/languages' );
	}
}