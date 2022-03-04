<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Plugin defines
 */
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

define( 'WPTN_URL', plugin_dir_url( dirname( __FILE__ ) ) );
define( 'WPTN_DIR', plugin_dir_path( dirname( __FILE__ ) ) );

$plugin_data = get_plugin_data( WPTN_DIR . 'wp-telegram-notifications.php' );

define( 'WPTN_VERSION', $plugin_data['Version'] );
define( 'WPTN_ADMIN_URL', get_admin_url() );
define( 'WPTN_SITE', 'https://wp-telegram.com/' );
define( 'WPTN_MOBILE_REGEX', '/^[\+|\(|\)|\d|\- ]*$/' );
define( 'WPTN_CURRENT_DATE', date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) ) );