<?php namespace BEA\Find_Media;

class Main {
	use Singleton;

	protected function init() {
		add_filter( 'bea.find_media.db.get_data', [ $this, 'format_indexed_values' ], 100 );
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
				continue;
			}
			$out[ $_d->object_type ][ $_d->media_id ][ $_d->object_id ][] = [ $_d->type ];
		}

		return $out;
	}
}