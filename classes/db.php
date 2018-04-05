<?php namespace BEA\Media_Analytics;

class DB {

	use Singleton;

	protected function init() {
		add_action( 'delete_blog', [ $this, 'delete_blog' ] );
	}

	/**
	 * On blog deletion, Manage to delete all data from the blog
	 *
	 * @since  1.0.0
	 *
	 * @author Maxime CULEA
	 *
	 * @param int $blog_id
	 */
	public function delete_blog( $blog_id = 0 ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( $db_table->get_table_name(), [ 'blog_id' => $blog_id ], [ '%d' ] );

		delete_option( 'bea_media_analytics_index' );
		delete_transient( 'bea_media_analytics_activated_notice' );
	}

	/**
	 * Manage to insert into db the given media ids for indexation
	 *
	 * @param $media_ids
	 * @param $object_id
	 * @param $object_type
	 *
	 * @since  1.0.0
	 *
	 * @author Maxime CULEA
	 */
	public static function insert( $media_ids, $object_id, $object_type ) {
		if ( empty( $media_ids ) ) {
			return;
		}

		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		/**
		 * Before insert, delete all data against object_id
		 * To ensure to not store useless data
		 */
		self::delete_all_object_id( $object_id, $object_type );

		$blog_id = get_current_blog_id();
		foreach ( $media_ids as $media_id => $types ) {
			foreach ( $types as $type ) {
				$db_table->db->insert( $db_table->get_table_name(), [
					'blog_id'     => $blog_id,
					'type'        => $type,
					'media_id'    => $media_id,
					'object_id'   => $object_id,
					'object_type' => $object_type,
				], [ '%d', '%s', '%d', '%s', '%s' ] );
			}
		}
	}

	/**
	 * Manage to delete all data against an object id
	 *
	 * @param $object_id
	 * @param $object_type
	 *
	 * @since  1.0.0
	 *
	 * @author Maxime CULEA
	 */
	public static function delete_all_object_id( $object_id, $object_type ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( DB_Table::get_instance()->get_table_name(), [
			'blog_id'     => get_current_blog_id(),
			'object_id'   => $object_id,
			'object_type' => $object_type,
		], [ '%d', '%s', '%s' ] );
	}

	/**
	 * Manage to delete all data against a media id
	 *
	 * @param int $media_id
	 *
	 * @since  1.0.0
	 *
	 * @author Maxime CULEA
	 */
	public static function delete_all_media_id( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return;
		}

		$db_table->db->delete( DB_Table::get_instance()->get_table_name(), [
			'blog_id'  => get_current_blog_id(),
			'media_id' => $media_id,
		], [ '%d', '%d' ] );
	}

	/**
	 * Get the counter for a given media id
	 *
	 * @param int $media_id
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return int
	 */
	public static function get_counter( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return 0;
		}

		$counter = (int) $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . DB_Table::get_instance()->get_table_name() . " WHERE blog_id = %d AND media_id = %d", get_current_blog_id(), $media_id ) );

		/**
		 * Filter the media's counter for a third party add-on, for example CSF.
		 *
		 * @since 1.0.0
		 *
		 * @param int $counter  How many times used.
		 * @param int $media_id Media ID looking for.
		 */
		return apply_filters( 'bea.media_analytics.db.get_counter', $counter, $media_id );
	}

	/**
	 * Get all indexed data against a media
	 *
	 * @param int $media_id
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function get_data( $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return [];
		}

		$data = $db_table->db->get_results( $db_table->db->prepare( "SELECT * FROM " . DB_Table::get_instance()->get_table_name() . " WHERE blog_id = %d AND media_id = %d", get_current_blog_id(), $media_id ) );

		/**
		 * Filter saved indexed data against the given media
		 *
		 * @since 1.0.0
		 *
		 * @param array $data     The indexed data, reordoned.
		 * @param int   $media_id Media ID looking for.
		 */
		return apply_filters( 'bea.media_analytics.db.get_data', $data, $media_id );
	}

	/**
	 * Check if a data exists into db
	 *
	 * @param string $type
	 * @param int    $media_id
	 * @param int    $object_id
	 * @param string $object_type
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return bool
	 */
	public static function exists( $type, $media_id, $object_id, $object_type ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return false;
		}

		// Check if raw exists for insert
		$column_exists = $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . $db_table->get_table_name() . " WHERE blog_id = %d AND type = %s AND media_id = %d AND object_id = %s AND object_type = %s", get_current_blog_id(), $type, $media_id, $object_id, $object_type ) );

		return ! empty( $column_exists );
	}
}