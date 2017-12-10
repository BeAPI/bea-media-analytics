<?php namespace BEA\Find_Media\Addons\Content_Sync_Fusion;

use BEA\Find_Media\Singleton;

class Main {

	use Singleton;

	protected function init() {
		// Only working for emitter sites
		if ( ! $this->is_emitter() ) {
			return;
		}

		add_filter( 'bea.find_media.media.admin_column_title', [ $this, 'admin_column_title' ] );
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
		// Get syncs for current post_type and mode set to "auto"
		$is_emitter = \BEA_CSF_Synchronizations::get( [
			'post_type' => 'attachment',
			'emitters'  => get_current_blog_id(),
		], 'AND', false, true );

		return ! empty( $is_emitter );
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
}