<?php namespace BEA\Media_Analytics\Addons\Content_Sync_Fusion;

use BEA\Media_Analytics\DB_Table;
use BEA\Media_Analytics\Singleton;

class Main {

	use Singleton;

	protected function init() {
		// Only working for emitter sites
		if ( ! $this->is_emitter() ) {
			return;
		}

		add_filter( 'bea.media_analytics.media.admin_column_title', [ $this, 'admin_column_title' ] );
		add_filter( 'bea.media_analytics.media.modal_view_title', [ $this, 'modal_view_title' ], 20, 2 );
		add_filter( 'bea.media_analytics.media.edit_view_title', [ $this, 'edit_view_title' ], 20, 2 );
		add_filter( 'bea.media_analytics.media.edit_view_html', [ $this, 'edit_view_html' ], 20, 2 );
		add_filter( 'bea.media_analytics.main.localize_scripts', [ $this, 'localize_scripts' ] );

		add_filter( 'bea.media_analytics.db.get_counter', [ $this, 'get_counter' ], 20, 2 );
		add_filter( 'bea.media_analytics.db.get_data', [ $this, 'get_data' ], 20, 2 );
		// Filter array values for MS
		remove_filter( 'bea.media_analytics.db.get_data', [
			'BEA\Media_Analytics\Main',
			'format_indexed_values'
		], 100 );
		add_filter( 'bea.media_analytics.db.get_data', [
			'BEA\Media_Analytics\Main',
			'format_indexed_values_ms'
		], 120 );
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
		return _x( 'Usage (CSF)', 'Admin column name for CSF', 'bea-media-analytics' );
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
			$_counter = (int) $db_table->db->get_var( $db_table->db->prepare( "SELECT count(id) FROM " . $table_name . " WHERE blog_id = %d AND media_id = %d", $receiver_blog_id, $receiver_media_id ) );
			if ( 0 === $_counter ) {
				continue;
			}
			$counter += $_counter;
		}

		return $counter;
	}

	/**
	 * Change the modal view's title for CSF
	 *
	 * @param string $label
	 * @param int    $counter
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function modal_view_title( $label, $counter ) {
		if ( 1 === $counter ) {
			$label = __( 'One single time across all synchronized sites.', 'bea-media-analytics' );
		} else {
			$label = sprintf( __( '%s times across all synchronized sites.', 'bea-media-analytics' ), esc_html( $counter ) );
		}

		return $label;
	}

	/**
	 * Change the edit view's title for CSF
	 *
	 * @param string $title
	 * @param int    $counter
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function edit_view_title( $title, $counter ) {
		if ( 0 === $counter ) {
			$title = __( 'This media is not used.', 'bea-media-analytics' );
		} elseif ( 1 == $counter ) {
			$title = __( 'This media is used once across all synchronized sites :', 'bea-media-analytics' );
		} else {
			$title = sprintf( __( 'This media is used %s times across all synchronized sites :', 'bea-media-analytics' ), $counter );
		}

		return $title;
	}

	/**
	 * Get all medias data from emitter and receivers sites :
	 * - the emitter usages is the data value
	 * - the receivers usages is what is added on
	 * Final array is gathered by blog_id.
	 *
	 * @param $data
	 * @param $media_id
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function get_data( $data, $media_id ) {
		$db_table = DB_Table::get_instance();
		if ( ! $db_table->table_exists() ) {
			return $data;
		}

		$emitter_blog_id = get_current_blog_id();

		// Get attachment syncs from current blog_id
		$syncs = Helper::get_syncronizations( $emitter_blog_id );
		if ( empty( $syncs ) ) {
			return $data;
		}

		// Get receivers blogs ids from synchronizations
		$receivers_blogs_ids = Helper::get_receivers_blogs_ids( $syncs );
		if ( empty( $receivers_blogs_ids ) ) {
			// No receivers
			return $data;
		}

		$data_by_blog = [];
		if ( ! empty( $data ) ) {
			$data_by_blog[ $emitter_blog_id ] = $data;
		}
		$table_name = DB_Table::get_instance()->get_table_name();
		foreach ( $receivers_blogs_ids as $receiver_blog_id ) {
			// Get the receiver media id
			$receiver_media_id = Helper::get_receiver_obj_id_from_emitter_obj_id( $emitter_blog_id, $receiver_blog_id, $media_id );
			// Get receiver media id usages
			$_data = $db_table->db->get_results( $db_table->db->prepare( "SELECT * FROM " . $table_name . " WHERE blog_id = %d AND media_id = %d", $receiver_blog_id, $receiver_media_id ) );
			if ( empty( $_data ) ) {
				continue;
			}
			$data_by_blog[ $receiver_blog_id ] = $_data;
		}

		return $data_by_blog;
	}

	/**
	 * Change the media edit modal html for display each site details
	 *
	 * @param string $html
	 * @param array  $data
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return string
	 */
	public function edit_view_html( $html, $data ) {
		if ( empty( $data ) ) {
			// Fake content to be empty
			$html = ' ';
		} else {
			$html = '<ul>';
			foreach ( $data as $blog_id => $blog_data ) {
				switch_to_blog( $blog_id );
				$html .= sprintf( '<li><a href="%s" target="_blank">%s</a></li><ul>', get_admin_url( $blog_id ), sprintf( _x( 'On site : %s', 'Each site details for media usage', 'bea-media-analytics' ), get_option( 'blogname' ) ) );
				foreach ( $blog_data as $object_type => $obj ) {
					foreach ( $obj as $media_id => $media ) {
						foreach ( $media as $content_id => $types ) {
							$_types = array_map( [ 'BEA\Media_Analytics\Helpers', 'humanize_object_type' ], $types );
							$html   .= sprintf( '<li><a href="%s" target="_blank">%s</a> : %s</li>', get_edit_post_link( $content_id ), get_the_title( $content_id ), implode( ', ', $_types ) );
						}
					}
				}
				restore_current_blog();
				$html .= '</ul>';
			}
			$html .= '</ul>';
		}

		return $html;
	}

	/**
	 * Overwrite i18n strings for CSF context
	 *
	 * @param array $strings
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return mixed
	 */
	public function localize_scripts( $strings ) {
		$strings['i18n']['warning_confirm'] = _x( "This media is currently used %s across all synchronized sites. Are you sure you want to delete it ?\nThis action is irreversible !\n«Cancel» to stop, «OK» to delete.", 'Popup for confirmation media delete for CSF. %s will display the number with the singular / plural string (time/times).', 'bea-media-analytics' );

		return $strings;
	}
}