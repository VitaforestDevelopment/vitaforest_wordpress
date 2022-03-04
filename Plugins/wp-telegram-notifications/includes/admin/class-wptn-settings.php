<?php

namespace WPTN\Admin;

use WPTN\BOT;
use WPTN\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // No direct access allowed ;)

class Settings {

	public $setting_name;
	public $options = array();

	public function __construct() {
		$this->setting_name = 'wptn_settings';
		$this->get_settings();
		$this->options = get_option( $this->setting_name );

		if ( empty( $this->options ) ) {
			update_option( $this->setting_name, array() );
		}

		add_action( 'admin_menu', array( $this, 'add_settings_menu' ), 11 );

		if ( isset( $_GET['page'] ) and $_GET['page'] == 'wptn-settings' or isset( $_POST['option_page'] ) and $_POST['option_page'] == 'wptn_settings' ) {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		// Check License Code
		if ( isset( $_POST['submit'] ) AND isset( $_REQUEST['option_page'] ) AND $_REQUEST['option_page'] == 'wptn_settings' ) {
			add_filter( 'pre_update_option_' . $this->setting_name, array( $this, 'check_license_key' ), 10, 2 );
		}

	}

	/**
	 * Add setting menu
	 * */
	public function add_settings_menu() {
		add_submenu_page( 'wptn', __( 'Setting', 'wp-telegram-notifications' ), __( 'Setting', 'wp-telegram-notifications' ), 'wptn_setting', 'wptn-settings', array(
			$this,
			'render_settings'
		) );
	}

	/**
	 * Gets saved settings from WP core
	 *
	 * @return          array
	 * @since           2.0
	 */
	public function get_settings() {
		$settings = get_option( $this->setting_name );
		if ( empty( $settings ) ) {
			update_option( $this->setting_name, array(//'admin_lang'	=>  'enable',
			) );
		}

		// Set default values
		if ( ! Option::getOption( 'wordpress_chat_button_box_position' ) ) {
			Option::updateOption( 'wordpress_chat_button_box_position', 'right' );
		}

		return apply_filters( 'wptn_get_settings', $settings );
	}

	/**
	 * Registers settings in WP core
	 *
	 * @return          void
	 * @since           2.0
	 */
	public function register_settings() {
		if ( false == get_option( $this->setting_name ) ) {
			add_option( $this->setting_name );
		}

		foreach ( $this->get_registered_settings() as $tab => $settings ) {
			add_settings_section(
				'wptn_settings_' . $tab,
				__return_null(),
				'__return_false',
				'wptn_settings_' . $tab
			);

			if ( empty( $settings ) ) {
				return;
			}

			foreach ( $settings as $option ) {
				$name = isset( $option['name'] ) ? $option['name'] : '';

				add_settings_field(
					'wptn_settings[' . $option['id'] . ']',
					$name,
					array( $this, $option['type'] . '_callback' ),
					'wptn_settings_' . $tab,
					'wptn_settings_' . $tab,
					array(
						'id'          => isset( $option['id'] ) ? $option['id'] : null,
						'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'        => isset( $option['name'] ) ? $option['name'] : null,
						'after_input' => isset( $option['after_input'] ) ? $option['after_input'] : null,
						'section'     => $tab,
						'size'        => isset( $option['size'] ) ? $option['size'] : null,
						'options'     => isset( $option['options'] ) ? $option['options'] : '',
						'std'         => isset( $option['std'] ) ? $option['std'] : ''
					)
				);

				register_setting( $this->setting_name, $this->setting_name, array( $this, 'settings_sanitize' ) );
			}
		}
	}

	/**
	 * Gets settings tabs
	 *
	 * @return              array Tabs list
	 * @since               2.0
	 */
	public function get_tabs() {
		$tabs = array(
			'general'      => __( 'General', 'wp-telegram-notifications' ),
			'bot_api'      => __( 'Bot API', 'wp-telegram-notifications' ),
			'wordpress'    => __( 'WordPress', 'wp-telegram-notifications' ),
			'woocommerce'  => __( 'WooCommerce', 'wp-telegram-notifications' ),
			'cf7'          => __( 'CF7', 'wp-telegram-notifications' ),
			'gravityforms' => __( 'Gravityforms', 'wp-telegram-notifications' ),
			'quform'       => __( 'Quform', 'wp-telegram-notifications' ),
			'edd'          => __( 'EDD', 'wp-telegram-notifications' ),
		);

		return $tabs;
	}

	/*
		 * Activate Icon
		 */
	public function activate_icon() {
		if ( isset( $this->options['license_key_status'] ) ) {
			$item = array( 'icon' => 'no', 'text' => 'Deactive!', 'color' => '#ff0000' );

			if ( $this->options['license_key_status'] == "yes" ) {
				$item = array( 'icon' => 'yes', 'text' => 'Active!', 'color' => '#1eb514' );
			}

			return '<span style="color: ' . $item['color'] . '">&nbsp;&nbsp;<span class="dashicons dashicons-' . $item['icon'] . '" style="vertical-align: -4px;"></span>' . __( $item['text'], 'wp-telegram-notifications' ) . '</span>';
		}

		return null;
	}

	/*
	 * Check license key
	 */
	public function check_license_key( $new_value, $old_value ) {
		//Set Default Option
		$default_option = 'no';
		if ( isset( $_POST['wptn_settings']['license_key'] ) ) {
			/*
			 * Check License
			 */
			$response = wp_remote_get( add_query_arg( array(
				'plugin-name' => 'wp-telegram-notifications',
				'license_key' => sanitize_text_field( $_POST['wptn_settings']['license_key'] ),
				'website'     => get_bloginfo( 'url' ),
			),
				WPTN_SITE . '/wp-json/plugins/v1/validate'
			) );
			if ( is_wp_error( $response ) === false ) {
				$result = json_decode( $response['body'], true );
				if ( isset( $result['status'] ) and $result['status'] == 200 ) {
					$default_option = 'yes';
				}
			}

			$new_value['license_key_status'] = $default_option;

		} else {

			/*
			 * Set Old license
			 */
			if ( isset( $old_value['license_key_status'] ) and $old_value['license_key_status'] != "" ) {
				$new_value['license_key_status'] = $old_value['license_key_status'];
			} else {
				$new_value['license_key_status'] = $default_option;
			}

		}

		return $new_value;
	}

	/**
	 * Sanitizes and saves settings after submit
	 *
	 * @param array $input Settings input
	 *
	 * @return              array New settings
	 * @since               2.0
	 *
	 */
	public function settings_sanitize( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'wp';

		$input = $input ? $input : array();
		$input = apply_filters( 'wptn_settings_' . $tab . '_sanitize', $input );

		// Loop through each setting being saved and pass it through a sensitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;

			if ( $type ) {
				// Field type specific filter
				$input[ $key ] = apply_filters( 'wptn_settings_sanitize_' . $type, $value, $key );
			}

			// General filter
			$input[ $key ] = apply_filters( 'wptn_settings_sanitize', $value, $key );
		}

		// Loop through the white list and unset any that are empty for the tab being saved
		if ( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $value ) {

				// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
				if ( is_numeric( $key ) ) {
					$key = $value['id'];
				}

				if ( empty( $input[ $key ] ) ) {
					unset( $this->options[ $key ] );
				}

			}
		}

		// Merge our new settings with the existing
		$output = array_merge( $this->options, $input );

		add_settings_error( 'wptn-notices', '', __( 'Settings updated', 'wp-telegram-notifications' ), 'updated' );

		return $output;
	}

	/**
	 * Get settings fields
	 *
	 * @return          array Fields
	 * @since           2.0
	 */
	public function get_registered_settings() {
		$options = array(
			'enable'  => __( 'Enable', 'wp-telegram-notifications' ),
			'disable' => __( 'Disable', 'wp-telegram-notifications' )
		);

		$position = array(
			'right' => __( 'Right', 'wp-telegram-notifications' ),
			'left'  => __( 'Left', 'wp-telegram-notifications' )
		);

		$avatar_url = \WPTN\Option::getOption( 'wordpress_chat_button_box_avatar' );
		if ( ! $avatar_url ) {
			$avatar_url = WPTN_URL . 'assets/images/wptn-default-avatar-chatbox.png';
		}

		if ( is_admin() AND isset( $_REQUEST['page'] ) AND isset( $_REQUEST['tab'] ) AND $_REQUEST['page'] == 'wptn-settings' AND $_REQUEST['tab'] == 'bot_api' ) {

			$get_bot_api = BOT::getMe();
			if ( is_wp_error( $get_bot_api ) ) {
				$bot_api_status = $get_bot_api->get_error_message();
				$bot_api_name   = '';
			} else {
				$bot_api_status = 'Active';
				$bot_api_name   = $get_bot_api->username;
			}
		} else {
			$bot_api_status = '';
			$bot_api_name   = '';
		}

		$channels_list = [];
		foreach ( \WPTN\Channels::getChannels() as $channel ) {
			$channels_list[ $channel->ID ] = $channel->channel_name;
		}

		$gf_forms = array();
		$qf_forms = array();

		// Get gravityforms
		if ( class_exists( 'RGFormsModel' ) ) {
			$forms = \RGFormsModel::get_forms( null, 'title' );

			foreach ( $forms as $form ):

				$gf_forms[ 'gravityforms_form_title_' . $form->id ]    = array(
					'id'   => 'gravityforms_form_title_' . $form->id,
					'name' => sprintf( __( 'Form: %s', 'wp-telegram-notifications' ), $form->title ),
					'type' => 'header'
				);
				$gf_forms[ 'gravityforms_form_' . $form->id ]          = array(
					'id'      => 'gravityforms_form_' . $form->id,
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when this form get new message', 'wp-telegram-notifications' )
				);
				$gf_forms[ 'gravityforms_form_channel_' . $form->id ]  = array(
					'id'      => 'gravityforms_form_channel_' . $form->id,
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				);
				$gf_forms[ 'gravityforms_form_template_' . $form->id ] = array(
					'id'      => 'gravityforms_form_template_' . $form->id,
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Form name: %s, IP: %s, Form url: %s, User agent: %s, Content form: %s', 'wp-telegram-notifications' ),
						             '<code>%title%</code>',
						             '<code>%ip%</code>',
						             '<code>%source_url%</code>',
						             '<code>%user_agent%</code>',
						             '<code>%content%</code>'
					             )
				);
			endforeach;
		} else {
			$gf_forms['gravityforms_form'] = array(
				'id'   => 'gravityforms_form',
				'name' => __( 'Not active', 'wp-telegram-notifications' ),
				'type' => 'notice',
				'desc' => __( 'Gravityforms should be enable to run this tab.', 'wp-telegram-notifications' ),
			);
		}

		// Get quforms
		if ( class_exists( 'Quform_Repository' ) ) {
			$quform = new \Quform_Repository();
			$forms  = $quform->allForms();

			if ( $forms ) {
				foreach ( $forms as $form ):

					$qf_forms[ 'qf_notify_form_' . $form['id'] ]        = array(
						'id'   => 'qf_form_title_' . $form['id'],
						'name' => sprintf( __( 'Form: %s', 'wp-telegram-notifications' ), $form['name'] ),
						'type' => 'header'
					);
					$qf_forms[ 'qf_notify_enable_form_' . $form['id'] ] = array(
						'id'      => 'qf_notify_enable_form_' . $form['id'],
						'name'    => __( 'Status', 'wp-telegram-notifications' ),
						'type'    => 'checkbox',
						'options' => $options,
						'desc'    => __( 'Send message when this form get new message', 'wp-telegram-notifications' )
					);
					$qf_forms[ 'qf_form_channel_' . $form['id'] ]       = array(
						'id'      => 'qf_form_channel_' . $form['id'],
						'name'    => __( 'Channel', 'wp-telegram-notifications' ),
						'type'    => 'select',
						'options' => $channels_list,
						'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
					);
					$qf_forms[ 'qf_form_template_' . $form['id'] ]      = array(
						'id'      => 'qf_form_template_' . $form['id'],
						'name'    => __( 'Message body', 'wp-telegram-notifications' ),
						'type'    => 'textarea',
						'options' => 'true',
						'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
						             sprintf(
							             __( 'Form name: %s, Form url: %s, Referring url: %s', 'wp-telegram-notifications' ),
							             '<code>%post_title%</code>',
							             '<code>%form_url%</code>',
							             '<code>%referring_url%</code>'
						             )
					);
				endforeach;
			} else {
				$qf_forms['qf_notify_form'] = array(
					'id'   => 'qf_notify_form',
					'name' => __( 'No data', 'wp-telegram-notifications' ),
					'type' => 'notice',
					'desc' => __( 'There is no form available on Quform plugin, please first add your forms.', 'wp-telegram-notifications' ),
				);
			}
		} else {
			$qf_forms['qf_notify_form'] = array(
				'id'   => 'qf_notify_form',
				'name' => __( 'Not active', 'wp-telegram-notifications' ),
				'type' => 'notice',
				'desc' => __( 'Quform should be enable to run this tab.', 'wp-telegram-notifications' ),
			);
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$wc_settings = array(
				'woocommerce_new_order_title'      => array(
					'id'   => 'woocommerce_new_order_title',
					'name' => __( 'New order', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'woocommerce_new_order'            => array(
					'id'      => 'woocommerce_new_order',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send a message to you when get new order.', 'wp-telegram-notifications' )
				),
				'woocommerce_new_order_channel'    => array(
					'id'      => 'woocommerce_new_order_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'woocommerce_new_order_template'   => array(
					'id'      => 'woocommerce_new_order_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					sprintf(
						__( 'Billing First Name: %s, Billing Company: %s, Billing Address: %s, Billing Phone Number: %s, Billing Email Address: %s, Order id: %s, Order number: %s, Order Total: %s, Order status: %s, Customer Username: %s, Customer Email: %s', 'wp-sms' ),
						'<code>%billing_first_name%</code>',
						'<code>%billing_company%</code>',
						'<code>%billing_address%</code>',
						'<code>%billing_phone%</code>',
						'<code>%billing_email%</code>',
						'<code>%order_id%</code>',
						'<code>%order_number%</code>',
						'<code>%order_total%</code>',
						'<code>%status%</code>',
						'<code>%customer_username%</code>',
						'<code>%customer_email%</code>'
					),
				),
				'woocommerce_new_product_title'    => array(
					'id'   => 'woocommerce_new_product_title',
					'name' => __( 'New product', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'woocommerce_new_product'          => array(
					'id'      => 'woocommerce_new_product',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when publish new a product', 'wp-telegram-notifications' )
				),
				'woocommerce_new_product_channel'  => array(
					'id'      => 'woocommerce_new_product_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'woocommerce_new_product_template' => array(
					'id'      => 'woocommerce_new_product_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Product title: %s, Product url: %s, Product date: %s, Product price: %s', 'wp-telegram-notifications' ),
						             '<code>%product_title%</code>',
						             '<code>%product_url%</code>',
						             '<code>%product_date%</code>',
						             '<code>%product_price%</code>'
					             )
				),
				'woocommerce_stock_title'          => array(
					'id'   => 'woocommerce_stock_title',
					'name' => __( 'Low stock', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'woocommerce_stock'                => array(
					'id'      => 'woocommerce_stock',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when stock is low', 'wp-telegram-notifications' )
				),
				'woocommerce_stock_channel'        => array(
					'id'      => 'woocommerce_stock_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'woocommerce_stock_template'       => array(
					'id'      => 'woocommerce_stock_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Product id: %s, Product name: %s', 'wp-telegram-notifications' ),
						             '<code>%product_id%</code>',
						             '<code>%product_name%</code>'
					             )
				),
			);
		} else {
			$wc_settings = array(
				'woocommerce_disable_notification' => array(
					'id'   => 'woocommerce_disable_notification',
					'name' => __( 'Not active', 'wp-telegram-notifications' ),
					'type' => 'notice',
					'desc' => __( 'WooCommerce should be enable to run this tab.', 'wp-telegram-notifications' ),
				) );
		}

		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			$cf7_settings = array(
				'cf7_metabox_title' => array(
					'id'   => 'cf7_metabox_title',
					'name' => __( 'Metabox notifications', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'cf7_metabox'       => array(
					'id'      => 'cf7_metabox',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Added notifications box to forms page when enable this option.', 'wp-telegram-notifications' )
				),
			);
		} else {
			$cf7_settings = array(
				'cf7_disable_notification' => array(
					'id'   => 'cf7_disable_notification',
					'name' => __( 'Not active', 'wp-telegram-notifications' ),
					'type' => 'notice',
					'desc' => __( 'Contact form 7 should be enable to run this tab.', 'wp-telegram-notifications' ),
				) );
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			$edd_settings = array(
				'edd_new_order_title'    => array(
					'id'   => 'edd_new_order_title',
					'name' => __( 'New order', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'edd_new_order'          => array(
					'id'      => 'edd_new_order',
					'name'    => __( 'New order', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send a message to you when get new order.', 'wp-telegram-notifications' )
				),
				'edd_new_order_channel'  => array(
					'id'      => 'edd_new_order_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'edd_new_order_template' => array(
					'id'      => 'edd_new_order_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Customer email: %s, Customer name: %s, Customer last name: %s', 'wp-telegram-notifications' ),
						             '<code>%edd_email%</code>',
						             '<code>%edd_first%</code>',
						             '<code>%edd_last%</code>'
					             )
				)
			);
		} else {
			$edd_settings = array(
				'edd_disable_notification' => array(
					'id'   => 'edd_disable_notification',
					'name' => __( 'Not active', 'wp-telegram-notifications' ),
					'type' => 'notice',
					'desc' => __( 'Easy digital downloads should be enable to run this tab.', 'wp-telegram-notifications' ),
				) );
		}

		$settings = apply_filters( 'wptn_registered_settings', array(
			// Options for general tab
			'general'      => apply_filters( 'wp-telegram-notifications', array(
				'license'     => array(
					'id'   => 'license',
					'name' => __( 'License', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'license_key' => array(
					'id'          => 'license_key',
					'name'        => __( 'License Key', 'wp-telegram-notifications' ),
					'type'        => 'text',
					'after_input' => $this->activate_icon(),
					'desc'        => sprintf(
						__( 'The license key is used for access to automatic update and support, to get the license, please go to %1$syour account%2$s', 'wp-telegram-notifications' ),
						'<a href="' . esc_url( WPTN_SITE ) . '" target="_blank">',
						'</a>'
					),
				),
			) ),
			// Gateway tab
			'bot_api'      => apply_filters( 'wptn_bot_settings', array(
				// Gateway
				'bot_api_header'        => array(
					'id'   => 'bot_api_header',
					'name' => __( 'Bot API', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'bot_api_token'         => array(
					'id'      => 'bot_api_token',
					'name'    => __( 'Your bot api token', 'wp-telegram-notifications' ),
					'type'    => 'text',
					'options' => '',
					'desc'    => __( 'Please enter your bot api token', 'wp-telegram-notifications' )
				),
				// Credit
				'bot_api_status_header' => array(
					'id'   => 'bot_api_status_header',
					'name' => __( 'Bot status', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'bot_api_status'        => array(
					'id'      => 'bot_api_status',
					'name'    => __( 'Bot status', 'wp-telegram-notifications' ),
					'type'    => 'html',
					'options' => $bot_api_status,
				),
				'bot_api_username'      => array(
					'id'      => 'bot_api_username',
					'name'    => __( 'Bot name', 'wp-telegram-notifications' ),
					'type'    => 'html',
					'options' => $bot_api_name,
				),
			) ),
			// Wordpress notify tab
			'wordpress'    => apply_filters( 'wptn_wordpress_settings', array(
				// Publish new post
				'wordpress_publish_new_post_title'      => array(
					'id'   => 'wordpress_publish_new_post_title',
					'name' => __( 'Published new posts', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_publish_new_post'            => array(
					'id'      => 'wordpress_publish_new_post',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message to channel When published new posts.', 'wp-telegram-notifications' )
				),
				'wordpress_publish_new_post_channel'    => array(
					'id'      => 'wordpress_publish_new_post_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'wordpress_publish_new_post_template'   => array(
					'id'      => 'wordpress_publish_new_post_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Post title: %s, Post content: %s, Post url: %s, Post date: %s', 'wp-telegram-notifications' ),
						             '<code>%post_title%</code>',
						             '<code>%post_content%</code>',
						             '<code>%post_url%</code>',
						             '<code>%post_date%</code>'
					             )
				),
				// Publish new post
				'wordpress_chat_button_title'           => array(
					'id'   => 'wordpress_chat_button_title',
					'name' => __( 'Chat Button', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_chat_button_status'          => array(
					'id'      => 'wordpress_chat_button_status',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Set chat button for each page.', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_channel'         => array(
					'id'      => 'wordpress_chat_button_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_box_position'    => array(
					'id'      => 'wordpress_chat_button_box_position',
					'name'    => __( 'Position', 'wp-telegram-notifications' ),
					'type'    => 'radio',
					'options' => $position,
					'desc'    => __( 'Set chat box showing position on pages.', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_box_avatar'      => array(
					'id'      => 'wordpress_chat_button_box_avatar',
					'name'    => __( 'Avatar', 'wp-telegram-notifications' ),
					'type'    => 'upload_image',
					'options' => $avatar_url,
					'desc'    => __( 'Set default avatar.', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_box_title'       => array(
					'id'   => 'wordpress_chat_button_box_title',
					'name' => __( 'Title', 'wp-telegram-notifications' ),
					'type' => 'text',
					'desc' => __( 'Enter the title of the box.', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_welcome_msg'     => array(
					'id'      => 'wordpress_chat_button_welcome_msg',
					'name'    => __( 'Welcome message', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the welcome message.', 'wp-telegram-notifications' )
				),
				'wordpress_chat_button_final_msg'       => array(
					'id'      => 'wordpress_chat_button_final_msg',
					'name'    => __( 'Final message', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the final message.', 'wp-telegram-notifications' )
				),
				// Publish new wp version
				'wordpress_publish_new_version_title'   => array(
					'id'   => 'wordpress_publish_new_version_title',
					'name' => __( 'The new release of WordPress', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_publish_new_version'         => array(
					'id'      => 'wordpress_publish_new_version',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when the new release of WordPress.', 'wp-telegram-notifications' )
				),
				'wordpress_publish_new_version_channel' => array(
					'id'      => 'wordpress_publish_new_version_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				// Register new user
				'wordpress_register_new_user_title'     => array(
					'id'   => 'wordpress_register_new_user_title',
					'name' => __( 'Register a new user', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_register_new_user'           => array(
					'id'      => 'wordpress_register_new_user',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when register on wordpress.', 'wp-telegram-notifications' )
				),
				'wordpress_register_new_user_channel'   => array(
					'id'      => 'wordpress_register_new_user_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'wordpress_register_new_user_template'  => array(
					'id'      => 'wordpress_register_new_user_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'User login: %s, User email: %s, Register date: %s', 'wp-telegram-notifications' ),
						             '<code>%user_login%</code>',
						             '<code>%user_email%</code>',
						             '<code>%date_register%</code>'
					             )
				),
				// New comment
				'wordpress_new_comment_title'           => array(
					'id'   => 'wordpress_new_comment_title',
					'name' => __( 'New comment', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_new_comment'                 => array(
					'id'      => 'wordpress_new_comment',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when get a new comment.', 'wp-telegram-notifications' )
				),
				'wordpress_new_comment_channel'         => array(
					'id'      => 'wordpress_new_comment_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'wordpress_new_comment_template'        => array(
					'id'      => 'wordpress_new_comment_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Comment author: %s, Author email: %s, Author url: %s, Author IP: %s, Comment date: %s, Comment content: %s, Comment url: %s', 'wp-telegram-notifications' ),
						             '<code>%comment_author%</code>',
						             '<code>%comment_author_email%</code>',
						             '<code>%comment_author_url%</code>',
						             '<code>%comment_author_IP%</code>',
						             '<code>%comment_date%</code>',
						             '<code>%comment_content%</code>',
						             '<code>%comment_url%</code>'
					             )
				),
				// User login
				'wordpress_user_login_title'            => array(
					'id'   => 'wordpress_user_login_title',
					'name' => __( 'User login', 'wp-telegram-notifications' ),
					'type' => 'header'
				),
				'wordpress_user_login'                  => array(
					'id'      => 'wordpress_user_login',
					'name'    => __( 'Status', 'wp-telegram-notifications' ),
					'type'    => 'checkbox',
					'options' => $options,
					'desc'    => __( 'Send message when user is login.', 'wp-telegram-notifications' )
				),
				'wordpress_user_login_channel'          => array(
					'id'      => 'wordpress_user_login_channel',
					'name'    => __( 'Channel', 'wp-telegram-notifications' ),
					'type'    => 'select',
					'options' => $channels_list,
					'desc'    => __( 'Select the channel for sending message', 'wp-telegram-notifications' )
				),
				'wordpress_user_login_template'         => array(
					'id'      => 'wordpress_user_login_template',
					'name'    => __( 'Message body', 'wp-telegram-notifications' ),
					'type'    => 'textarea',
					'options' => 'true',
					'desc'    => __( 'Enter the contents of the message.', 'wp-telegram-notifications' ) . '<br>' .
					             sprintf(
						             __( 'Username: %s, Nickname: %s', 'wp-telegram-notifications' ),
						             '<code>%username_login%</code>',
						             '<code>%display_name%</code>'
					             )
				),
			) ),
			// Woocommerce tab
			'woocommerce'  => apply_filters( 'wptn_cf7_settings', $wc_settings ),
			// CF7  tab
			'cf7'          => apply_filters( 'wptn_cf7_settings', $cf7_settings ),
			// Gravityforms tab
			'gravityforms' => apply_filters( 'wptn_gravityforms_settings', $gf_forms ),
			// Quform tab
			'quform'       => apply_filters( 'wptn_quform_settings', $qf_forms ),
			// EDD tab
			'edd'          => apply_filters( 'wptn_cf7_settings', $edd_settings )
		) );

		return $settings;
	}

	public function header_callback( $args ) {
		echo '<hr/>';
	}

	public function html_callback( $args ) {
		echo $args['options'];
	}

	public function notice_callback( $args ) {
		echo $args['desc'];
	}

	public function checkbox_callback( $args ) {

		$checked = isset( $this->options[ $args['id'] ] ) ? checked( 1, $this->options[ $args['id'] ], false ) : '';
		$html    = '<input type="checkbox" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
		$html    .= '<label for="wptn_settings[' . $args['id'] . ']"> ' . __( 'Active', 'wp-telegram-notifications' ) . '</label>';
		$html    .= '<p class="description">' . $args['desc'] . '</p>';

		echo $html;
	}

	public function multicheck_callback( $args ) {
		$html = '';
		foreach ( $args['options'] as $key => $value ) {
			$option_name = $args['id'] . '-' . $key;
			$this->checkbox_callback( array(
				'id'   => $option_name,
				'desc' => $value
			) );
			echo '<br>';
		}

		echo $html;
	}

	public function radio_callback( $args ) {

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key ) {
				$checked = true;
			} elseif ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) ) {
				$checked = true;
			}

			echo '<input name="wptn_settings[' . $args['id'] . ']"" id="wptn_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>';
			echo '<label for="wptn_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label>&nbsp;&nbsp;';
		endforeach;

		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	public function text_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size        = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$after_input = ( isset( $args['after_input'] ) && ! is_null( $args['after_input'] ) ) ? $args['after_input'] : '';
		$html        = '<input type="text" class="' . $size . '-text" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html        .= $after_input;
		$html        .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function number_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$max  = isset( $args['max'] ) ? $args['max'] : 999999;
		$min  = isset( $args['min'] ) ? $args['min'] : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;

		$size  = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function textarea_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if ( isset( $args['options'] ) ) {
			$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
			$html = '<div class="wptn-emoji-area" data-emojiarea="" data-type="unicode" data-global-picker="false">
                        <div class="emoji-button">' . __( '😊', 'wp-telegram-notifications' ) . '</div>
                            <textarea class="large-text" cols="50" rows="5" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>;
                        </div>';
			$html .= '<p class="description"> ' . $args['desc'] . '</p>';
		} else {
			$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
			$html = '<textarea class="large-text" cols="50" rows="5" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
			$html .= '<p class="description"> ' . $args['desc'] . '</p>';
		}

		echo $html;
	}

	public function password_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="password" class="' . $size . '-text" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function missing_callback( $args ) {
		echo '&ndash;';

		return false;
	}


	public function select_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$html = '<select id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html     .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function advancedselect_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if ( is_rtl() ) {
			$class_name = 'chosen-select chosen-rtl';
		} else {
			$class_name = 'chosen-select';
		}

		$html = '<select class="' . $class_name . '" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $key => $v ) {
			$html .= '<optgroup label="' . ucfirst( $key ) . '">';

			foreach ( $v as $option => $name ) :
				$selected = selected( $option, $value, false );
				$html     .= '<option value="' . $option . '" ' . $selected . '>' . ucfirst( $name ) . '</option>';
			endforeach;

			$html .= '</optgroup>';
		}

		$html .= '</select>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function color_select_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$html = '<select id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $color ) :
			$selected = selected( $option, $value, false );
			$html     .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function rich_editor_callback( $args ) {
		global $wp_version;
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
			$html = wp_editor( stripslashes( $value ), 'wptn_settings[' . $args['id'] . ']', array( 'textarea_name' => 'wptn_settings[' . $args['id'] . ']' ) );
		} else {
			$html = '<textarea class="large-text" rows="10" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		}

		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function upload_image_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$display_clear = $value ? "" : "display: none;";

		wp_enqueue_media();

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = "<div class='wptn-image-preview-wrapper'><img id='wptn-image-preview' src='" . $args['options'] . "' width='60' height='60' style='border-radius: 50%;'></div>";
		$html .= '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="wptn_settings[' . $args['id'] . ']"  name="wptn_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<span>&nbsp;<input type="button" class="wptn_settings_upload_button button-secondary" value="' . __( 'Upload File', 'wp-telegram-notifications' ) . '"/>&nbsp;<input type="button" class="wptn_settings_clear_upload_button button-secondary" style="' . $display_clear . '" value="' . __( 'X', 'wp-telegram-notifications' ) . '"/></span>';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function color_callback( $args ) {
		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$default = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="wptn-color-picker" id="wptn_settings[' . $args['id'] . ']" name="wptn_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
		$html .= '<p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	public function render_settings() {
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->get_tabs() ) ? $_GET['tab'] : 'general';

		ob_start();
		?>
        <div class="wrap wptn-settings-wrap">
			<?php do_action( 'wptn_settings_page' ); ?>
            <h2><?php _e( 'Settings', 'wp-telegram-notifications' ) ?></h2>
            <div class="wptn-tab-group">
                <ul class="wptn-tab">
                    <li id="wptn-logo">
                        <img src="<?php echo WPTN_URL; ?>assets/images/logo-wptn.png"/>
                        <p><?php echo sprintf( __( 'WP-Telegram v%s', 'wp-telegram-notifications' ), WPTN_VERSION ); ?></p>
                    </li>
					<?php
					foreach ( $this->get_tabs() as $tab_id => $tab_name ) {

						$tab_url = add_query_arg( array(
							'settings-updated' => false,
							'tab'              => $tab_id
						) );

						$active = $active_tab == $tab_id ? 'active' : '';

						echo '<li><a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="' . $active . '">';
						echo $tab_name;
						echo '</a></li>';
					}
					?>
                </ul>
				<?php echo settings_errors( 'wptn-notices' ); ?>
                <div class="wptn-tab-content">
                    <form method="post" action="options.php">
                        <table class="form-table">
							<?php
							settings_fields( $this->setting_name );
							do_settings_fields( 'wptn_settings_' . $active_tab, 'wptn_settings_' . $active_tab );
							?>
                        </table>
						<?php submit_button(); ?>
                    </form>
                </div>
            </div>
        </div>
		<?php
		echo ob_get_clean();
	}
}

new Settings();