<?php namespace BEA\Media_Analytics\Addons\Image_Map_Pro;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Helpers;
use BEA\Media_Analytics\Helper\Helper;
use BEA\Media_Analytics\Singleton;

class Main {

	use Singleton;

	protected function init() {
		add_filter( 'pre_update_option_' . 'image-map-pro-fragmented-saves', array(
			$this,
			'pre_update_option'
		), 10, 2 );
		add_filter( 'bea.media_analytics.media.edit_view_context', array( $this, 'edit_view_context' ), 10, 4 );

		add_filter( 'bea.media_analytics.cli.index_site', array( $this, 'index_site' ), 10 );
	}

	/**
	 * Add image map pro indexation into WP-CLi command
	 *
	 * @param integer $i
	 *
	 * @since  2.1.0
	 * @author Amaury BALMER
	 */
	public function index_site( $i ) {
		\WP_CLI::warning( 'Image Map Pro indexing.' );
		$i ++;

		$value = get_option( 'image-map-pro-fragmented-saves' );
		$this->pre_update_option( $value, $value );
	}

	/**
	 * Show in edit media view the plugin context
	 *
	 * @param $html
	 * @param $types
	 * @param $content_id
	 * @param $media_id
	 *
	 * @since  2.1.0
	 * @author Amaury BALMER
	 *
	 * @return string
	 */
	public function edit_view_context( $html, $types, $content_id, $media_id ) {
		if ( $types[0] !== 'image-map-pro' ) {
			return $html;
		}

		$html .= '<li>Plugin : Image Map Pro</li>';

		return $html;
	}

	/**
	 * Listen plugin update option for extract image used
	 *
	 * @param $value
	 * @param $old_value
	 *
	 * @since  2.1.0
	 * @author Amaury BALMER
	 *
	 * @return mixed
	 */
	public function pre_update_option( $value, $old_value ) {
		if ( empty( $value['saves'] ) ) {
			return $value;
		}

		$medias = array();

		// Get all fragments for extract all links
		foreach ( $value['saves'] as $save_id => $save_data ) {
			foreach ( $save_data['fragments'] as $fragment ) {
				preg_match_all( '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $fragment, $matches );

				if ( ! empty( $matches ) && isset( $matches[0] ) ) {
					foreach ( (array) $matches[0] as $url ) {
						// Keep only local medias URL
						if ( strpos( $url, WP_CONTENT_URL ) !== false ) {
							$medias[] = $url;
						}
					}
				}
			}
		}

		if ( empty( $medias ) ) {
			return $value;
		}

		// Loop on medias for get ID instead URL
		$medias = array_map( [ 'BEA\Media_Analytics\Helper\Helper', 'get_attachment_id_from_url' ], $medias );
		$medias = array_filter( $medias );

		// Validate image IDs
		$medias = Helper::merge_old_with_new( [], $medias, 'image-map-pro' );
		$medias = Helpers::check_image_ids( $medias );

		DB::insert( $medias, 'image-map-pro', 'image-map-pro' );

		return $value;
	}
}