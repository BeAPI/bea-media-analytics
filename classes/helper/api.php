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
		$medias_query = new \WP_Query( [
			'post_type'           => 'attachment',
			'post_status'         => 'any',
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'bea_media_analytics' => 'unused',
			'nopaging'            => true
		] );

		if ( $medias_query->have_posts() ) {
			return $medias_query->posts;
		}

		return [];
	}
}