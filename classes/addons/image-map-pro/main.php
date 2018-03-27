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
		$medias = array_map( array( $this, 'get_attachment_id_from_url' ), $medias );
		$medias = array_filter( $medias );

		// Validate image IDs
		$medias = Helper::merge_old_with_new( array(), $medias, 'image-map-pro' );
		$medias = Helpers::check_image_ids( $medias );

		DB::insert( $medias, 'image-map-pro', 'image-map-pro' );

		return $value;
	}

	/**
	 * Transform full URL in attachment ID
	 * credit : https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
	 *
	 * @param string $attachment_url
	 *
	 * @return int
	 */
	public function get_attachment_id_from_url( $attachment_url = '' ) {
		global $wpdb;

		// If there is no url, return.
		if ( '' == $attachment_url ) {
			return 0;
		}

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
		}

		return 0;
	}

}