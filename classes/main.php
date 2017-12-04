<?php namespace BEA\Find_Media;

class Main {
	use Singleton;

	protected function init() {
		add_filter( 'bea.find_media.db.get_data', [ $this, 'format_indexed_values' ], 100 );
	}

	/**
	 * @param $data
	 */
	public function format_indexed_values( $data ) {
		if ( empty( $data ) ) {
			return [];
		}

		$out = [];
		foreach ( $data as $_d ) {
			if ( ! isset( $out[$_d->object_type][$_d->object_id][$_d->media_id] ) || ! in_array( $_d->type, $out[$_d->object_type][$_d->object_id][$_d->media_id] ) ) {
				$out[$_d->object_type][$_d->object_id][$_d->media_id] = [$_d->type];
			}
		}

		return $out;
	}
}