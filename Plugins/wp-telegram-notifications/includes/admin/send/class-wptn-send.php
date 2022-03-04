<?php

namespace WPTN\Admin;

use WPTN\BOT;
use WPTN\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Send {

	/**
	 * Sending message admin page
	 */
	public function render_page() {
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			$get_bloginfo_url = WPTN_ADMIN_URL . "admin.php?page=wptn-settings";
			echo '<br><div class="update-nag">' . sprintf( __( 'You should have set Bot API token in setting page for sending message', 'wp-telegram-notifications' ), $get_bloginfo_url ) . '</div>';

			return;
		}

		if ( isset( $_POST['do_send'] ) ) {
			if ( $_POST['message'] ) {
				$result = BOT::sendMessage( $_POST['channel_name'], $_POST['message'] );

				if ( is_wp_error( $result ) ) {
					$result = $result->get_error_message();
					Helper::notice( sprintf( __( 'Message was not delivered! results received: %s', 'wp-telegram-notifications' ), $result ), 'error' );
				} else {
					Helper::notice( __( 'Message was sent with success', 'wp-telegram-notifications' ), 'success' );
				}
			} else {
				Helper::notice( __( 'Please enter a message', 'wp-telegram-notifications' ), 'error' );
			}
		}

		include_once WPTN_DIR . "includes/admin/send/send-message.php";
	}
}
