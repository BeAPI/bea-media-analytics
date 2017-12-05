<?php namespace BEA\Find_Media;

class Helpers{
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
}