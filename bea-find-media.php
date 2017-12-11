<?php
/*
 Plugin Name: BEA - Find Media
 Version: 1.0.0
 Plugin URI: https://beapi.fr
 Description: Find where medias are used across your site.
 Author: Be API Technical team
 Author URI: https://beapi.fr
 Domain Path: languages
 Text Domain: bea-find-media

 ----

 Copyright 2017 Be API Technical team (human@beapi.fr)

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
define( 'BEA_FIND_MEDIA_VERSION', '1.0.0' );
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
/** Init the plugin and load all classes */
function plugins_loaded_bea_find_media_plugin() {
	// DB
	\BEA\Find_Media\DB_Table::get_instance();

	// Client
	\BEA\Find_Media\Main::get_instance();
	// Addons
	\BEA\Find_Media\Addons\Main::get_instance();

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
