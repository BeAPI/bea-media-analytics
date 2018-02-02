<?php namespace BEA\Media_Analytics\API;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Singleton;

class Rest_Api {
	use Singleton;

	protected function init() {
		add_filter( 'rest_api_init', [ $this, 'rest_api_add_custom_fields' ] );
	}

	/**
	 * Register custom fields against attachment post type
	 *
	 * @author Maxime CULEA
	 */
	public function rest_api_add_custom_fields() {
		if ( ! function_exists( 'register_rest_field' ) ) {
			return;
		}

		register_rest_field( [ 'attachment' ], 'bea_media_analytics_counter', [
			'get_callback' => [ $this, 'get_media_counter' ],
			'schema'       => [
				'description' => 'Display the attachment\'s counter for media use.',
				'type'        => 'int',
				'context'     => [ 'view', 'edit' ],
			],
		] );
	}

	/**
	 * Get attachment's counter (use)
	 *
	 * @param $post
	 * @param $field_name
	 * @param $request
	 *
	 * @author Maxime CULEA
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_media_counter( $post, $field_name, $request ) {
		return (string) DB::get_counter( $post['id'] );
	}
}