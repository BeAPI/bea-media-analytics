<?php namespace BEA\Media_Analytics;

class Plugin {
	use Singleton;

	protected function init() {
		add_action( 'init', [ $this, 'plugin_admin_notices' ] );
	}

	/**
	 * Plugin activation
	 */
	public static function activate() {
		DB_Table::get_instance()->upgrade_database();

		// For safety, delete existing data
		DB::get_instance()->delete_blog( get_current_blog_id() );

		Crons::schedule();

		set_transient( 'bea_media_analytics_activated_notice', true );
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		DB::get_instance()->delete_blog( get_current_blog_id() );
		Crons::unschedule();
	}

	/**
	 * Set admin notice for plugin update
	 * @since  2.1.1
	 * @author Maxime CULEA
	 */
	public function plugin_admin_notices() {
		if ( ! get_transient( 'bea_media_analytics_activated_notice' ) ) {
			return;
		}
		add_action( 'admin_init', function () {
			dnh_register_notice( sprintf( 'bea_media_analytics_activated_notice_%s', BEA_MEDIA_ANALYTICS_VERSION ), 'updated', _x( 'As BEA - Media Analytics plugin has been activated, the process of indexing contents will silently launch himself soon.', 'Admin notice', 'bea-media-analytics' ) );
		} );
	}
}
