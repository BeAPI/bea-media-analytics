<?php namespace BEA\Find_Media\Addons\Content_Sync_Fusion;

use BEA\Find_Media\DB_Table;
use BEA\Find_Media\Singleton;

class Main {

	use Singleton;

	protected function init() {
		// Only working for emitter sites
		if ( ! $this->is_emitter() ) {
			return;
		}

		add_filter( 'bea.find_media.media.admin_column_title', [ $this, 'admin_column_title' ] );
		add_filter( 'bea.find_media.media.modal_view_title', [ $this, 'modal_view_title' ], 20, 2 );

		add_filter( 'bea.find_media.db.get_counter', [ $this, 'get_counter' ], 20, 2 );
	}

	/**
	 * Check if current site is an emitter one
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return bool
	 */
	public function is_emitter() {
		return ! empty( Helper::get_syncronizations() );
	}

	/**
	 * Change the title for CSF
	 *
	 * @param string $title
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function admin_column_title( $title ) {
		return _x( 'Usage (CSF)', 'Admin column name for CSF', 'bea-find-media' );
	}

	/**
	 * Get all medias usage from emitter and receivers sites :
	 * - the emitter usages is the counter value
	 * - the receivers usages is what is added on
	 *
	 * @param int $counter
	 * @param int $media_id
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function get_counter( $counter, $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return $counter;
		}

		$emitter_blog_id = get_current_blog_id();

		// Get attachment syncs from current blog_id
		$syncs = Helper::get_syncronizations( $emitter_blog_id );
		if ( empty( $syncs ) ) {
			return $counter;
		}

		// Get receivers blogs ids from synchronizations
		$receivers_blogs_ids = Helper::get_receivers_blogs_ids( $syncs );
		if ( empty( $receivers_blogs_ids ) ) {
			// No receivers
			return $counter;
		}

		$table_name = DB_Table::get_instance()->get_table_name();
		foreach ( $receivers_blogs_ids as $receiver_blog_id ) {
			// Get the receiver media id
			$receiver_media_id = Helper::get_receiver_obj_id_from_emitter_obj_id( $emitter_blog_id, $receiver_blog_id, $media_id );
			// Get receiver media id usages
			$_counter = $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . $table_name . " WHERE blog_id = %d AND media_id = %d", $receiver_blog_id, $receiver_media_id ) );
			if ( empty( $_counter ) ) {
				continue;
			}
			$counter += (int) $_counter ?? 0;
		}

		return $counter;
	}

	/**
	 * Change the modal view's title for CSF
	 *
	 * @param $label
	 * @param $counter
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function modal_view_title( $label, $counter ) {
		if ( 1 === $counter ) {
			$label = __( 'One single time across all synchronized sites.', 'bea-find-media' );
		} else {
			$label = sprintf( __( '%s times across all synchronized sites.', 'bea-find-media' ), esc_html( $counter ) );
		}

		return $label;
	}
}