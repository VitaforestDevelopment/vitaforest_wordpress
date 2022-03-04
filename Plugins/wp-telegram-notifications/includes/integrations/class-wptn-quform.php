<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Quform {

	public $options;

	public function __construct() {
		$this->options = Option::getOptions();

		add_action( 'quform_pre_process', array( $this, 'notification_form' ) );
	}

	public function notification_form() {
		if ( isset( $this->options[ 'qf_notify_enable_form_' . $_REQUEST['quform_form_id'] ] ) ) {
			$template_vars = array(
				'%post_title%'    => $_REQUEST['post_title'],
				'%form_url%'      => $_REQUEST['form_url'],
				'%referring_url%' => $_REQUEST['referring_url'],
			);
			$channel_name  = Channels::getChannelByID( $this->options[ 'qf_form_channel_' . $_REQUEST['quform_form_id'] ] );
			$message       = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options[ 'qf_form_template_' . $_REQUEST['quform_form_id'] ] );
			BOT::sendMessage( $channel_name, $message );
		}
	}
}

new Quform();