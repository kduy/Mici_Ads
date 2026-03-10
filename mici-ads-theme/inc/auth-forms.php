<?php
/**
 * Mici Ads Theme — Auth Form Handlers & Helpers
 *
 * POST handlers for signup/login + utility functions.
 * Rendering is in auth-shortcode-renderer.php.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// Helper: get auth page URL (page using page-auth.php template)
// -------------------------------------------------------------------------

/**
 * Get the URL of the page using the Auth page template.
 *
 * @return string|false Page URL or false.
 */
function mici_get_auth_page_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-auth.php',
			'number'     => 1,
		)
	);
	return ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : false;
}

// -------------------------------------------------------------------------
// Phone number normalization
// -------------------------------------------------------------------------

/**
 * Normalize phone number: strip spaces/dashes, keep digits and leading +.
 *
 * @param string $phone Raw phone input.
 * @return string Normalized phone.
 */
function mici_normalize_phone( $phone ) {
	return preg_replace( '/[^\d+]/', '', trim( $phone ) );
}

// -------------------------------------------------------------------------
// Signup handler (POST)
// -------------------------------------------------------------------------

/**
 * Process signup form submission.
 */
function mici_handle_signup() {
	if ( ! isset( $_POST['mici_signup_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_signup_nonce'] ) ), 'mici_signup' ) ) {
		return;
	}

	$email    = isset( $_POST['mici_email'] ) ? sanitize_email( wp_unslash( $_POST['mici_email'] ) ) : '';
	$phone    = isset( $_POST['mici_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_phone'] ) ) : '';
	$name     = isset( $_POST['mici_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_name'] ) ) : '';
	$password = isset( $_POST['mici_password'] ) ? $_POST['mici_password'] : ''; // phpcs:ignore -- raw password.

	$errors = array();

	// Validate required fields.
	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors[] = __( 'Vui lòng nhập email hợp lệ.', 'mici-ads' );
	}
	if ( empty( $password ) || strlen( $password ) < 6 ) {
		$errors[] = __( 'Mật khẩu phải có ít nhất 6 ký tự.', 'mici-ads' );
	}
	if ( empty( $name ) ) {
		$errors[] = __( 'Vui lòng nhập họ tên.', 'mici-ads' );
	}

	// Check duplicate email.
	if ( empty( $errors ) && email_exists( $email ) ) {
		$errors[] = __( 'Email này đã được đăng ký.', 'mici-ads' );
	}

	// Check duplicate phone (if provided).
	if ( ! empty( $phone ) ) {
		$phone = mici_normalize_phone( $phone );
		$existing = get_users(
			array(
				'meta_key'   => '_mici_phone',
				'meta_value' => $phone,
				'number'     => 1,
			)
		);
		if ( ! empty( $existing ) ) {
			$errors[] = __( 'Số điện thoại này đã được đăng ký.', 'mici-ads' );
		}
	}

	if ( ! empty( $errors ) ) {
		// Store errors in transient for display after redirect.
		set_transient( 'mici_signup_errors_' . wp_hash( $email ), $errors, 60 );
		$redirect = add_query_arg(
			array( 'tab' => 'signup', 'email' => rawurlencode( $email ) ),
			mici_get_auth_page_url() ?: home_url( '/' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	// Create user with email as username (sanitized).
	$username = sanitize_user( $email, true );
	$user_id  = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		set_transient( 'mici_signup_errors_' . wp_hash( $email ), array( $user_id->get_error_message() ), 60 );
		$redirect = add_query_arg( array( 'tab' => 'signup' ), mici_get_auth_page_url() ?: home_url( '/' ) );
		wp_safe_redirect( $redirect );
		exit;
	}

	// Set role to inactive until email confirmed.
	$user = new WP_User( $user_id );
	$user->set_role( 'mici_inactive' );

	// Store display name and phone.
	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
			'first_name'   => $name,
		)
	);
	if ( ! empty( $phone ) ) {
		update_user_meta( $user_id, '_mici_phone', $phone );
	}

	// Generate token and send confirmation email.
	$token = mici_generate_confirmation_token( $user_id );
	mici_send_confirmation_email( $user_id, $token );

	// Redirect with success.
	$redirect = add_query_arg( 'mici_signup_success', '1', mici_get_auth_page_url() ?: home_url( '/' ) );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_nopriv_mici_signup', 'mici_handle_signup' );
add_action( 'admin_post_mici_signup', 'mici_handle_signup' );

// -------------------------------------------------------------------------
// Login handler (POST)
// -------------------------------------------------------------------------

/**
 * Process login form submission.
 */
function mici_handle_login() {
	if ( ! isset( $_POST['mici_login_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_login_nonce'] ) ), 'mici_login' ) ) {
		return;
	}

	$credential = isset( $_POST['mici_credential'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_credential'] ) ) : '';
	$password   = isset( $_POST['mici_password'] ) ? $_POST['mici_password'] : ''; // phpcs:ignore

	// Resolve credential: could be email or phone number.
	$username = $credential;
	if ( ! is_email( $credential ) ) {
		// Try phone number lookup.
		$phone = mici_normalize_phone( $credential );
		$users = get_users(
			array(
				'meta_key'   => '_mici_phone',
				'meta_value' => $phone,
				'number'     => 1,
			)
		);
		if ( ! empty( $users ) ) {
			$username = $users[0]->user_login;
		}
	}

	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => true,
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		set_transient( 'mici_login_error_' . wp_hash( $credential ), $user->get_error_message(), 60 );
		$redirect = add_query_arg(
			array( 'tab' => 'login', 'credential' => rawurlencode( $credential ) ),
			mici_get_auth_page_url() ?: home_url( '/' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	// Redirect to templates page or referrer.
	$redirect = isset( $_POST['mici_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['mici_redirect'] ) ) : '';
	if ( empty( $redirect ) ) {
		$redirect = mici_get_templates_page_url();
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_nopriv_mici_login', 'mici_handle_login' );
add_action( 'admin_post_mici_login', 'mici_handle_login' );

// -------------------------------------------------------------------------
// Templates page URL helper
// -------------------------------------------------------------------------

/**
 * Get the URL of the templates page.
 *
 * @return string URL.
 */
function mici_get_templates_page_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates.php',
			'number'     => 1,
		)
	);
	return ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : home_url( '/' );
}
