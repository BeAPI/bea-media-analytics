<?php namespace BEA\Media_Analytics\Helper;

use BEA\Media_Analytics\Singleton;

class Option extends Helper {

	use Singleton;

	/**
	 * All kind of field that involve object
	 *
	 * @var array
	 */
	private $_acf_object_fields = array();

	/**
	 * All kind of field that involve textual fields
	 *
	 * @var array
	 */
	private $_acf_textual_fields = array();

	/**
	 * Retrieved medias from ACF fields
	 *
	 * @var array
	 */
	private $_found_medias = array();

	/**
	 * Parse post's ACF fields to get media ids
	 *
	 * @param string $page_menu_slug
	 *
	 * @author Amaury BALMER
	 * @since  future
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

	/**
	 * Recursive way to extract all possible fields for a post
	 * TODO : Maybe better save this fields somewhere (one time)
	 *
	 * @since  future
	 *
	 * @author Amaury Balmer
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_media_fields( $fields ) {
		if ( empty( $fields ) ) {
			return;
		}

		foreach ( (array) $fields as $field ) {
			if ( in_array( $field['type'], array( 'flexible_content' ) ) ) {
				// Flexible is recursive structure with sub_fields into layouts
				foreach ( $field['layouts'] as $layout_field ) {
					$this->recursive_get_post_media_fields( $layout_field['sub_fields'] );
				}
			} elseif ( in_array( $field['type'], [
				'repeater',
				'clone',
				'group',
				'component_field',
			] ) ) {
				// Repeater, Clone and Group fields is a recursive structure with sub_fields
				$this->recursive_get_post_media_fields( $field['sub_fields'] );
			} elseif ( in_array( $field['type'], [
				'image',
				'gallery',
				'post_object',
				'relationship',
				'file',
				'page_link',
			] ) ) {
				// All type of ACF Fields which involve media as object
				$this->_acf_object_fields[ $field['key'] ] = $field['name'];
			} elseif ( in_array( $field['type'], [
				'wysiwyg',
				'textarea',
			] ) ) {
				// All type of ACF Fields which are textual
				$this->_acf_textual_fields[ $field['key'] ] = $field['name'];
			}
		}
	}

	/**
	 * From media fields, get media ids
	 *
	 * @since  future
	 *
	 * @author Amaury Balmer
	 *
	 * @param array $fields
	 */
	private function recursive_get_post_medias( $fields ) {
		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $key => $field ) {
			if ( is_array( $field ) ) {
				// If not final key => field, recursively relaunch
				$this->recursive_get_post_medias( $field );
			}

			if ( empty( $field ) || is_array( $field ) ) {
				// Go to next one if empty, array (already recursively relaunched) and the key is not a media field
				continue;
			}

			// Save the media ID
			if ( in_array( $key, $this->_acf_object_fields ) || isset( $this->_acf_object_fields[ $key ] ) ) {
				$this->_found_medias = array_merge( $this->_found_medias, (array) $field );
			} elseif ( in_array( $key, $this->_acf_textual_fields ) || isset( $this->_acf_textual_fields[ $key ] ) ) {
				$this->_found_medias = array_merge( $this->_found_medias, Post::get_media_from_text( $field ) );
			}
		}
	}
}