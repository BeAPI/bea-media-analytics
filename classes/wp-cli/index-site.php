<?php namespace BEA\Find_Media\WP_Cli;

class Index_Site extends \WP_CLI_Command {

	/**
	 * Force core db upgrade on all networks
	 *
	 * ## EXAMPLES
	 * wp bea_find_media index_site --url=
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @synopsis
	 */
	function index_site() {
		plugins_loaded_bea_find_media_plugin();

		$contents_q = new \WP_Query( [
			'no_found_rows'  => true,
			'nopaging'       => true,
			'fields'         => 'ids',
			'posts_per_page' => '-1',
			'post_type'      => 'any'
		] );
		if ( ! $contents_q->have_posts() ) {
			\WP_CLI::error( sprintf( 'No content to index.' ) );
			return;
		}

		\WP_CLI::log( sprintf( 'Starting indexing blog id %s.', get_current_blog_id() ) );
		\WP_CLI::runcommand( sprintf( 'post update %s --field', implode( ' ', $contents_q->posts ) ) );

		\WP_CLI::success( sprintf( '%s indexed contents for blog id : %s !', count( $contents_q->posts ), get_current_blog_id() ) );
	}
}