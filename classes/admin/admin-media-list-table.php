<?php namespace BEA\Find_Media\Admin;

class Admin_Media_List_Table extends \WP_List_Table {

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