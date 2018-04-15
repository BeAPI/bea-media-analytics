<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\DB;
use BEA\Media_Analytics\Helpers;

class Admin_Media_List_Table extends \WP_List_Table {


	public function __construct() {
		parent::__construct( [
			'singular' => 'serie',
			'plural'   => 'series',
			'ajax'     => false,
		] );
	}

	public function prepare_items() {
		$this->_column_headers = [ $this->get_columns(), $this->get_sortable_columns() ];
		$this->items           = $data = DB::get_data( get_the_ID() );
	}

	/**
	 *
	 */
	public function no_items() {
		echo __( 'This media is not used.', 'bea-media-analytics' );
	}

	/**
	 *
	 * @return array
	 */
	public function get_columns() {
		$posts_columns = array();

		$posts_columns['object_title'] = 'Object title';
		$posts_columns['object_type']  = 'Object type';

		/**
		 * Filters the columns displayed in the Media list table view.
		 *
		 * @since future
		 *
		 * @param array $post_columns An array of column names.
		 */
		return apply_filters( 'bea.media_analytics.list_table.get_columns', $posts_columns );
	}

	public function single_row( $object ) {
		printf( '<tr id="bma-%s">%s</tr>', $object->id, $this->single_row_columns( $object ) );
	}

	/**
	 * @param $object
	 */
	public function column_object_title( $object ) {
		$title = sprintf( '<a href="%s" target="_blank">%s</a>', get_edit_post_link( $object->object_id ), get_the_title( $object->object_id ) );

		/**
		 * Allow third plugins to change the way title is displayed, mostly depending on object type
		 *
		 * @param string $title
		 * @param object $object
		 *
		 * @since  @future
		 *
		 * @author Maxime CULEA
		 */
		echo apply_filters( 'bea.media_analytics.list_table.column.title', $title, $object );
	}

	/**
	 * @param $object
	 */
	public function column_object_type( $object ) {
		/**
		 * Allow third plugins to change the way type is displayed
		 *
		 * @param string $type
		 * @param object $object
		 *
		 * @since  @future
		 *
		 * @author Maxime CULEA
		 */
		echo apply_filters( 'bea.media_analytics.list_table.column.type', Helpers::humanize_object_type( $object->type ), $object );
	}
}