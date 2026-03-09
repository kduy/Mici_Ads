<?php
/**
 * Mici Ads Theme — Email Confirmation System
 *
 * Generates confirmation tokens on signup, sends activation emails,
 * and handles the ?mici_confirm=TOKEN endpoint to activate accounts.
 * Tokens expire after 24 hours. Single-use.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate a confirmation token and store it on the user.
 *
 * @param int $user_id WP user ID.
 * @return string 32-char hex token.
 */
function mici_generate_confirmation_token( $user_id ) {
	$token = bin2hex( random_bytes( 16 ) );
	update_user_meta( $user_id, '_mici_confirm_token', $token );
	update_user_meta( $user_id, '_mici_confirm_expires', time() + DAY_IN_SECONDS );
	update_user_meta( $user_id, '_mici_email_confirmed', '0' );
	return $token;
}

/**
 * Send confirmation email to a newly registered user.
 *
 * @param int    $user_id WP user ID.
 * @param string $token   Confirmation token.
 */
function mici_send_confirmation_email( $user_id, $token ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}

	$confirm_url = add_query_arg(
		array( 'mici_confirm' => $token ),
		home_url( '/' )
	);

	$subject = __( 'Xác nhận tài khoản Mici Ads', 'mici-ads' );
	$message = sprintf(
		/* translators: 1: user display name, 2: confirmation URL */
		__(
			"Xin chào %1\$s,\n\n" .
			"Cảm ơn bạn đã đăng ký tài khoản tại Mici Ads.\n\n" .
			"Vui lòng nhấn vào liên kết bên dưới để xác nhận email:\n%2\$s\n\n" .
			"Liên kết này có hiệu lực trong 24 giờ.\n\n" .
			"Trân trọng,\nMici Ads Team",
			'mici-ads'
		),
		$user->display_name,
		$confirm_url
	);

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	wp_mail( $user->user_email, $subject, $message, $headers );
}

/**
 * Handle email confirmation via ?mici_confirm=TOKEN query param.
 * Activates the account if token is valid and not expired.
 */
function mici_handle_email_confirmation() {
	if ( empty( $_GET['mici_confirm'] ) ) {
		return;
	}

	$token = sanitize_text_field( wp_unslash( $_GET['mici_confirm'] ) );

	// Find user by token.
	$users = get_users(
		array(
			'meta_key'   => '_mici_confirm_token',
			'meta_value' => $token,
			'number'     => 1,
		)
	);

	if ( empty( $users ) ) {
		wp_die(
			esc_html__( 'Liên kết xác nhận không hợp lệ hoặc đã được sử dụng.', 'mici-ads' ),
			esc_html__( 'Xác nhận email', 'mici-ads' ),
			array( 'response' => 400 )
		);
	}

	$user    = $users[0];
	$expires = (int) get_user_meta( $user->ID, '_mici_confirm_expires', true );

	if ( time() > $expires ) {
		wp_die(
			esc_html__( 'Liên kết xác nhận đã hết hạn. Vui lòng đăng ký lại.', 'mici-ads' ),
			esc_html__( 'Xác nhận email', 'mici-ads' ),
			array( 'response' => 410 )
		);
	}

	// Activate: set confirmed flag, remove token, ensure role is registered.
	update_user_meta( $user->ID, '_mici_email_confirmed', '1' );
	delete_user_meta( $user->ID, '_mici_confirm_token' );
	delete_user_meta( $user->ID, '_mici_confirm_expires' );

	// Only upgrade role if still inactive (admin may have changed it).
	if ( in_array( 'mici_inactive', (array) $user->roles, true ) ) {
		$user->set_role( 'mici_registered' );
	}

	// Redirect to auth page with success message.
	$auth_page = mici_get_auth_page_url();
	$redirect  = add_query_arg( 'mici_confirmed', '1', $auth_page ? $auth_page : home_url( '/' ) );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'template_redirect', 'mici_handle_email_confirmation' );

/**
 * Block unconfirmed users from logging in.
 *
 * @param WP_User|WP_Error $user     User object or error.
 * @param string           $password Password (unused).
 * @return WP_User|WP_Error
 */
function mici_block_unconfirmed_login( $user, $password ) {
	if ( is_wp_error( $user ) ) {
		return $user;
	}

	// Skip check for admins and editors.
	if ( array_intersect( array( 'administrator', 'editor' ), (array) $user->roles ) ) {
		return $user;
	}

	$confirmed = get_user_meta( $user->ID, '_mici_email_confirmed', true );
	if ( '1' !== $confirmed ) {
		return new WP_Error(
			'mici_unconfirmed',
			__( 'Vui lòng xác nhận email trước khi đăng nhập. Kiểm tra hộp thư của bạn.', 'mici-ads' )
		);
	}

	return $user;
}
add_filter( 'wp_authenticate_user', 'mici_block_unconfirmed_login', 20, 2 );
