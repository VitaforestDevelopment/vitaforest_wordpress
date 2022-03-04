<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Wordpress {

	public $date;
	public $options;

	public function __construct() {
		global $wp_version;

		$this->date    = WPTN_CURRENT_DATE;
		$this->options = Option::getOptions();

		if ( isset( $this->options['wordpress_publish_new_post'] ) ) {
			add_action( 'add_meta_boxes', array( $this, 'notification_meta_box' ) );
			add_action( 'publish_post', array( $this, 'new_post' ), 10, 2 );
		}

		if ( isset ( $this->options['wordpress_chat_button_status'] ) ) {
			add_action( 'wp_ajax_wptn_chatbox_send_message', array( $this, 'wptn_chatbox_send_message' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_chat_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_chat_scripts' ) );
			add_action( 'wp_footer', array( $this, 'footer_chat_html' ) );
			// Remove this two lines if you want the default wordpress emojis back
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		}

		// Wordpress new version
		if ( isset( $this->options['wordpress_publish_new_version'] ) ) {
			$update = get_site_transient( 'update_core' );
			$update = $update->updates;

			if ( isset( $update[1] ) ) {
				if ( $update[1]->current > $wp_version ) {
					if ( get_option( 'wptn_last_send_notification' ) == false ) {
						$message      = sprintf( __( 'WordPress %s is available! Please update now', 'wp-telegram-notifications' ), $update[1]->current );
						$channel_name = Channels::getChannelByID( $this->options['wordpress_publish_new_version_channel'] );
						BOT::sendMessage( $channel_name, $message );

						update_option( 'wptn_last_send_notification', true );
					}
				} else {
					update_option( 'wptn_last_send_notification', false );
				}
			}
		}

		if ( isset( $this->options['wordpress_register_new_user'] ) ) {
			add_action( 'user_register', array( $this, 'new_user' ), 10, 1 );
		}

		if ( isset( $this->options['wordpress_new_comment'] ) ) {
			add_action( 'wp_insert_comment', array( $this, 'new_comment' ), 99, 2 );
		}

		if ( isset( $this->options['wordpress_user_login'] ) ) {
			add_action( 'wp_login', array( $this, 'login_user' ), 99, 2 );
		}
	}

	public function notification_meta_box() {
		add_meta_box( 'wptn-meta-box', __( 'Telegram Notifications', 'wp-telegram-notifications' ), array(
			$this,
			'notification_meta_box_handler'
		), 'post', 'normal', 'high' );

		// Load meta box assets
		add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_assets' ) );
	}

	/**
	 * @internal param $post
	 */
	public function notification_meta_box_handler() {
		include_once "meta-box.php";
	}

	/**
	 * Include meta box assets
	 */
	public function meta_box_assets() {
		wp_enqueue_style( 'wptn-meta-box', WPTN_URL . 'assets/css/wptn-metabox.css', false, WPTN_VERSION );
		wp_enqueue_script( 'wptn-meta-box', WPTN_URL . 'assets/js/wptn-meta-box.js', array( 'jquery' ), WPTN_VERSION, true );
	}

	/**
	 * @param $ID
	 * @param $post
	 *
	 * @return null
	 * @internal param $post_id
	 */
	public function new_post( $ID, $post ) {
		if ( empty( $_REQUEST['wptn_send'] ) ) {
			return;
		}

		if ( $_REQUEST['wptn_send'] == 'yes' ) {
			$channel_name  = $_REQUEST['wptn_channel_name'];
			$template_vars = array(
				'%post_title%'   => get_the_title( $ID ),
				'%post_content%' => $post->post_content,
				'%post_url%'     => wp_get_shortlink( $ID ),
				'%post_date%'    => get_post_time( 'Y-m-d', true, $ID, true ),
			);

			// Get post thumbnail url
			$thumbnail = get_the_post_thumbnail_url( $ID );

			// Check send thumbnail is active
			if ( isset( $_POST['wptn_attachment_type'] ) and $_POST['wptn_attachment_type'] == "image" ) {
				// Unset content from the message
				unset( $template_vars['%post_content%'] );

				$message = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $_REQUEST['wptn-text-template'] );

				if ( $_POST['wptn_image_type'] == "thumbnail" ) {
					if ( $_POST['wptn_attachment_position'] == "before" ) {
						BOT::sendPhoto( $channel_name, $thumbnail, $message );
					} else {
						BOT::sendPhoto( $channel_name, $thumbnail, $message, true );
					}
				} else {
					if ( $_POST['wptn_attachment_position'] == "before" ) {
						BOT::sendPhoto( $channel_name, $_POST['wptn_image_final'], $message );
					} else {
						BOT::sendPhoto( $channel_name, $_POST['wptn_image_final'], $message, true );
					}
				}
			} elseif ( isset( $_POST['wptn_attachment_type'] ) and $_POST['wptn_attachment_type'] == "video" ) {
				// Unset content from the message
				unset( $template_vars['%post_content%'] );

				$message = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $_REQUEST['wptn-text-template'] );

				if ( $_POST['wptn_attachment_position'] == "before" ) {
					BOT::sendVideo( $channel_name, $_POST['wptn_video_final'], $message );
				} else {
					BOT::sendVideo( $channel_name, $_POST['wptn_video_final'], $message, true );
				}
			} else {
				$message = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $_REQUEST['wptn-text-template'] );

				BOT::sendMessage( $channel_name, $message );
			}

		}
	}

	/**
	 * @param $user_id
	 */
	public function new_user( $user_id ) {
		$user          = get_userdata( $user_id );
		// Надо вывести пацанам страну
		$user_country_get = $user->billing_country;
		$country_arr = WC()->countries->get_countries();
		$full_country_name = $country_arr[$user_country_get];
		$adFLine = $user->billing_address_1;
        $adSLine = $user->billing_address_2;
        $adPostcode = $user->billing_postcode;
        $adCity = $user->billing_city;
        $adCountry = $user->billing_country;
        $adState = $user->billing_state;
		$vatNum = get_user_meta($user_id, b2bking_custom_field_15829);
		$codeNum = get_user_meta($user_id, b2bking_custom_field_15821);
        $userAddress = 'Country: '.$full_country_name.' '.'State: '.$adState.' '.'City: '.$adCity.' '.'Address: '.$adFLine.', '.$adSLine.' '.'Postcode: '.$adPostcode;
		$template_vars = array(
			'%user_login%'    => $user->user_login,
			'%user_email%'    => $user->user_email,
			'%user_address%' => $userAddress,
			'%user_firstname%' => $user->first_name,
        	'%user_lastname%' => $user->last_name,
			'%date_register%' => $this->date,
			'%user_company%' => $user->billing_company,
			'%user_phone%' => $user->billing_phone,
			'%user_vat%' => $vatNum[0],
			'%user_code%' => $codeNum[0],
			'%user_idnum%' => $user_id,
		);

		$channel_name = Channels::getChannelByID( $this->options['wordpress_register_new_user_channel'] );
		$message      = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['wordpress_register_new_user_template'] );
		BOT::sendMessage( $channel_name, $message );
	}

	/**
	 * @param $comment_id
	 * @param $comment_smsect
	 */
	public function new_comment( $comment_id, $comment_smsect ) {

		if ( $comment_smsect->comment_type == 'order_note' ) {
			return;
		}

		if ( $comment_smsect->comment_type == 'edd_payment_note' ) {
			return;
		}

		$template_vars = array(
			'%comment_author%'       => $comment_smsect->comment_author,
			'%comment_author_email%' => $comment_smsect->comment_author_email,
			'%comment_author_url%'   => $comment_smsect->comment_author_url,
			'%comment_author_IP%'    => $comment_smsect->comment_author_IP,
			'%comment_date%'         => $comment_smsect->comment_date,
			'%comment_content%'      => $comment_smsect->comment_content,
			'%comment_url%'			 => get_comment_link($comment_smsect),
		);

		$channel_name = Channels::getChannelByID( $this->options['wordpress_new_comment_channel'] );
		$message      = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['wordpress_new_comment_template'] );
		BOT::sendMessage( $channel_name, $message );
	}

	/**
	 * @param $username_login
	 * @param $username
	 */
	public function login_user( $username_login, $username ) {
		$template_vars = array(
			'%username_login%' => $username->user_login,
			'%display_name%'   => $username->display_name
		);

		$channel_name = Channels::getChannelByID( $this->options['wordpress_user_login_channel'] );
		$message      = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['wordpress_user_login_template'] );
		BOT::sendMessage( $channel_name, $message );
	}

	/**
	 * Load chat feature styles
	 */
	public function enqueue_chat_styles() {
		wp_enqueue_style( 'wptn-emoji-area', WPTN_URL . 'assets/css/emojiarea.css', false, WPTN_VERSION );
		wp_enqueue_style( 'wptn-chat-box', WPTN_URL . 'assets/css/wptn-chatbox-button.css', array(), WPTN_VERSION, 'all' );
		wp_enqueue_style( 'wptn-scroll-bar', WPTN_URL . 'assets/css/scrollbar.css', array(), WPTN_VERSION, 'all' );
	}

	/**
	 * Load chat feature scripts
	 */
	public function enqueue_chat_scripts() {
		wp_enqueue_script( 'wptn-emoji-area', WPTN_URL . 'assets/js/jquery.emojiarea.min.js', array( 'jquery' ), WPTN_VERSION, true );
		wp_enqueue_script( 'wptn-chat-box', WPTN_URL . 'assets/js/wptn-chatbox-scripts.js', array(), WPTN_VERSION, true );
		wp_enqueue_script( 'wptn-scroll-bar', WPTN_URL . 'assets/js/scrollbar.js', array(), WPTN_VERSION, true );

		// Set variables for chat box scripts
		$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://';

		$ajax_url = add_query_arg(
			array(
				'action' => 'wptn_chatbox_send_message'
			),
			admin_url( 'admin-ajax.php', $protocol )
		);

		$variables = array(
			'chat_box_ajaxurl'     => $ajax_url,
			'chat_box_title'       => Option::getOption( 'wordpress_chat_button_box_title' ) ? Option::getOption( 'wordpress_chat_button_box_title' ) : __( 'WP - Telegram', 'wp-telegram-notifications' ),
			'chat_box_you'         => __( 'You', 'wp-telegram-notifications' ),
			'chat_box_wlc_msg'     => Option::getOption( 'wordpress_chat_button_welcome_msg' ) ? Option::getOption( 'wordpress_chat_button_welcome_msg' ) : __( 'Hello, How can i help you?', 'wp-telegram-notifications' ),
			'chat_box_unknown_err' => __( '⚠ Unknown Error! Check your connection and try again.', 'wp-telegram-notifications' ),
		);

		wp_localize_script( 'wptn-chat-box', 'wptn_chat_vars', $variables );
	}

	/**
	 * Load chat content on the footer
	 */
	public function footer_chat_html() {
		include "chat-box-button.php";
	}

	/*
	 * Login Mobile Ajax Process
	 */
	public function wptn_chatbox_send_message() {
		$phone        = isset( $_POST['user_tg_phone'] ) ? $_POST['user_tg_phone'] : '';
		$telegram_id  = isset( $_POST['user_tg_id'] ) ? $_POST['user_tg_id'] : '';
		$user_message = isset( $_POST['user_message'] ) ? $_POST['user_message'] : '';

		//Default variables
		$result = array(
			'error' => 'yes',
			'text'  => '',
		);
		if ( $phone || $telegram_id ) {

			$telegram_id = strpos( $telegram_id, '@' ) !== false ? $telegram_id : '@' . $telegram_id;

			// Set and Send message
			$message = "";
			if ( $telegram_id AND $telegram_id != '@' ) {
				$message .= sprintf( __( '👤 From: %s', 'wp-telegram-notifications' ), $telegram_id ) . PHP_EOL;
			}
			if ( $phone ) {
				$message .= sprintf( __( '📞 Mobile: %s', 'wp-telegram-notifications' ), $phone ) . PHP_EOL;
			}
			$message .= sprintf( __( '💻 IP: %s', 'wp-telegram-notifications' ), self::getRealIpAddr() ) . PHP_EOL;
			$message .= sprintf( __( '✉ Message: %s ', 'wp-telegram-notifications' ), $user_message ) . PHP_EOL;

			$channel_name = Channels::getChannelByID( Option::getOption( 'wordpress_chat_button_channel' ) );

			$res = BOT::sendMessage( $channel_name, $message );

			if ( is_wp_error( $res ) ) {
				$result['text'] = $res->get_error_message();
				$this->json_exit( $result );
			}

			$result['error'] = 'no';
			$result['text']  = Option::getOption( 'wordpress_chat_button_final_msg' ) ? Option::getOption( 'wordpress_chat_button_final_msg' ) : __( 'Thank you for your message!', 'wp-telegram-notifications' );
			$this->json_exit( $result );
		} else {
			$result['text'] = __( 'Unknown Error! Check your connection and try again.', 'wp-telegram-notifications' );
			$this->json_exit( $result );
		}

	}

	/**
	 * Show Json and Exit
	 */
	private function json_exit( $array ) {
		if ( $array['error'] == 'yes' ) {
			$array['text'] = __( '⚠ Error: ', 'wp-telegram-notifications' ) . $array['text'];
		}
		wp_send_json( $array );
		exit;
	}

	/**
	 * Get real user IP address
	 *
	 * @return mixed
	 */
	public static function getRealIpAddr() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) )   //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )   //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

}

new Wordpress();