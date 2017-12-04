<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\Singleton;
use BEA\Find_Media\DB;

class Media_Template {
	use Singleton;

	protected function init() {
		add_filter( 'attachment_fields_to_edit', [ $this, 'modal_view' ], 20, 2 );
		add_filter( 'attachment_fields_to_edit', [ $this, 'edit_view' ], 20, 2 );
	}

	/**
	 * Display into media's modal, the number of indexed
	 *
	 * @param $form_fields
	 * @param $media
	 *
	 * @since 1.0.0
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public function modal_view( $form_fields, $media ) {
		$counter = DB::get_counter( $media->ID );
		if ( ! empty( $counter ) ) {
			$html = sprintf( '<a href="%s" type="text" >%s</a>', get_edit_post_link( $media->ID ), esc_html( $counter ) );
		} else {
			$html = '<span>0</span>';
		}
		$form_fields['bea_find_media'] = array(
			'label'         => __( 'Time used', 'bea-find-media' ),
			'input'         => 'html',
			'html'          => $html,
			'show_in_edit'  => false,
			'show_in_modal' => true,
		);

		return $form_fields;
	}

	public function edit_view( $form_fields, $media ) {
		$data = DB::get_data( $media->ID );
		$amlt = new Admin_Media_List_Table();

		ob_start();
		$amlt->display();
		$html = ob_get_clean();

		$form_fields['bea_find_media'] = array(
			'label'         => __( 'Time used', 'bea-find-media' ),
			'input'         => 'html',
			'html'          => $html,
			'show_in_edit'  => true,
			'show_in_modal' => false,
		);

		return $form_fields;
	}
}