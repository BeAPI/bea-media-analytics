<?php Namespace BEA\Media_Analytics\WP_Cli;

use BEA\Media_Analytics\Singleton;

class Main {

	use Singleton;

	protected function init() {
		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'bea_media_analytics', 'BEA\Media_Analytics\WP_Cli\Index_Site' );
		}
	}
}