<?php namespace BEA\Media_Analytics;

class Upgrader {
	use Singleton;

	protected function init() {
		add_action( 'upgrader_process_complete', [ $this, 'plugin_updated_actions' ], 10, 2 );
	}

	/**
	 * On plugin update, launch custom actions as reindexing contents
	 *
	 * @param $upgrader
	 * @param $options
	 *
	 * @since  future
	 * @author Maxime CULEA
	 */
	private function plugin_updated_actions( $upgrader, $options ) {
		if ( 'plugin' !== $options['type'] || 'update' !== $options['action'] || ! in_array( BEA_MEDIA_ANALYTICS_PLUGIN_DIRNAME, $options['plugins'] ) ) {
			return;
		}

		// Update option for forcing cron schedule
		update_option( 'bea_media_analytics_index', false );
		Crons::schedule();

		// Set this transient, for 15min, to allow to show admin notice that indexing will manage soon
		set_transient( 'bma_notice_plugin_updated', true, MINUTE_IN_SECONDS * 15 );
	}
}