<?php

namespace WPTN;

use TelegramBot\Api\BotApi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class BOT {

	/**
	 * Get Bot API Status
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	public static function getMe() {
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot      = new BotApi( Option::getOption( 'bot_api_token' ) );
			$response = $bot->getMe();

			return json_decode( $response->toJson() );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'error', $e->getMessage() );
		}
	}

	/**
	 * Get information about a member of a chat.
	 *
	 * @param $chat_id
	 *
	 * @return mixed|null|\WP_Error
	 */
	public static function getChatMember( $chat_id ) {
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot      = new BotApi( Option::getOption( 'bot_api_token' ) );
			$response = $bot->call( 'getChatMembersCount', [ 'chat_id' => $chat_id ] );

			return $response;
		} catch ( \Exception $e ) {
			return null;
		}
	}


	/**
	 * Use this method to get a list of administrators in a chat
	 *
	 * @param $chat_id
	 *
	 * @return mixed|null|\WP_Error
	 */
	public static function getChatAdministrators( $chat_id ) {
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot      = new BotApi( Option::getOption( 'bot_api_token' ) );
			$response = $bot->call( 'getChatAdministrators', [ 'chat_id' => $chat_id ] );

			return $response;
		} catch ( \Exception $e ) {
			return null;
		}
	}


	/**
	 * Send message to channel
	 *
	 * @param $channel_name
	 * @param $message
	 * @param $parseMode
	 *
	 * @return \TelegramBot\Api\Types\Message|\WP_Error
	 */
	public static function sendMessage( $channel_name, $message, $parseMode = null ) {
		global $wpdb;

		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot      = new BotApi( Option::getOption( 'bot_api_token' ) );
			$response = $bot->sendMessage( $channel_name, self::modify_messgae( $message ), $parseMode );

			// Insert to database
			$wpdb->insert(
				$wpdb->prefix . 'tn_outbox',
				array(
					'date'         => WPTN_CURRENT_DATE,
					'channel_name' => $channel_name,
					'message'      => self::modify_messgae( $message ),
					'message_id'   => $response->getMessageId()
				)
			);

			return $response;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'error', $e->getMessage() );
		}
	}

	/**
	 * Send photo to channel
	 *
	 * @param $channel_name
	 * @param $photo
	 * @param $message
	 * @param bool $after_text
	 *
	 * @return \TelegramBot\Api\Types\Message|\WP_Error
	 */
	public static function sendPhoto( $channel_name, $photo, $message, $after_text = false ) {
		global $wpdb;
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot = new BotApi( Option::getOption( 'bot_api_token' ) );

			if ( $after_text ) {
				$message  = $message . '<a href="' . $photo . '"> ⁠</a>';
				$response = $bot->sendMessage( $channel_name, self::modify_messgae( $message ), "HTML" );
			} else {
				$response = $bot->sendPhoto( $channel_name, $photo, self::modify_messgae( $message ) );
			}

			// Insert to database
			$wpdb->insert(
				$wpdb->prefix . 'tn_outbox',
				array(
					'date'         => WPTN_CURRENT_DATE,
					'channel_name' => $channel_name,
					'message'      => self::modify_messgae( $message ),
					'message_id'   => $response->getMessageId()
				)
			);

			return $response;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'error', $e->getMessage() );
		}
	}

	/**
	 * Send video to channel
	 *
	 * @param $channel_name
	 * @param $video
	 * @param $message
	 * @param bool $after_text
	 *
	 * @return \TelegramBot\Api\Types\Message|\WP_Error
	 */
	public static function sendVideo( $channel_name, $video, $message, $after_text = false ) {
		global $wpdb;
		if ( ! Option::getOption( 'bot_api_token' ) ) {
			return new \WP_Error( 'error', __( 'API Token does not exists', 'wp-telegram-notifications' ) );
		}

		try {
			$bot = new BotApi( Option::getOption( 'bot_api_token' ) );

			if ( $after_text ) {
				$message  = $message . '<a href="' . $video . '"> ⁠</a>';
				$response = $bot->sendMessage( $channel_name, self::modify_messgae( $message ), "HTML" );
			} else {
				$response = $bot->sendVideo( $channel_name, $video, null, self::modify_messgae( $message ) );
			}

			// Insert to database
			$wpdb->insert(
				$wpdb->prefix . 'tn_outbox',
				array(
					'date'         => WPTN_CURRENT_DATE,
					'channel_name' => $channel_name,
					'message'      => self::modify_messgae( $message ),
					'message_id'   => $response->getMessageId()
				)
			);

			return $response;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'error', $e->getMessage() );
		}
	}

	private static function modify_messgae( $message ) {
		/**
		 * Modify text message
		 *
		 * @param string $message | text message.
		 */
		return apply_filters( 'wp_telegram_msg', $message );
	}
}
