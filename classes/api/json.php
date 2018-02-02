<?php namespace BEA\Media_Analytics\API;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Singleton;

class Json {
	use Singleton;

	protected function init() {
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'wp_prepare_attachment_for_js' ], 20, 3 );
	}

	/**
	 * Add to the media json response, custom fields
	 *
	 * @param $response
	 * @param $attachment
	 * @param $meta
	 *
	 * @author Maxime CULEA
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		// Add media
		$response[ 'bea_media_analytics_counter' ] = (string) DB::get_counter( $response['id'] );
		return $response;
	}
}