<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Option {

	/**
	 * Get the whole Plugin Options
	 *
	 * @return mixed|void
	 */
	public static function getOptions() {
		global $wptn_option;

		return $wptn_option;
	}

	/**
	 * Get the only Option that we want
	 *
	 * @param $option_name
	 * @param string $setting_name
	 * @param bool $pro
	 *
	 * @return string
	 */
	public static function getOption( $option_name ) {
		global $wptn_option;

		return isset( $wptn_option[ $option_name ] ) ? $wptn_option[ $option_name ] : '';
	}

	/**
	 * Add an option
	 *
	 * @param $option_name
	 * @param $option_value
	 */
	public static function addOption( $option_name, $option_value ) {
		add_option( $option_name, $option_value );
	}

	/**
	 * Update Option
	 *
	 * @param $key
	 * @param $value
	 * @param bool $pro
	 */
	public static function updateOption( $key, $value ) {

		$setting_name    = 'wptn_settings';
		$options         = self::getOptions();
		$options[ $key ] = $value;

		update_option( $setting_name, $options );
	}
}