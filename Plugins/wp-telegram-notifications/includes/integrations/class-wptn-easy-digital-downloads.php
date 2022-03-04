<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class EDD {

	public $options;

	public function __construct() {
		$this->options = Option::getOptions();

		if ( isset( $this->options['edd_new_order'] ) ) {
			add_action( 'edd_complete_purchase', array( $this, 'notification_order' ) );
		}
	}

	public function notification_order() {
		$template_vars = array(
			'%edd_email%' => $_REQUEST['edd_email'],
			'%edd_first%' => $_REQUEST['edd_first'],
			'%edd_last%'  => $_REQUEST['edd_last'],
		);
		$channel_name  = Channels::getChannelByID( $this->options['edd_new_order_channel'] );
		$message       = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['edd_new_order_template'] );
		BOT::sendMessage( $channel_name, $message );
	}
}

new EDD();