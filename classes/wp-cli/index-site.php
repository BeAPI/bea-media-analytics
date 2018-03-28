<?php namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Admin\Post;

class Index_Site extends \WP_CLI_Command {

	/**
	 * Force core db upgrade on all networks
	 *
	 * ## EXAMPLES
	 * wp bea_media_analytics index_site --url=
	 *
	 * @since  1.0.0
	 * @author Maxime CULEA
	 *
	 * @synopsis
	 */
	function index_site() {
		plugins_loaded_bea_media_analytics_plugin();

		$contents_q = new \WP_Query( [
			'no_found_rows' => true,
			'nopaging'      => true,
			'post_type'     => 'any',
			'post_status'   => 'any'
		] );
		if ( ! $contents_q->have_posts() ) {
			\WP_CLI::error( sprintf( 'No content to index.' ) );

			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( sprintf( 'Indexing %s contents for blog_id %s', $contents_q->post_count, get_current_blog_id() ), $contents_q->post_count );
		foreach ( $contents_q->posts as $post ) {
			Post::index_post( $post->ID, $post, true );
			$progress->tick();
		}

		$progress->finish();
	}
}
