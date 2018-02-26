<?php namespace BEA\Media_Analytics\WP_Cli;

class Unused extends \WP_CLI_Command {

	/**
	 * Work with unused medias
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
		if ( empty( $args ) || $args['action'] ) {
			\WP_CLI::error( "No action provided ! Choose between 'list' or 'deleted'. \n Usage : wp bea_media_analytics unused <action>" );
		}

		\WP_CLI::success( 'Done' );
	}
}