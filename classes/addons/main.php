<?php namespace BEA\Find_Media\Addons;

use BEA\Find_Media\Addons\Content_Sync_Fusion\Main as CSF;
use BEA\Find_Media\Singleton;

class Main {

	use Singleton;

	protected function init() {
		$this->load_addons();
	}

	private function load_addons() {
		if ( function_exists( 'init_bea_content_sync_fusion' ) ) {
			CSF::get_instance();
		}
	}
}