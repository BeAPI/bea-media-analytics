<?php namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Admin\Option;
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
	function __invoke() {
		plugins_loaded_bea_media_analytics_plugin();

		$i = 0;

		// Posts
		$contents_q = new \WP_Query( [
			'no_found_rows' => true,
			'nopaging'      => true,
			'post_type'     => 'any',
			'post_status'   => 'any'
		] );

		if ( $contents_q->have_posts() ) {
			$total = count( $contents_q->posts );

			$progress = \WP_CLI\Utils\make_progress_bar( sprintf( 'Loop on posts for blog id %d', get_current_blog_id() ), $total );
			foreach ( $contents_q->posts as $post ) {
				$i ++;

				Post::index_post( $post->ID, $post, true );
				$progress->tick();
			}

			$progress->finish();

		} else {
			\WP_CLI::warning( 'No post to index.' );
		}

		// ACF
		if ( function_exists( 'acf_options_page' ) ) {
			$pages = acf_options_page()->get_pages();

			if ( empty( $pages ) ) {
				\WP_CLI::warning( 'No settings page to index.' );
			} else {
				$total = count( $pages );

				$progress = \WP_CLI\Utils\make_progress_bar( sprintf( 'Loop on settings page for blog id %d', get_current_blog_id() ), $total );
				foreach ( $pages as $page ) {
					$i ++;

					Option::index_page_option( $page['menu_slug'] );

					$progress->tick();
				}

				$progress->finish();
			}
		}

		do_action_ref_array( 'bea.media_analytics.cli.index_site', array( &$i ) );

		\WP_CLI::success( sprintf( '%s indexed contents for blog id : %d !', $i, get_current_blog_id() ) );
	}
}