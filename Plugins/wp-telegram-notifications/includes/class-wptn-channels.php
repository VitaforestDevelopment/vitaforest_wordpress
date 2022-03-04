<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Channels {

	/**
	 * Add new channel
	 *
	 * @param $channel_name
	 *
	 * @return mixed
	 */
	public static function addChannel( $channel_name ) {
		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->prefix . 'tn_channels',
			array(
				'channel_name' => $channel_name,
			)
		);

		return $result;
	}

	/**
	 * Get Channels
	 */
	public static function getChannels() {
		global $wpdb;

		return $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'tn_channels`' );
	}

	/**
	 * Get channel by ID
	 *
	 * @param $channel_id
	 *
	 * @return mixed
	 */
	public static function getChannelByID( $channel_id ) {
		global $wpdb;

		$channel = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}tn_channels` WHERE ID = {$channel_id}" );
		if ( $channel ) {
			return $channel->channel_name;
		}
	}
}