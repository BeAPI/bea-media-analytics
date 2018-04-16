<?php namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Helper\API;

class Unused extends \WP_CLI_Command {
	/**
	 * Clear the WP object cache after this many regenerations/imports.
	 *
	 * @var integer
	 */
	const WP_CLEAR_OBJECT_CACHE_INTERVAL = 500;

	/**
	 * Handle wp cli to list unused media
	 * Func could be name enumerate, but sub-command is registered against list
	 *
	 * ## EXAMPLES
	 * wp bea_media_analytics unused list --url=
	 *
	 * @since  2.1.0
	 * @author Maxime CULEA
	 *
	 * @synopsis
	 */

	public function enumerate() {
		$table  = [];
		$medias = API::get_unused_media();
		if ( ! empty( $medias ) ) {
			$i = 0;
			foreach ( $medias as $media_id ) {
				$i ++;
				if ( 0 === $i % self::WP_CLEAR_OBJECT_CACHE_INTERVAL ) {
					\WP_CLI\Utils\wp_clear_object_cache();
				}

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
	 * ## EXAMPLES
	 * wp bea_media_analytics unused delete --url=
	 *
	 * @since  2.1.0
	 * @author Maxime CULEA
	 *
	 * @synopsis
	 */
	public function delete() {
		$medias = API::get_unused_media();
		if ( empty( $medias ) ) {
			\WP_CLI::error( "wp bea_media_analytics unused delete : All media are used." );

			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( sprintf( 'Deleting unused media on blog_id : %s', get_current_blog_id() ), count( $medias ) );
		$i        = 0;
		foreach ( $medias as $media_id ) {
			$i ++;
			if ( 0 === $i % self::WP_CLEAR_OBJECT_CACHE_INTERVAL ) {
				\WP_CLI\Utils\wp_clear_object_cache();
			}

			wp_delete_attachment( $media_id, true );
			$progress->tick();
		}
		$progress->finish();
	}
}