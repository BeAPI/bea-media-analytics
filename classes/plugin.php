<?php namespace BEA\Media_Analytics;

class Plugin {
	use Singleton;

	function init() {
		add_action( 'upgrader_process_complete', [ $this, 'plugin_updated_actions' ], 10, 2 );
	}

	/**
	 * Plugin activation
	 */
	public static function activate() {
		DB_Table::get_instance()->upgrade_database();

		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );

		Crons::schedule();
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
		Crons::unschedule();
	}

	/**
	 * On plugin update, launch custom actions as reindexing contents
	 *
	 * @param $upgrader_object
	 * @param $options
	 *
	 * @since  future
	 * @author Maxime CULEA
	 */
	private function plugin_updated_actions( $upgrader_object, $options ) {
		if ( 'plugin' !== $options['type'] || 'update' !== $options['action'] || ! in_array( BEA_MEDIA_ANALYTICS_PLUGIN_DIRNAME, $options['plugins'] ) ) {
			return;
		}

		Crons::schedule();
	}
}
