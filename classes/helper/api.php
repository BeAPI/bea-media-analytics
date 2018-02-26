<?php namespace BEA\Media_Analytics\Helper;

use BEA\Media_Analytics\DB;

class API {

	/**
	 * Get unused media
	 *
	 * @since future
	 *
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function get_unused_media() {
		$_medias = [];
		$medias = new \WP_Query( [
			'post_type'     => 'attachment',
			'post_status'   => 'any',
			'no_found_rows' => true,
			'fields'        => 'ids',
			'nopaging'      => true
		] );
		if ( $medias->have_posts() ) {
			foreach ( $medias->posts as $media_id ) {
				if ( ! empty( DB::get_data( $media_id ) ) ) {
					continue;
				}

				$_medias[] = $media_id;
			}
		}

		return $_medias;
	}
}