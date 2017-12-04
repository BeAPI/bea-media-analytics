<?php namespace BEA\Find_Media\API;

use BEA\Find_Media\DB;
use BEA\Find_Media\Singleton;

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
		$response[ 'bea_find_media_counter' ] = (string) DB::get_counter( $response['id'] );

		return $response;
	}
}