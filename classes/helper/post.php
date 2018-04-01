<?php namespace BEA\Media_Analytics\Helper;

use BEA\Media_Analytics\Singleton;

class Post extends Helper {

	use Singleton;

	/**
	 * Parse post's ACF fields to get media ids
	 *
	 * @param int $post_id
	 *
	 * @author Amaury BALMER
	 * @since  1.0.0
	 *
	 * @return array Media ids
	 */
	public function get_media_from_acf_fields( $post_id ) {
		// ACF PRO is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		$new_post = get_post( $post_id );
		if ( false === $new_post || is_wp_error( $new_post ) ) {
			return [];
		}

		// Get only fields with medias
		$this->_acf_object_fields  = array();
		$this->_acf_textual_fields = array();
		$this->_found_medias       = array();

		// Get media possible fields
		$this->recursive_get_post_media_fields( get_field_objects( $post_id ) );

		// Use media fields to get media ids
		$this->recursive_get_post_medias( get_fields( $post_id, false ) );

		// Keep only valid ID && remove zero values
		return array_filter( array_map( 'intval', $this->_found_medias ) );
	}
}