<?php
/**
 * Mici Ads Theme — Password Reset Customization
 *
 * Custom forgot/reset password forms on the auth page.
 * Uses WP's native token system (get_password_reset_key / check_password_reset_key).
 * Emails sent through existing Brevo integration.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// Customize WP password reset email content
// -------------------------------------------------------------------------

/**
 * Replace default WP reset email with Vietnamese branded version.
 *
 * @param string  $message    Default message.
 * @param string  $key        Reset key.
 * @param string  $user_login User login name.
 * @param WP_User $user_data  User object.
 * @return string Custom message.
 */
function mici_custom_reset_email_message( $message, $key, $user_login, $user_data ) {
	$auth_url  = mici_get_auth_page_url() ?: home_url( '/' );
	$reset_url = add_query_arg( array(
		'tab'   => 'reset',
		'key'   => $key,
		'login' => rawurlencode( $user_login ),
	), $auth_url );

	return sprintf(
		__( "Xin chào %1\$s,\n\nBạn đã yêu cầu đặt lại mật khẩu tại Mici Ads.\n\nNhấn liên kết sau để đặt mật khẩu mới:\n%2\$s\n\nLiên kết có hiệu lực trong 24 giờ.\nNếu bạn không yêu cầu, hãy bỏ qua email này.\n\nTrân trọng,\nMici Ads Team", 'mici-ads' ),
		$user_data->display_name,
		$reset_url
	);
}
add_filter( 'retrieve_password_message', 'mici_custom_reset_email_message', 10, 4 );

/**
 * Customize password reset email subject.
 */
function mici_custom_reset_email_title() {
	return __( 'Đặt lại mật khẩu — Mici Ads', 'mici-ads' );
}
add_filter( 'retrieve_password_title', 'mici_custom_reset_email_title' );

// -------------------------------------------------------------------------
// Redirect wp-login.php?action=lostpassword to our auth page
// -------------------------------------------------------------------------

/**
 * Redirect WP lost password page to custom auth page.
 */
function mici_redirect_lost_password() {
	if ( isset( $_GET['action'] ) && 'lostpassword' === $_GET['action'] ) {
		$auth_url = mici_get_auth_page_url();
		if ( $auth_url ) {
			wp_safe_redirect( add_query_arg( 'tab', 'forgot', $auth_url ) );
			exit;
		}
	}
}
add_action( 'login_init', 'mici_redirect_lost_password' );

// -------------------------------------------------------------------------
// POST handler: request password reset
// -------------------------------------------------------------------------

/**
 * Handle forgot password form submission.
 */
function mici_handle_forgot_password() {
	if ( ! isset( $_POST['mici_forgot_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_forgot_nonce'] ) ), 'mici_forgot_password' ) ) {
		wp_die( __( 'Phiên làm việc hết hạn.', 'mici-ads' ) );
	}

	$email       = isset( $_POST['mici_email'] ) ? sanitize_email( wp_unslash( $_POST['mici_email'] ) ) : '';
	$auth_url    = mici_get_auth_page_url() ?: home_url( '/' );

	if ( empty( $email ) || ! is_email( $email ) ) {
		set_transient( 'mici_forgot_error', __( 'Vui lòng nhập email hợp lệ.', 'mici-ads' ), 60 );
		wp_safe_redirect( add_query_arg( 'tab', 'forgot', $auth_url ) );
		exit;
	}

	$user = get_user_by( 'email', $email );
	// Always show success (prevent email enumeration).
	if ( $user ) {
		$key = get_password_reset_key( $user );
		if ( ! is_wp_error( $key ) ) {
			// retrieve_password_message filter will customize the email content.
			wp_mail(
				$email,
				apply_filters( 'retrieve_password_title', '' ),
				apply_filters( 'retrieve_password_message', '', $key, $user->user_login, $user )
			);
		}
	}

	wp_safe_redirect( add_query_arg( array( 'tab' => 'forgot', 'sent' => '1' ), $auth_url ) );
	exit;
}
add_action( 'admin_post_nopriv_mici_forgot_password', 'mici_handle_forgot_password' );
add_action( 'admin_post_mici_forgot_password', 'mici_handle_forgot_password' );

// -------------------------------------------------------------------------
// POST handler: reset password with token
// -------------------------------------------------------------------------

/**
 * Handle reset password form submission.
 */
function mici_handle_reset_password() {
	if ( ! isset( $_POST['mici_reset_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_reset_nonce'] ) ), 'mici_reset_password' ) ) {
		wp_die( __( 'Phiên làm việc hết hạn.', 'mici-ads' ) );
	}

	$key       = isset( $_POST['mici_key'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_key'] ) ) : '';
	$login     = isset( $_POST['mici_login'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_login'] ) ) : '';
	$password  = isset( $_POST['mici_password'] ) ? $_POST['mici_password'] : ''; // phpcs:ignore
	$confirm   = isset( $_POST['mici_password_confirm'] ) ? $_POST['mici_password_confirm'] : ''; // phpcs:ignore
	$auth_url  = mici_get_auth_page_url() ?: home_url( '/' );

	$errors = array();

	if ( empty( $password ) || strlen( $password ) < 6 ) {
		$errors[] = __( 'Mật khẩu phải có ít nhất 6 ký tự.', 'mici-ads' );
	}
	if ( $password !== $confirm ) {
		$errors[] = __( 'Xác nhận mật khẩu không khớp.', 'mici-ads' );
	}

	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		$errors[] = __( 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.', 'mici-ads' );
	}

	if ( ! empty( $errors ) ) {
		set_transient( 'mici_reset_errors', $errors, 60 );
		wp_safe_redirect( add_query_arg( array(
			'tab'   => 'reset',
			'key'   => $key,
			'login' => rawurlencode( $login ),
		), $auth_url ) );
		exit;
	}

	reset_password( $user, $password );
	wp_safe_redirect( add_query_arg( array( 'tab' => 'login', 'reset_success' => '1' ), $auth_url ) );
	exit;
}
add_action( 'admin_post_nopriv_mici_reset_password', 'mici_handle_reset_password' );
add_action( 'admin_post_mici_reset_password', 'mici_handle_reset_password' );
