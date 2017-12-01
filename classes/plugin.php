<?php namespace BEA\Find_Media;

class Plugin {
	use Singleton;

	public static function activate() {
		DB_Table::get_instance()->upgrade_database();

		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );
	}

	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
	}
}