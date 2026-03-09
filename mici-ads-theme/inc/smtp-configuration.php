<?php
/**
 * Mici Ads Theme — SMTP Configuration via Environment Variables
 *
 * Hooks into PHPMailer to route wp_mail() through an SMTP server.
 * Required env vars: SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS.
 * Optional: SMTP_FROM, SMTP_FROM_NAME, SMTP_SECURE (tls|ssl).
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Configure PHPMailer to use SMTP when env vars are set.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
 */
function mici_configure_smtp( $phpmailer ) {
	$host = getenv( 'SMTP_HOST' );
	$port = getenv( 'SMTP_PORT' );
	$user = getenv( 'SMTP_USER' );
	$pass = getenv( 'SMTP_PASS' );

	// Only configure if all required vars are present.
	if ( ! $host || ! $user || ! $pass ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = $host;
	$phpmailer->Port       = $port ? (int) $port : 587;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = $user;
	$phpmailer->Password   = $pass;
	$phpmailer->SMTPSecure = getenv( 'SMTP_SECURE' ) ?: 'tls';

	// Optional from address override.
	$from      = getenv( 'SMTP_FROM' );
	$from_name = getenv( 'SMTP_FROM_NAME' );
	if ( $from ) {
		$phpmailer->From = $from;
	}
	if ( $from_name ) {
		$phpmailer->FromName = $from_name;
	}
}
add_action( 'phpmailer_init', 'mici_configure_smtp' );

/**
 * Override default WordPress from address.
 *
 * @param string $email Default from email.
 * @return string
 */
function mici_smtp_from_email( $email ) {
	$from = getenv( 'SMTP_FROM' );
	return $from ? $from : $email;
}
add_filter( 'wp_mail_from', 'mici_smtp_from_email' );

/**
 * Override default WordPress from name.
 *
 * @param string $name Default from name.
 * @return string
 */
function mici_smtp_from_name( $name ) {
	$from_name = getenv( 'SMTP_FROM_NAME' );
	return $from_name ? $from_name : 'Mici Ads';
}
add_filter( 'wp_mail_from_name', 'mici_smtp_from_name' );
