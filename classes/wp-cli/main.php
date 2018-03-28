<?php Namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Singleton;

class Main {

	use Singleton;

	protected function init() {
		if ( ! defined( 'WP_CLI' ) ) {
			return;
		}

		\WP_CLI::add_command( 'bea_media_analytics index_site', [ 'BEA\Media_Analytics\WP_Cli\Index_Site', 'index_site' ] );
		\WP_CLI::add_command( 'bea_media_analytics unused list', [ 'BEA\Media_Analytics\WP_Cli\Unused', 'enumerate' ] );
		\WP_CLI::add_command( 'bea_media_analytics unused delete', [ 'BEA\Media_Analytics\WP_Cli\Unused', 'delete'] );
	}
}
