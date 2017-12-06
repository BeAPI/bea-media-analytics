<?php namespace BEA\Find_Media;

class Helpers {
	/**
	 * Get from the db type an understandable label for display purpose
	 *
	 * @param $type
	 *
	 * @since  1.0.0
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
			case 'acf' :
				$label = _x( 'Advanced Custom Fields', 'Label for humanizing object types', 'bea-find-media' );
				break;
			default :
				$label = '';
				break;
		}

		return $label;
	}


	/**
	 * Check image validity vs DB
	 *
	 * @param int $image_id
	 *
	 * @author Amaury BALMER
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public static function check_image_id( $image_id ) {
		if ( 0 === (int) $image_id ) {
			return false;
		}

		$object = get_post( $image_id );
		if ( false == $object || is_wp_error( $object ) ) {
			return false;
		}

		if ( $object->post_type !== 'attachment' ) {
			return false;
		}

		return true;
	}

	/**
	 * Check given array of image for validation vs DB
	 *
	 * @param array $image_ids
	 *
	 * @author Amaury BALMER
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function check_image_ids( $image_ids ) {
		return array_filter( $image_ids, [ 'BEA\Find_Media\Helpers', 'check_image_id' ], ARRAY_FILTER_USE_KEY );
	}
}