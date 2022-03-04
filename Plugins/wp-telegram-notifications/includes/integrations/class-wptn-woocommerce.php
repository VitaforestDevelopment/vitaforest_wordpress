<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WooCommerce {

	public $options;

	public function __construct() {
		$this->options = Option::getOptions();

		if ( isset( $this->options['woocommerce_new_product'] ) ) {
			add_action( 'publish_product', array( $this, 'notification_new_product' ) );
		}

		if ( isset( $this->options['woocommerce_new_order'] ) ) {
			add_action( 'woocommerce_new_order', array( $this, 'notification_new_order' ) );
		}

		if ( isset( $this->options['woocommerce_stock'] ) ) {
			add_action( 'woocommerce_low_stock', array( $this, 'notification_low_stock' ) );
		}
	}

	/**
	 * WooCommerce notification new product
	 *
	 * @param $post_ID
	 */
	public function notification_new_product( $post_ID ) {
		$template_vars = array(
			'%product_title%' => get_the_title( $post_ID ),
			'%product_url%'   => wp_get_shortlink( $post_ID ),
			'%product_date%'  => get_post_time( 'Y-m-d', true, $post_ID, true ),
			'%product_price%' => $_REQUEST['_regular_price']
		);
		$channel_name  = Channels::getChannelByID( $this->options['woocommerce_new_product_channel'] );
		$message       = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['woocommerce_new_product_template'] );
		BOT::sendMessage( $channel_name, $message );
	}

	/**
	 * WooCommerce notification new order
	 *
	 * @param $order_id
	 */
	public function notification_new_order( $order_id ) {
		$order         = new \WC_Order( $order_id );
		$template_vars = array(
			'%billing_first_name%' => $order->get_billing_first_name(),
			'%billing_company%'    => $order->get_billing_company(),
			'%billing_address%'    => ( $order->get_billing_address_1() == "" ? $order->get_billing_address_2() : $order->get_billing_address_1() ),
			'%billing_phone%'      => wp_strip_all_tags( $order->get_billing_phone() ),
			'%billing_email%'      => wp_strip_all_tags( $order->get_billing_email() ),
			'%order_total%'        => wp_strip_all_tags( $order->get_formatted_order_total() ),
			'%order_id%'           => $order_id,
			'%order_number%'       => $order->get_order_number(),
			'%status%'             => $order->get_status(),
			'%customer_username%'	=> ($order->get_user() ? $order->get_user()->user_login : ''),
			'%customer_email%'	=> ($order->get_user() ? $order->get_user()->user_email : ''),
		);
		$channel_name  = Channels::getChannelByID( $this->options['woocommerce_new_order_channel'] );
		$message       = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['woocommerce_new_order_template'] );
		BOT::sendMessage( $channel_name, $message );
	}

	/**
	 * WooCommerce notification low stock
	 *
	 * @param $stock
	 */
	public function notification_low_stock( $stock ) {
		$template_vars = array(
			'%product_id%'   => $stock->id,
			'%product_name%' => $stock->post->post_title
		);
		$channel_name  = Channels::getChannelByID( $this->options['woocommerce_stock_channel'] );
		$message       = str_replace( array_keys( $template_vars ), array_values( $template_vars ), $this->options['woocommerce_stock_template'] );
		BOT::sendMessage( $channel_name, $message );
	}
}

new WooCommerce();