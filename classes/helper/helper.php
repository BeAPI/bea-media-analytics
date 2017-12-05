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

	/**
	 * Get from the db type an understandable label for display purpose
	 *
	 * @param $type
	 *
	 * @since 1.0.0
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public static function humanize_object_type( $type ) {
		switch ( $type ) {
			case 'post_content' :
				$label = _x( 'Post content', 'Label for humanizing object types', 'bea-find-media' );
			break;
			case 'post_thumbnail' :
				$label = _x( 'Post thumbnail', 'Label for humanizing object types', 'bea-find-media' );
			break;
			default :
				$label = '';
			break;
		}

		return $label;
	}
}