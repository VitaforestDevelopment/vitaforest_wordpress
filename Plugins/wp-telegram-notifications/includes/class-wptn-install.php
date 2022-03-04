<?php

namespace WPTN;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

Class Install {

	/**
	 * Creating plugin tables
	 *
	 * @param Not param
	 */
	static function install() {
		self::table_sql();

		if ( is_admin() ) {
			self::upgrade();
		}

	}

	/**
	 * Table SQL
	 *
	 * @param Not param
	 */
	public static function table_sql() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		$outbox_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}tn_outbox(
						ID int(10) NOT NULL auto_increment,
						date DATETIME,
						channel_name VARCHAR(255) NOT NULL,
						message TEXT NOT NULL,
						message_id VARCHAR(20) NOT NULL,
						PRIMARY KEY(ID)) $charset_collate;";

		$channels_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}tn_channels(
						ID int(10) NOT NULL auto_increment,
						channel_name VARCHAR(255) NOT NULL,
						PRIMARY KEY(ID)) $charset_collate;";

		dbDelta( $outbox_sql );
		dbDelta( $channels_sql );

		add_option( 'wp_telegram_notifications_db_version', WPTN_VERSION );
		delete_option( 'wp_notification_new_wp_version' );
	}

	/**
	 * Upgrade plugin requirements if needed
	 */
	static function upgrade() {
		if ( WPTN_VERSION > '1.0.4' ) {

			global $wpdb;

			$table_name = $wpdb->prefix . 'tn_outbox';
			// Change charset outbox table to utf8mb4 if not
			$result = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
				DB_NAME, $table_name, 'message'
			) );

			if ( $result->COLLATION_NAME != $wpdb->collate ) {
				$wpdb->query( "ALTER TABLE {$table_name} CONVERT TO CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}" );
			}

		}
	}

}