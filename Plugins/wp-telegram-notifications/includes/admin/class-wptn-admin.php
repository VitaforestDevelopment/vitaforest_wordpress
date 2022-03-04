<?php

namespace WPTN;

use WPTN\Admin\Outbox;
use WPTN\Admin\Send;
use WPTN\Admin\Update;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Admin {

	public function __construct() {
		// Initial
		$this->init();

		// Add menu items
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Load admin assets
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

		// Load and check new version of the plugin
		$this->check_new_version();

	}

	/**
	 * Initial plugin
	 */
	private function init() {
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'wptn-hide-newsletter' ) {
				update_option( 'wptn_hide_newsletter', true );
			}
		}

		if ( ! get_option( 'wptn_hide_newsletter' ) ) {
			add_action( 'wptn_settings_page', array( $this, 'admin_newsletter' ) );
		}

		// Check exists require function
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . "wp-includes/pluggable.php" );
		}

		// Add plugin caps to admin role
		if ( is_admin() and is_super_admin() ) {
			$this->add_cap();
		}
	}

	/**
	 * Adding new capability in the plugin
	 */
	public function add_cap() {
		// get administrator role
		$role = get_role( 'administrator' );
		$role->add_cap( 'wptn_send' );
		$role->add_cap( 'wptn_outbox' );
		$role->add_cap( 'wptn_setting' );
	}

	/**
	 * Admin newsletter
	 */
	public function admin_newsletter() {
		include_once WPTN_DIR . '/includes/templates/wptn-admin-newsletter.php';
	}

	/**
	 * Include admin assets
	 */
	public function admin_assets() {
		wp_register_style( 'wptn-admin', WPTN_URL . 'assets/css/admin.css', true, WPTN_VERSION );
		wp_enqueue_style( 'wptn-admin' );
		wp_enqueue_style( 'wptn-emoji-area', WPTN_URL . 'assets/css/emojiarea.css', false, WPTN_VERSION );
		wp_enqueue_style( 'wptn-chosen', WPTN_URL . 'assets/css/chosen.min.css', true, WPTN_VERSION );
		wp_enqueue_script( 'wptn-emoji-area', WPTN_URL . 'assets/js/jquery.emojiarea.min.js', array( 'jquery' ), WPTN_VERSION, true );
		wp_enqueue_script( 'wptn-chosen', WPTN_URL . 'assets/js/chosen.jquery.min.js', true, WPTN_VERSION );
		wp_enqueue_script( 'wptn-admin', WPTN_URL . 'assets/js/admin.js', true, WPTN_VERSION );
		wp_localize_script( 'wptn-admin', 'wptn_vars', array(
			'upload_title'       => __( 'Select a image to upload', 'wp-telegram-notifications' ),
			'upload_button'      => __( 'Select', 'wp-telegram-notifications' ),
			'default_avatar_url' => WPTN_URL . 'assets/images/wptn-default-avatar-chatbox.png'
		) );
	}

	/**
	 * Administrator admin_menu
	 */
	public function admin_menu() {
		add_menu_page( __( 'Telegram Notifications', 'wp-telegram-notifications' ), __( 'Telegram', 'wp-telegram-notifications' ), 'wptn_send', 'wptn', array(
			$this,
			'send_callback'
		), WPTN_URL . 'assets/images/telegram-icon.png' );
		add_submenu_page( 'wptn', __( 'Send Message', 'wp-telegram-notifications' ), __( 'Send Message', 'wp-telegram-notifications' ), 'wptn_send', 'wptn', array(
			$this,
			'send_callback'
		) );
		add_submenu_page( 'wptn', __( 'Channels', 'wp-telegram-notifications' ), __( 'Channels', 'wp-telegram-notifications' ), 'wptn_send', 'wptn-channels', array(
			$this,
			'channels_callback'
		) );
		add_submenu_page( 'wptn', __( 'Outbox', 'wp-telegram-notifications' ), __( 'Outbox', 'wp-telegram-notifications' ), 'wptn_outbox', 'wptn-outbox', array(
			$this,
			'outbox_callback'
		) );
		// Add styles to menu pages
		// Uncomment if needed
		/*foreach ( $hook_suffix as $menu => $hook ) {
			add_action( "load-{$hook}", array( $this, $menu . '_assets' ) );
		}*/
	}

	/**
	 * Sending message page
	 */
	public function send_callback() {
		$page = new Send();
		$page->render_page();
	}

	/**
	 * Channels admin page
	 */
	public function channels_callback() {
		$page = new \WPTN\Admin\Channels();
		$page->render_page();
	}

	/**
	 * Outbox message admin page
	 */
	public function outbox_callback() {
		$page = new Outbox();
		$page->render_page();
	}

	/**
	 * Check new version of plugin
	 */
	public function check_new_version() {
		new Update( array(
			'plugin_slug'  => 'wp-telegram-notifications',
			'website_url'  => 'https://wp-telegram.com',
			'license_key'  => Option::getOption( 'license_key' ),
			'plugin_path'  => wp_normalize_path( WPTN_DIR ) . 'wp-telegram-notifications.php',
			'setting_page' => admin_url( 'admin.php?page=wptn-settings' )
		) );
	}

}

new Admin();
