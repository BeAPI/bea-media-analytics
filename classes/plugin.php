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

		// Set this transient to allow to show admin notice that indexing will manage soon
		set_transient( 'bma_notice_plugin_activated', true, HOUR_IN_SECONDS );
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
		Crons::unschedule();
	}
}
