<?php namespace BEA\Media_Analytics\Admin;

class Admin_Media_List_Table extends \WP_List_Table {

	// TODO : to be implemented !!

	function get_columns() {
		$columns = [
			'type'        => 'Type',
			'object_id'   => 'Object ID',
			'object_type' => 'Object Type'
		];

		return $columns;
	}

	function saveFields( $post, $attachment ) {
		return false;
	}
}