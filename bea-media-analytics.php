<?php
/*
 Plugin Name: BEA - Media Analytics
 Version: 2.1.0
 Plugin URI: https://github.com/BeAPI/bea-media-analytics
 Description: Find where and how media are used across your site.
 Author: Be API Technical team
 Author URI: https://beapi.fr
 Domain Path: languages
 Text Domain: bea-media-analytics
 Contributors: Maxime Culea, Amaury Balmer

 ----

 Copyright 2018 Be API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_MEDIA_ANALYTICS_VERSION', '2.1.0' );
define( 'BEA_MEDIA_ANALYTICS_MIN_PHP_VERSION', '5.6' );

// Plugin URL and PATH
define( 'BEA_MEDIA_ANALYTICS_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_MEDIA_ANALYTICS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_MEDIA_ANALYTICS_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );

// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_MEDIA_ANALYTICS_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_MEDIA_ANALYTICS_DIR . 'compat.php' );

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\Media_Analytics\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

// Autoload all the things \o/
require_once BEA_MEDIA_ANALYTICS_DIR . 'autoload.php';

// Plugin activate/deactive hooks
register_activation_hook( __FILE__, [ '\BEA\Media_Analytics\Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ '\BEA\Media_Analytics\Plugin', 'deactivate' ] );

add_action( 'plugins_loaded', 'plugins_loaded_bea_media_analytics_plugin' );
/** Init the plugin */
function plugins_loaded_bea_media_analytics_plugin() {
	// Upgrader
	\BEA\Media_Analytics\Upgrader::get_instance();
	// DB
	\BEA\Media_Analytics\DB_Table::get_instance();

	// Client
	\BEA\Media_Analytics\Main::get_instance();
	// Addons
	\BEA\Media_Analytics\Addons\Main::get_instance();
	// Crons
	\BEA\Media_Analytics\Crons::get_instance();

	// Api
	\BEA\Media_Analytics\API\Json::get_instance();
	\BEA\Media_Analytics\API\Query::get_instance();
	\BEA\Media_Analytics\API\Rest_Api::get_instance();

	// WP Cli
	\BEA\Media_Analytics\WP_Cli\Main::get_instance();

	// Admin or wp-cli context
	if ( is_admin() || defined( 'WP_CLI' ) ) {
		\BEA\Media_Analytics\Admin\Main::get_instance();
		\BEA\Media_Analytics\Admin\Post::get_instance();
		\BEA\Media_Analytics\Admin\Option::get_instance();
		\BEA\Media_Analytics\Admin\Media::get_instance();
	}
}
