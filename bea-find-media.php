<?php
/*
 Plugin Name: BEA - Find Media
 Version: 1.0.1
 Plugin URI: https://github.com/BeAPI/bea-find-media
 Description: Find where and how medias are used across your site.
 Author: Be API Technical team
 Author URI: https://beapi.fr
 Contributors: Maxime Culea
 
 Domain Path: languages
 Text Domain: bea-find-media
 ----
 Copyright 2017 Be API Technical team (human@beapi.fr)
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_FIND_MEDIA_VERSION', '1.0.1' );
define( 'BEA_FIND_MEDIA_MIN_PHP_VERSION', '7.0' );

// Plugin URL and PATH
define( 'BEA_FIND_MEDIA_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_FIND_MEDIA_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_FIND_MEDIA_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );


// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_FIND_MEDIA_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_FIND_MEDIA_DIR . 'compat.php' );

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\Find_Media\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

// Autoload all the things \o/
require_once BEA_FIND_MEDIA_DIR . 'autoload.php';

// Plugin activate/deactive hooks
register_activation_hook( __FILE__, [ '\BEA\Find_Media\Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ '\BEA\Find_Media\Plugin', 'deactivate' ] );

add_action( 'plugins_loaded', 'plugins_loaded_bea_find_media_plugin' );
/** Init the plugin */
function plugins_loaded_bea_find_media_plugin() {
	// DB
	\BEA\Find_Media\DB_Table::get_instance();

	// Client
	\BEA\Find_Media\Main::get_instance();
	// Addons
	\BEA\Find_Media\Addons\Main::get_instance();
	// Crons
	\BEA\Find_Media\Crons::get_instance();

	// Api
	\BEA\Find_Media\API\Json::get_instance();
	\BEA\Find_Media\API\Rest_Api::get_instance();

	// WP Cli
	\BEA\Find_Media\WP_Cli\Main::get_instance();

	// Admin or wp-cli context
	if ( is_admin() || defined( 'WP_CLI' ) ) {
		\BEA\Find_Media\Admin\Main::get_instance();
		\BEA\Find_Media\Admin\Post::get_instance();
		\BEA\Find_Media\Admin\Media::get_instance();
	}
}
