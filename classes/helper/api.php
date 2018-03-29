<?php namespace BEA\Media_Analytics\Helper;

class API {

	/**
	 * Get unused media
	 *
	 * @since  1.0.0
	 * @update future : improve performande with custom SQL query based on the bea_media_analytics arg
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
			'nopaging'            => true,
		] );

		return $medias_query->have_posts() ? $medias_query->posts : [];
	}
}