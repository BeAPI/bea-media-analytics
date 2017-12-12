<?php namespace BEA\Find_Media;

class Plugin {
	use Singleton;

	public static function activate() {
		DB_Table::get_instance()->upgrade_database();

		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );

		// Index all content with a cron
		wp_schedule_single_event( time() + 60, 'bea.find_media.cron.force_indexation' );
	}

	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
	}
}