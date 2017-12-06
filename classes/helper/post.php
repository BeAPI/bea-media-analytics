<?php namespace BEA\Find_Media\Helper;

use BEA\Find_Media\Singleton;

class Post extends Helper {

	use Singleton;

	private $_acf_fields = array();

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
		// ACF is installed and enabled ?
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		$new_post = get_post( $post_id );
		if ( false === $new_post || is_wp_error( $new_post ) ) {
			return [];
		}

		if ( 'attachment' === $new_post->post_type ) {
			// get field groups
			$groups = acf_get_field_groups( array( 'attachment' => $new_post->ID ) );
		} else {
			$groups = acf_get_field_groups( array( 'post_type' => $new_post->post_type ) );
		}

		// No groups, no fields...
		if ( empty( $groups ) ) {
			return [];
		}

		$fields = array();
		foreach ( $groups as $group ) {
			$fields += (array) acf_get_fields( $group );
		}

		// Get only fields with medias
		$this->_acf_fields = array();
		$this->prepare_acf_fields( $fields );

		// Loop on each field
		$found_medias = array();
		foreach ( (array) $this->_acf_fields as $field ) {
			$value = get_post_meta( $post_id, $field['name'], true );
			if ( empty( $value ) ) {
				continue;
			}

			$found_medias = array_merge( (array) $value, $found_medias );
		}

		// Keep only valid ID
		$found_medias = array_map( 'intval', $found_medias );

		// Remove Zero values
		return array_filter( $found_medias );
	}

	/**
	 * Extract from group fields only ACF field with ID database reference (recursive !)
	 *
	 * @param array $fields
	 */
	private function prepare_acf_fields( $fields ) {
		foreach ( (array) $fields as $field ) {
			if ( in_array( $field['type'], array( 'flexible_content' ) ) ) { // Flexible is recursive structure with layouts
				foreach ( $field['layouts'] as $layout_field ) {
					$this->prepare_acf_fields( $layout_field['sub_fields'] );
				}
			} elseif ( in_array( $field['type'], array( 'repeater' ) ) ) { // Repeater is recursive structure
				$this->prepare_acf_fields( $field['sub_fields'] );
			} elseif ( in_array( $field['type'], array(
				'image',
				'gallery',
				'post_object',
				'relationship',
				'file',
				'page_link'
			) ) ) {
				$this->_acf_fields[ $field['key'] ] = $field;
			}
		}
	}
}