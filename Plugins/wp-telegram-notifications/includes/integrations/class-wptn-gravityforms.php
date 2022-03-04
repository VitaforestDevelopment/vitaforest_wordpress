<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class GravityForms {

	public $options;

	public function __construct() {
		$this->options = Option::getOptions();

		add_action( 'gform_after_submission', array( $this, 'notification_form' ), 10, 2 );
	}

	public function notification_form( $entry, $form ) {
		if ( isset( $this->options[ 'gravityforms_form_' . $form['id'] ] ) ) {
			$channel_name = Channels::getChannelByID( $this->options[ 'gravityforms_form_channel_' . $form['id'] ] );

			foreach ( $form['fields'] as $items ) {
				$value = apply_filters('wptn_gravityforms_field_value', $entry[ $items['id'] ], $items);
				$result[] = $value;
			}

			if ( ! isset( $result ) ) {
				$result = array();
			}

			$template_vars = array(
				'%title%'      => $form['title'],
				'%ip%'         => $entry['ip'],
				'%source_url%' => $entry['source_url'],
				'%user_agent%' => $entry['user_agent'],
				'%content%'    => implode( "\n", $result )
			);

			$message = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options[ 'gravityforms_form_template_' . $form['id'] ] );
			BOT::sendMessage( $channel_name, $message, 'HTML');
		}
	}
}

new GravityForms();