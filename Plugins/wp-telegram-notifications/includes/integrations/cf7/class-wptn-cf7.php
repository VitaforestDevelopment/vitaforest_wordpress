<?php

namespace WPTN;

use WPCF7_Submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class CF7 {
	public $date;
	public $options;

	public function __construct() {
		$this->date    = WPTN_CURRENT_DATE;
		$this->options = Option::getOptions();
		$this->cf7_data = [];

		// Contact Form 7
		if ( isset( $this->options['cf7_metabox'] ) ) {
			add_filter( 'wpcf7_editor_panels', array( $this, 'cf7_editor_panels' ) );
			add_action( 'wpcf7_after_save', array( $this, 'wpcf7_save_form' ) );
			add_action( 'wpcf7_before_send_mail', array( $this, 'wpcf7_wptn_handler' ) );
		}
	}

	public function cf7_editor_panels( $panels ) {
		$new_page = array(
			'wptn' => array(
				'title'    => __( 'Telegram Notification', 'wp-telegram-notifications' ),
				'callback' => array( $this, 'cf7_setup_form' )
			)
		);

		$panels = array_merge( $panels, $new_page );

		return $panels;
	}

	public function cf7_setup_form( $form ) {
		$cf7_options = get_option( 'wpcf7_wptn_' . $form->id() );

		if ( ! isset( $cf7_options['channel'] ) ) {
			$cf7_options['channel'] = '';
		}
		if ( ! isset( $cf7_options['message'] ) ) {
			$cf7_options['message'] = '';
		}

		include_once "cf7-form.php";
	}

	public function wpcf7_save_form( $form ) {
		update_option( 'wpcf7_wptn_' . $form->id(), isset( $_POST['wpcf7-wptn'] ) ? $_POST['wpcf7-wptn'] : '' );
		update_option( 'wpcf7_wptn_form' . $form->id(), isset( $_POST['wpcf7-wptn-form'] ) ? $_POST['wpcf7-wptn-form'] : '' );
	}

	public function wpcf7_wptn_handler( $form ) {
		$cf7_options = get_option( 'wpcf7_wptn_' . $form->id() );

		foreach ( $_POST as $index => $key ) {
			if ( is_array( $key ) ) {
				$plain_data[ $index ] = implode( ',', $key );
			} else {
				$plain_data[ $index ] = $key;
			}
		}

		$message = isset( $cf7_options['message'] ) ? $cf7_options['message'] : '';
		$channel = isset( $cf7_options['channel'] ) ? $cf7_options['channel'] : '';

		$submission = WPCF7_Submission::get_instance();
		if ( $submission ) $this->cf7_data = $submission->get_posted_data();

		if ( $message && $channel ) {
			$channel_name = $channel;
			$message      = preg_replace_callback( '/%([a-zA-Z0-9._-]+)%/', function ( $matches ) {
				foreach ( $matches as $item ) {
					if ( isset( $this->cf7_data[$item] ) ) {
						if(is_array($this->cf7_data[$item])){
							$data = implode( ', ', $this->cf7_data[$item]);
							return $this->cf7_data[ $item ] = $data;
						} else {
							return $this->cf7_data[ $item ];
						}
					}
				}
			}, $message );

			BOT::sendMessage( $channel_name, $message );
		}
	}
}

new CF7();