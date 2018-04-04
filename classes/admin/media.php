<?php namespace BEA\Media_Analytics\Admin;

use BEA\Media_Analytics\Singleton;
use BEA\Media_Analytics\DB;

class Media {
	use Singleton;

	protected function init() {
		// Hooks
		add_action( 'delete_attachment', [ $this, 'delete_attachment' ] );

		// Views
		add_filter( 'attachment_fields_to_edit', [ $this, 'modal_view' ], 20, 2 );
		add_filter( 'attachment_fields_to_edit', [ $this, 'edit_view' ], 20, 2 );

		// Custom admin columns
		add_filter( 'manage_media_columns', [ $this, 'admin_columns_header' ] );
		add_action( 'manage_media_custom_column', [ $this, 'admin_columns_values' ], 10, 2 );
		// TODO : No inline hook + css
		add_action( 'admin_head', function () {
			echo '<style type="text/css">.column-bea-media-analytics-counter { width: 7%; }</style>';
		} );

		// Warning on delete
		add_filter( 'media_row_actions', [ $this, 'delete_from_list_warning' ], 20, 3 );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ], 10 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Display into media's modal, the number of indexed
	 *
	 * @param $form_fields
	 * @param $media
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function modal_view( $form_fields, $media ) {
		$counter = DB::get_counter( $media->ID );
		if ( ! empty( $counter ) ) {
			if ( 1 == $counter ) {
				$label = __( 'One time.', 'bea-media-analytics' );
			} else {
				$label = sprintf( __( '%s times.', 'bea-media-analytics' ), esc_html( $counter ) );
			}
			/**
			 * Filter the title for the modal view
			 *
			 * @since 1.0.1
			 *
			 * @param string $label   Depending on counter, display the single or multiple title.
			 * @param int    $counter The number of usages.
			 */
			$label = apply_filters( 'bea.media_analytics.media.modal_view_title', $label, $counter );
			$html  = sprintf( '<span class="value"><a href="%s" title="%s" style="vertical-align: -webkit-baseline-middle;">%s</a></span>', get_edit_post_link( $media->ID ), _x( 'View media usage.', 'title for the usage link', 'bea-media-analytics' ), $label );
		} else {
			$html = sprintf( '<span class="value">%s</span>', __( 'Not used anywhere.', 'bea-media-analytics' ) );
		}

		$form_fields['bea_media_analytics_view'] = [
			'label'         => __( 'Usage :', 'bea-media-analytics' ),
			'input'         => 'html',
			'html'          => $html,
			'show_in_edit'  => false,
			'show_in_modal' => true,
		];

		return $form_fields;
	}

	/**
	 * Display into media's edit view, the number of indexed
	 *
	 * @param $form_fields
	 * @param $media
	 *
	 * @since   1.0.0
	 * @since   2.1.0 : acf-option case
	 *
	 * @author  Maxime CULEA
	 *
	 * @return array
	 */
	public function edit_view( $form_fields, $media ) {
		$counter = DB::get_counter( $media->ID );
		if ( 0 === $counter ) {
			$title = __( 'This media is not used.', 'bea-media-analytics' );
		} elseif ( 1 == $counter ) {
			$title = __( 'This media is used once :', 'bea-media-analytics' );
		} else {
			$title = sprintf( __( 'This media is used %s times :', 'bea-media-analytics' ), $counter );
		}

		/**
		 * Filter the title for the edit view
		 *
		 * @since 1.0.1
		 *
		 * @param string $title   Depending on counter, display the title.
		 * @param int    $counter The number of usages.
		 */
		$title = apply_filters( 'bea.media_analytics.media.edit_view_title', $title, $counter );

		/**
		 * $amlt = new Admin_Media_List_Table();
		 * ob_start();
		 * $amlt->display();
		 * $html = ob_get_clean();
		 */

		$data = DB::get_data( $media->ID );
		if ( empty( $data ) ) {
			// Fake content to be empty
			$html = ' ';
		} else {
			$html = '<ul>';
			foreach ( $data as $object_type => $obj ) {
				foreach ( $obj as $media_id => $media ) {
					foreach ( $media as $content_id => $types ) {

						$_types = array_map( [ 'BEA\Media_Analytics\Helpers', 'humanize_object_type' ], $types );

						if ( 'acf-option' == $types[0] ) {
							$page = acf_options_page()->get_page( $content_id );
							$html .= sprintf( '<li>%s : %s</li>', $page['page_title'], implode( ', ', $_types ) );
						} elseif ( $content_id > 0 ) {
							$html .= sprintf( '<li><a href="%s" target="_blank">%s</a> : %s</li>', get_edit_post_link( $content_id ), get_the_title( $content_id ), implode( ', ', $_types ) );
						}

						$html = apply_filters( 'bea.media_analytics.media.edit_view_context', $html, $types, $content_id, $media_id );

					}
				}
			}
			$html .= '</ul>';
		}

		/**
		 * Filter the title for the edit view
		 *
		 * @since 1.0.1
		 *
		 * @param string $html The formatted HTML for edit view display.
		 * @param array  $data All DB usages from the current media.
		 */
		$html = apply_filters( 'bea.media_analytics.media.edit_view_html', $html, $data );

		$form_fields['bea_media_analytics_edit'] = [
			'label'         => $title,
			'input'         => 'html',
			'html'          => $html,
			'show_in_edit'  => true,
			'show_in_modal' => false,
		];

		return $form_fields;
	}

	/**
	 * On media delete, remove indexed associated data
	 *
	 * @param int $media_id
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 */
	public function delete_attachment( $media_id ) {
		DB::delete_all_media_id( $media_id );
	}

	/**
	 * Add custom headers for attachment
	 *
	 * @param $headers
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return mixed
	 */
	public function admin_columns_header( $headers ) {
		/**
		 * Filter the admin column title
		 *
		 * @since 1.0.1
		 *
		 * @param string $title
		 */
		$headers['bea-media-analytics-counter'] = apply_filters( 'bea.media_analytics.media.admin_column_title', _x( 'Usage', 'Admin column name', 'bea-media-analytics' ) );

		return $headers;
	}

	/**
	 * Add values to the custom headers
	 *
	 * @param     $column_name
	 * @param int $media_id
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 */
	public function admin_columns_values( $column_name, $media_id ) {
		$counter = '';
		if ( 'bea-media-analytics-counter' === $column_name ) {
			$counter = DB::get_counter( $media_id );
		}

		// Depending on if has value, display the edit link to see them
		if ( empty( $counter ) ) {
			echo '0';
		} else {
			printf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $media_id ) ), esc_html( $counter ) );
		}
	}

	/**
	 * Change the delete action on the fly to launch custom JS event for media delete warning on list view
	 *
	 * @param $actions
	 * @param $media
	 * @param $detached
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return mixed
	 */
	public function delete_from_list_warning( $actions, $media, $detached ) {
		// Not used, then default actions
		if ( empty( DB::get_counter( $media->ID ) ) ) {
			return $actions;
		}

		// Change default one ( return showNotice.warn(); ) with our custom one ( return bea_media_analytics_warn(); )
		$actions['delete'] = str_replace( "onclick='return showNotice.warn();'", "onclick='return bea_media_analytics_warn_list({$media->ID});'", $actions['delete'] );

		return $actions;
	}

	/**
	 * Change the delete action on the fly to launch custom JS event for media delete warning on single view
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @return mixed
	 */
	public function delete_from_single_warning() {
		// Not used, then default actions
		$counter = DB::get_counter( get_the_ID() );
		if ( empty( $counter ) ) {
			return;
		}

		echo "<script type='text/javascript'>(function ($, w) {jQuery('#delete-action a').attr('onclick','return bea_media_analytics_warn_single(" . esc_js( $counter ) . ");');})(jQuery, window);</script>";
	}

	/**
	 * Registers admin scripts
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 */
	public function register_scripts() {
		wp_register_script( 'bea-media-analytics', BEA_MEDIA_ANALYTICS_URL . 'assets/js/bea-media-analytics.js', [ 'jquery' ], BEA_MEDIA_ANALYTICS_VERSION, true );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( is_admin() && 'attachment' === $screen->post_type && in_array( $screen->base, [ 'upload', 'post' ] ) ) {
			wp_enqueue_script( 'bea-media-analytics' );

			if ( 'post' === $screen->base ) {
				add_action( 'admin_footer', [ $this, 'delete_from_single_warning' ] );
			}
		}
	}
}