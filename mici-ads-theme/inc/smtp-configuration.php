<?php
/**
 * Mici Ads Theme — Email Delivery Configuration
 *
 * Priority: Brevo HTTP API (BREVO_API_KEY) > SMTP (SMTP_HOST).
 * Brevo bypasses blocked SMTP ports on Railway free/hobby tier.
 *
 * Env vars:
 *   BREVO_API_KEY              — Brevo (Sendinblue) API key
 *   MAIL_FROM                  — Sender email address
 *   MAIL_FROM_NAME             — Sender display name (default: Mici Ads)
 *   SMTP_HOST, SMTP_PORT,
 *   SMTP_USER, SMTP_PASS       — Fallback SMTP credentials
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Intercept wp_mail() and send via Brevo HTTP API when configured.
 *
 * Uses the `pre_wp_mail` filter (WP 5.7+). Returns non-null to
 * short-circuit wp_mail() and prevent PHPMailer from running.
 *
 * @param null|bool $result Short-circuit return value.
 * @param array     $atts   wp_mail() arguments.
 * @return null|bool
 */
function mici_brevo_send_mail( $result, $atts ) {
	$api_key = getenv( 'BREVO_API_KEY' );
	if ( ! $api_key ) {
		return null; // Fall through to default wp_mail / SMTP.
	}

	$to      = is_array( $atts['to'] ) ? $atts['to'] : array( $atts['to'] );
	$subject = $atts['subject'] ?? '';
	$message = $atts['message'] ?? '';
	$headers = $atts['headers'] ?? array();

	// Determine content type from headers.
	$is_html = false;
	if ( is_string( $headers ) ) {
		$headers = explode( "\n", $headers );
	}
	foreach ( $headers as $header ) {
		if ( stripos( $header, 'content-type' ) !== false && stripos( $header, 'text/html' ) !== false ) {
			$is_html = true;
		}
	}

	// Build recipients array.
	$recipients = array();
	foreach ( $to as $email ) {
		$clean = trim( $email );
		if ( preg_match( '/<([^>]+)>/', $clean, $m ) ) {
			$clean = $m[1];
		}
		if ( is_email( $clean ) ) {
			$recipients[] = array( 'email' => $clean );
		}
	}

	if ( empty( $recipients ) ) {
		return false;
	}

	$from_email = getenv( 'MAIL_FROM' ) ?: get_option( 'admin_email' );
	$from_name  = getenv( 'MAIL_FROM_NAME' ) ?: 'Mici Ads';

	$body = array(
		'sender'  => array(
			'name'  => $from_name,
			'email' => $from_email,
		),
		'to'      => $recipients,
		'subject' => $subject,
	);

	if ( $is_html ) {
		$body['htmlContent'] = $message;
	} else {
		$body['textContent'] = $message;
	}

	$response = wp_remote_post(
		'https://api.brevo.com/v3/smtp/email',
		array(
			'timeout' => 15,
			'headers' => array(
				'accept'       => 'application/json',
				'content-type' => 'application/json',
				'api-key'      => $api_key,
			),
			'body'    => wp_json_encode( $body ),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( '[Mici Mail] Brevo error: ' . $response->get_error_message() );
		return false;
	}

	$code         = wp_remote_retrieve_response_code( $response );
	$response_body = wp_remote_retrieve_body( $response );

	// Always log for diagnostics.
	error_log( '[Mici Mail] Brevo HTTP ' . $code . ' → ' . $response_body );

	if ( $code >= 200 && $code < 300 ) {
		return true;
	}

	return false;
}
add_filter( 'pre_wp_mail', 'mici_brevo_send_mail', 10, 2 );

/**
 * Diagnostic endpoint: ?mici_test_mail=1 (admin only).
 * Sends a test email to the admin and outputs the result.
 */
function mici_test_mail_endpoint() {
	if ( empty( $_GET['mici_test_mail'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$to      = get_option( 'admin_email' );
	$subject = 'Mici Ads — Test Email ' . gmdate( 'H:i:s' );
	$message = 'If you receive this, email delivery via Brevo is working.';
	$sent    = wp_mail( $to, $subject, $message );

	header( 'Content-Type: text/plain; charset=UTF-8' );
	echo 'wp_mail() returned: ' . ( $sent ? 'TRUE' : 'FALSE' ) . "\n";
	echo 'Sent to: ' . $to . "\n";
	echo 'BREVO_API_KEY set: ' . ( getenv( 'BREVO_API_KEY' ) ? 'yes (' . strlen( getenv( 'BREVO_API_KEY' ) ) . ' chars)' : 'NO' ) . "\n";
	echo 'MAIL_FROM: ' . ( getenv( 'MAIL_FROM' ) ?: '(not set)' ) . "\n";
	exit;
}
add_action( 'template_redirect', 'mici_test_mail_endpoint' );

// -------------------------------------------------------------------------
// SMTP fallback (when BREVO_API_KEY is absent but SMTP_HOST is set)
// -------------------------------------------------------------------------

/**
 * Configure PHPMailer to use SMTP when env vars are set.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
 */
function mici_configure_smtp( $phpmailer ) {
	// Skip if Brevo is active — mail won't reach PHPMailer.
	if ( getenv( 'BREVO_API_KEY' ) ) {
		return;
	}

	$host = getenv( 'SMTP_HOST' );
	$user = getenv( 'SMTP_USER' );
	$pass = getenv( 'SMTP_PASS' );

	if ( ! $host || ! $user || ! $pass ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = $host;
	$phpmailer->Port       = getenv( 'SMTP_PORT' ) ? (int) getenv( 'SMTP_PORT' ) : 587;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = $user;
	$phpmailer->Password   = $pass;
	$phpmailer->SMTPSecure = getenv( 'SMTP_SECURE' ) ?: 'tls';

	$from      = getenv( 'MAIL_FROM' );
	$from_name = getenv( 'MAIL_FROM_NAME' );
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
function mici_mail_from_email( $email ) {
	$from = getenv( 'MAIL_FROM' );
	return $from ? $from : $email;
}
add_filter( 'wp_mail_from', 'mici_mail_from_email' );

/**
 * Override default WordPress from name.
 *
 * @param string $name Default from name.
 * @return string
 */
function mici_mail_from_name( $name ) {
	$from_name = getenv( 'MAIL_FROM_NAME' );
	return $from_name ? $from_name : 'Mici Ads';
}
add_filter( 'wp_mail_from_name', 'mici_mail_from_name' );
