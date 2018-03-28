<?php namespace BEA\Media_Analytics\Helper;

class Helper {
	/**
	 * From a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @author Maxime CULEA
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	static function get_media_from_text( $text ) {
		if ( empty( $text ) ) {
			return [];
		}

		// match all wp-image-{media_id} from img html classes
		preg_match_all( '/wp-image-(\d*)/', $text, $images );
		if ( empty( $images ) ) {
			return [];
		}

		return $images[1];
	}

	/**
	 * From links into a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @author Maxime CULEA
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	static function get_media_from_links( $text ) {
		$img_ids = [];
		if ( empty( $text ) ) {
			return $img_ids;
		}

		/**
		 * Match all href="" from content
		 * @see : https://regex101.com/r/63ILkx/1
		 */
		preg_match_all( '/href="([^"\\\']+)"/', $text, $urls );
		if ( empty( $urls ) ) {
			return $img_ids;
		}

		foreach ( $urls[1] as $url ) {
			// Check if retrieved media from href really exists for the current site
			$attachment_id = self::get_attachment_id_from_url( $url );
			if ( empty( $attachment_id ) ) {
				continue;
			}
			$img_ids[] = (int) $attachment_id[0];
		}

		return $img_ids;
	}

	/**
	 * Transform full URL in attachment ID
	 *
	 * @param string $attachment_url
	 *
	 * @link : https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
	 *
	 * @since future
	 * @author Amaury Balmer
	 *
	 * @return int
	 */
	static function get_attachment_id_from_url( string $attachment_url ) {
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

	/**
	 * From post content and especially gallery shortcode, get image ids
	 *
	 * @param $text
	 *
	 * @author Maxime CULEA
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	static function get_media_from_shortcode_gallery( $text ) {
		$img_ids = [];
		if ( empty( $text ) ) {
			return $img_ids;
		}

		/**
		 * Match all [gallery ids=""] from content
		 * @see : https://regex101.com/r/KkmqkL/1
		 */
		preg_match_all( '/\[gallery ids="(.*)"\]/', $text, $galleries );
		if ( empty( $galleries ) ) {
			return $img_ids;
		}

		foreach ( $galleries[1] as $gallery ) {
			$imgs = array_map( 'intval', explode( ',', $gallery ) );
			if ( is_array( $imgs ) ) {
				// Multiple images into shortcode
				foreach ( $imgs as $img ) {
					$img_ids[] = $img;
				}
			} else {
				// Only one image into shortcode
				$img_ids[] = $imgs;
			}
		}

		return $img_ids;
	}

	/**
	 * Merge new data with old ones in order to have the same array format :
	 * [ {id} => [ 'type_1', 'type_2', ... ], ... ]
	 *
	 * @param array  $old
	 * @param array  $new
	 * @param string $type
	 *
	 * @since  1.0.0
	 *
	 * @author Maxime CULEA
	 *
	 * @return mixed
	 */
	public static function merge_old_with_new( $old, $new, $type ) {
		if ( empty( $new ) ) {
			return $old;
		}

		foreach ( $new as $media_id ) {
			// TODO : check if media really exists in DB

			// Not already existing into the old array, then create the row with type
			if ( ! isset( $old[ $media_id ] ) ) {
				$old[ $media_id ] = [ $type ];
				continue;
			}

			// Current type already exists into old array for the given media id
			if ( in_array( $type, $old[ $media_id ] ) ) {
				continue;
			}

			// Finally add the current type for the media id
			$old[ $media_id ][] = $type;
		}

		return $old;
	}
}