<?php namespace BEA\Media_Analytics;

class Upgrader {
	use Singleton;

	protected function init() {
		//add_action( 'upgrader_process_complete', [ $this, 'plugin_updated_actions' ], 10, 2 );
	}

	/**
	 * On plugin update, launch custom actions as reindexing contents
	 * //TODO : not working, need to check db version against new plugin one, then display message error
	 *
	 * @param $upgrader
	 * @param $options
	 *
	 * @since  2.1.0
	 * @author Maxime CULEA
	 */
	public static function plugin_updated_actions( $upgrader = [], $options = [] ) {
		/*if ( 'plugin' !== $options['type'] || 'update' !== $options['action'] || ! in_array( BEA_MEDIA_ANALYTICS_PLUGIN_DIRNAME, $options['plugins'] ) ) {
			return;
		}*/

		// Update option for forcing cron schedule
		update_option( 'bea_media_analytics_index', false );
		Crons::schedule();

		if ( function_exists( 'dnh_register_notice' ) ) {
			dnh_register_notice( sprintf( 'bea_media_analytics_updated_notice_%s', BEA_MEDIA_ANALYTICS_VERSION ), 'updated', _x( 'As BEA - Media Analytics plugin has been updated, new features are introduced which require to launch the process of indexing all contents. It will silently launch himself soon.', 'Admin notice', 'bea-media-analytics' ) );
		}
	}
}