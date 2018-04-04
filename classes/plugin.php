<?php namespace BEA\Media_Analytics;

class Plugin {
	use Singleton;

	/**
	 * Plugin activation
	 */
	public static function activate() {
		DB_Table::get_instance()->upgrade_database();

		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );

		Crons::schedule();

		dnh_register_notice( 'bea_media_analytics_activated_notice', 'updated', _x( 'As BEA - Media Analytics plugin has been activated, the process of indexing contents will silently launch himself soon.', 'Admin notice', 'bea-media-analytics' ) );
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
		Crons::unschedule();
	}
}
