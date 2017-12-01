<?php namespace BEA\Find_Media;

class DB {

	use Singleton;

	protected function init() {
		add_action( 'delete_blog', [ $this, 'delete_blog' ] );
	}

	public function delete_blog( $blog_id = 0 ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( $db_table->get_table_name(), [ 'blog_id' => $blog_id ], [ '%d' ] );
	}

	public static function insert( $media_ids, $object_id, $object_type ) {
		if ( empty( $media_ids ) ) {
			return;
		}

		global $wpdb;
		$db_tables = DB_Table::get_instance();

		foreach ( $media_ids as $media_id => $types ) {
			foreach ( $types as $type ) {
				// Check if raw exists for insert
				$column_exists = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM " . $db_tables->get_table_name() . " WHERE type = %s AND media_id = %d AND object_id = %d AND object_type = %s", $type, $media_id, $object_id, $object_type ) );
				if ( ! empty( $column_exists ) ) {
					continue;
				}

				$wpdb->insert( $db_tables->get_table_name(), [
					'type'        => $type,
					'media_id'    => $media_id,
					'object_id'   => $object_id,
					'object_type' => $object_type,
				] );
			}
		}
	}

	public static function delete_all( $object_id, $object_type ) {
		global $wpdb;

		$wpdb->delete( DB_Table::get_instance()->get_table_name(), [ 'object_id' => $object_id, 'object_type' => $object_type, ], [ '%d', '%s' ] );
	}
}