<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WPTN {

	public function __construct() {
		/*
		 * Plugin Loaded Action
		 */
		add_action( 'plugins_loaded', array( $this, 'plugin_setup' ) );

		/**
		 * Install And Upgrade plugin
		 */
		require_once WPTN_DIR . 'includes/class-wptn-install.php';

		register_activation_hook( WPTN_DIR . 'wp-telegram-notifications.php', array( '\WPTN\Install', 'install' ) );

	}

	/**
	 * Constructors plugin Setup
	 */
	public function plugin_setup() {
		// Load text domain
		add_action( 'init', array( $this, 'load_textdomain' ) );

		$this->includes();
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-telegram-notifications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Includes plugin files
	 *
	 * @param Not param
	 */
	public function includes() {

		// Utility classes.
		require_once WPTN_DIR . 'vendor/autoload.php';
		require_once WPTN_DIR . 'includes/class-wptn-option.php';
		require_once WPTN_DIR . 'includes/class-wptn-channels.php';
		require_once WPTN_DIR . 'includes/class-wptn-bot.php';
		require_once WPTN_DIR . 'includes/integrations/wordpress/class-wptn-wordpress.php';
		require_once WPTN_DIR . 'includes/integrations/cf7/class-wptn-cf7.php';
		require_once WPTN_DIR . 'includes/integrations/class-wptn-gravityforms.php';
		require_once WPTN_DIR . 'includes/integrations/class-wptn-quform.php';
		require_once WPTN_DIR . 'includes/integrations/class-wptn-woocommerce.php';
		require_once WPTN_DIR . 'includes/integrations/class-wptn-easy-digital-downloads.php';

		// Load admin classes.
		if ( is_admin() ) {
			// Admin & Settings classes.
			require_once WPTN_DIR . 'includes/admin/class-wptn-update.php';
			require_once WPTN_DIR . 'includes/admin/class-wptn-admin.php';
			require_once WPTN_DIR . 'includes/admin/class-wptn-admin-helper.php';
			require_once WPTN_DIR . 'includes/admin/class-wptn-settings.php';

			// Send class.
			require_once WPTN_DIR . 'includes/admin/send/class-wptn-send.php';

			// Channels class.
			require_once WPTN_DIR . 'includes/admin/channels/class-wptn-channels.php';

			// Outbox class.
			require_once WPTN_DIR . 'includes/admin/outbox/class-wptn-outbox.php';
		}

		// Template functions.
		require_once WPTN_DIR . 'includes/template-functions.php';
	}
}
