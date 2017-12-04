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
		$contents_q = new \WP_Query( [
			'no_found_rows'  => true,
			'nopaging'       => true,
			'posts_per_page' => '-1',
		] );
		if ( ! $contents_q->have_posts() ) {
			\WP_CLI::error( sprintf( 'No content to index.' ) );
			return;
		}

		\WP_CLI::log( sprintf( 'Starting indexing blog id %s.', get_current_blog_id() ) );
		foreach ( $contents_q->posts as $post ) {
			// TODO : improve by auto calling itself `wp post update {id} --url={url}`
			wp_update_post( $post );
			\WP_CLI::log( sprintf( 'Updating post : %s', esc_html( $post->post_title ) ) );
		}

		\WP_CLI::success( sprintf( '%s indexed contents for %s blog id !', $contents_q->found_posts, get_current_blog_id() ) );
	}
}