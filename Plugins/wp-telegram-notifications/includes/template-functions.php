<?php

use WPTN\BOT;

/**
 * Send Message.
 *
 * @param string $channel_name | Channel name OR Channel ID or Group ID
 * @param string $msg
 *
 * @return string | WP_Error
 */
function wp_telegram_send_message( $channel_name, $msg ) {
	return BOT::sendMessage( $channel_name, $msg );
}

/**
 * Send Photo.
 *
 * @param string $channel_name | Channel name OR Channel ID or Group ID
 * @param string $image | Photo URL
 * @param string $msg | Photo URL
 * @param bool $after_text
 *
 * @return string | WP_Error
 */
function wp_telegram_send_photo( $channel_name, $image, $msg, $after_text = false ) {
	return BOT::sendPhoto( $channel_name, $image, $msg, $after_text );
}

/**
 * Send Video.
 *
 * @param string $channel_name | Channel name OR Channel ID or Group ID
 * @param string $video | Video URL
 * @param string $msg | Photo URL
 * @param bool $after_text
 *
 * @return string | WP_Error
 */
function wp_telegram_send_video( $channel_name, $video, $msg, $after_text = false ) {
	return BOT::sendVideo( $channel_name, $video, $msg, $after_text );
}