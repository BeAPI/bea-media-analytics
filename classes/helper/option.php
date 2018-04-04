<?php namespace BEA\Media_Analytics\Helper;

use BEA\Media_Analytics\Singleton;

class Option extends Helper {

	use Singleton;

	/**
	 * Parse post's ACF fields to get media ids
	 *
	 * @param string $page_menu_slug
	 *
	 * @author Amaury BALMER
	 * @since  2.1.0
	 *
	 * @return array Media ids
	 */
	public function get_media_from_acf_fields( $page_menu_slug ) {
		// ACF PRO is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		// get field groups
		$field_groups = acf_get_field_groups( array(
			'options_page' => $page_menu_slug
		) );

		$fields = array();
		foreach ( $field_groups as $group ) {
			$_fields = (array) acf_get_fields( $group );
			foreach ( $_fields as $_field ) {
				$fields[] = $_field;
			}
		}

		if ( empty( $fields ) ) {
			return [];
		}

		// Get only fields with medias
		$this->_acf_object_fields  = array();
		$this->_acf_textual_fields = array();
		$this->_found_medias       = array();

		// Get media possible fields
		$this->recursive_get_post_media_fields( $fields );

		// Use media fields to get media ids
		$this->recursive_get_post_medias( get_fields( 'options', false ) );

		// Keep only valid ID && remove zero values
		return array_filter( array_map( 'intval', $this->_found_medias ) );
	}
}