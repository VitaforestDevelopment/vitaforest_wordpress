<?php

namespace WPTN\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Channels {

	/**
	 * Channels admin page
	 */
	public function render_page() {
		// Create new channel
		if ( isset( $_POST['add_channel'] ) ) {
			if ( \WPTN\Channels::addChannel( $_POST['channel_name'] ) ) {
				Helper::notice( __( 'Channel created successfully', 'wp-telegram-notifications' ), 'success' );
			} else {
				Helper::notice( __( 'Failed to create channel', 'wp-telegram-notifications' ), 'error' );
			}
		}

		include_once WPTN_DIR . 'includes/admin/channels/class-wptn-channels-table.php';

		// Create an instance of our package class...
		$list_table = new Channels_List_Table();

		// Fetch, prepare, sort, and filter our data...
		$list_table->prepare_items();

		include_once WPTN_DIR . "includes/admin/channels/channels.php";
	}

}