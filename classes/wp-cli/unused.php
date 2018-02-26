<?php namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Helper\API;

class Unused extends \WP_CLI_Command {

	/**
	 * Work with unused media
	 *
	 * ##
	 * <action> : Action to be launched. Could be list or delete.
	 *
	 * ## EXAMPLES
	 * wp bea_media_analytics unused <action> --url=
	 *
	 * @since  future
	 * @author Maxime CULEA
	 *
	 * @synopsis
	 */
	function unused( $args ) {
		list( $action ) = $args;
		if ( empty( $action ) ) {
			\WP_CLI::error( "No action provided ! Choose between 'list' or 'deleted'. \n Usage : wp bea_media_analytics unused <action>" );
		}

		switch ( $action ) {
			case 'list' :
				$this->list();
				break;
			case 'delete' :
				$this->delete();
				break;
		}
	}

	/**
	 * Handle wp cli to list unused media
	 *
	 * @since future
	 *
	 * @author Maxime CULEA
	 */
	private function list() {
		$table  = [];
		$medias = API::get_unused_media();
		if ( ! empty( $medias ) ) {
			foreach ( $medias as $media_id ) {
				$table[] = [
					'blog_id'     => get_current_blog_id(),
					'media_id'    => $media_id,
					'media_title' => get_the_title( $media_id ),
				];
			}
		}

		if ( ! empty( $table ) ) {
			\WP_CLI\Utils\format_items( 'table', $table, [ 'blog_id', 'media_id', 'media_title' ] );
		} else {
			\WP_CLI::error( "wp bea_media_analytics unused list : All media are used." );
		}
	}

	/**
	 * Handle wp cli to delete unused media
	 *
	 * @since future
	 *
	 * @author Maxime CULEA
	 */
	private function delete() {
		$medias = API::get_unused_media();
		if ( empty( $medias ) ) {
			\WP_CLI::error( "wp bea_media_analytics unused delete : All media are used." );
			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'wp bea_media_analytics unused delete', count( $medias ) );
		foreach ( $medias as $media_id ) {
			\WP_CLI::runcommand( sprintf( 'post delete %d --force', $media_id ) );
			$progress->tick();
		}
		$progress->finish();
	}
}