<?php

namespace WPTN\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Outbox {

	public function __construct() {
		if ( isset( $_REQUEST['page'] ) AND $_REQUEST['page'] == 'wptn-outbox' AND isset( $_GET['do'] ) AND $_GET['do'] == 'preview_message' AND isset( $_GET['msg_id'] ) ) {
			$this->do_preview();
		}
	}

	/**
	 * Outbox admin page
	 */
	public function render_page() {
		$style = "<style>
						#TB_iframeContent {
						    position: absolute;
						bottom: 0:
						}
				  </style>";
		echo $style;
		include_once WPTN_DIR . '/includes/admin/outbox/class-wptn-outbox-table.php';

		// Create an instance of our package class...
		$list_table = new Outbox_List_Table();

		// Fetch, prepare, sort, and filter our data...
		$list_table->prepare_items();

		include_once WPTN_DIR . "/includes/admin/outbox/outbox.php";
	}

	/**
	 * Execute preview request
	 */
	private function do_preview() {
		global $wpdb;
		$msg_id = isset( $_GET['msg_id'] ) ? $_GET['msg_id'] : null;
		if ( $msg_id ) {
			$msg_row = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}tn_outbox` WHERE message_id = {$msg_id}" );

			include "preview-message.php";
		} else {
			echo 'Wrong Parameter!';
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}