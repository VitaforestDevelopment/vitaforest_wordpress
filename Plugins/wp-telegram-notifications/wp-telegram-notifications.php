<?php
/**
 * Plugin Name: WP Telegram Notifications
 * Plugin URI: http://veronalabs.com
 * Description: A simple and powerful plugin for sending notifications to Telegram channel
 * Version: 2.0.5
 * Author: VeronaLabs
 * Author URI: http://veronalabs.com/
 * Text Domain: wp-telegram-notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
$wptn_settings_options = get_option( 'wptn_settings' );
$wptn_settings_options['license_key'] = '***********';
$wptn_settings_options['license_status'] = 'yes';
update_option( 'wptn_settings', $wptn_settings_options );
/**
 * Load Plugin Defines
 */
require_once 'includes/defines.php';

/**
 * Get plugin options
 */
$wptn_option = get_option( 'wptn_settings' );

// Run the plugin
include_once( 'includes/class-wptn.php' );

new WPTN();