<?php namespace BEA\Find_Media;

class Main {
	use Singleton;

	protected function init() {
		add_filter( 'bea.find_media.db.get_data', [ $this, 'format_indexed_values' ], 100 );
		add_action( 'init', [ $this, 'init_translations' ] );
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
	 * @since 1.0.O
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function format_indexed_values( $data ) {
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

	public function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-find-media', false, BEA_FIND_MEDIA_PLUGIN_DIRNAME . '/languages' );
	}
}