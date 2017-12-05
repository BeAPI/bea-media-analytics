<?php namespace BEA\Find_Media\Helper;

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
		 * Match all wp-image-{media_id} from img html classes
		 * @see : https://regex101.com/r/63ILkx/1
		 */
		preg_match_all( '/href="([^"\\\']+)"/', $text, $urls );
		if ( empty( $urls ) ) {
			return $img_ids;
		}

		global $wpdb;
		foreach ( $urls[1] as $url ) {
			// Check if retrieved media from href really exists for the current site
			$attachment_id = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $url ) );
			if ( empty( $attachment_id ) ) {
				continue;
			}
			$img_ids[] = (int) $attachment_id[0];
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