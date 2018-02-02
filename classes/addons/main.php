<?php namespace BEA\Media_Analytics\Addons;

use BEA\Media_Analytics\Addons\Content_Sync_Fusion\Main as CSF;
use BEA\Media_Analytics\Singleton;

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