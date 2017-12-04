<?php Namespace BEA\Find_Media\WP_Cli;

use BEA\Find_Media\Singleton;

class Main {

	use Singleton;

	protected function init() {
		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'bea_find_media', 'BEA\Find_Media\WP_Cli\Index_Site' );
		}
	}
}