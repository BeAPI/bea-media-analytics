<?php namespace BEA\Find_Media\Helper;

class Helper {
	/**
	 * From a text, get the inserted html image ids
	 *
	 * @param $text
	 *
	 * @author Maxime CULEA
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	static function get_media_from_text( $text ) {
		if ( empty( $text ) ) {
			return [];
		}

		preg_match_all( '/wp-image-(\d)/', $text, $images );
		if ( empty( $images ) ) {
			return [];
		}

		return $images[1];
	}

	public static function merge_old_with_new( $old, $new, $type ) {
		// check if image exists on merge
	}
}