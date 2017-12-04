<?php namespace BEA\Find_Media;
class Compatibility {
	/**
	 * admin_init hook callback
	 *
	 * @since 0.1
	 */
	public static function admin_init() {
		// Not on ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Check activation
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Load the textdomain
		load_plugin_textdomain( 'bea-find-media', false, BEA_FIND_MEDIA_PLUGIN_DIRNAME . '/languages' );

		trigger_error( sprintf( __( 'Plugin Boilerplate requires PHP version %s or greater to be activated.', 'bea-find-media' ), BEA_FIND_MEDIA_MIN_PHP_VERSION ) );

		// Deactive self
		deactivate_plugins( BEA_FIND_MEDIA_DIR . 'bea-find-media.php' );

		unset( $_GET['activate'] );

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	/**
	 * Notify the user about the incompatibility issue.
	 */
	public static function admin_notices() {
		echo '<div class="notice error is-dismissible">';
		echo '<p>' . esc_html( sprintf( __( 'Plugin Boilerplate require PHP version %s or greater to be activated. Your server is currently running PHP version %s.', 'bea-find-media' ), BEA_FIND_MEDIA_MIN_PHP_VERSION, PHP_VERSION ) ) . '</p>';
		echo '</div>';
	}
}
