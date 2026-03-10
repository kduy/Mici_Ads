<?php
/**
 * Mici Ads Theme — Profile Form Handler
 *
 * Shortcode [mici_profile] renders tabbed profile (Info / Liked Designs).
 * POST handler updates name, phone (unique), email (unique + confirmation).
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// Helper: get profile page URL
// -------------------------------------------------------------------------

/**
 * Get URL of the page using page-profile.php template.
 *
 * @return string|false Page URL or false.
 */
function mici_get_profile_page_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-profile.php',
		'number'     => 1,
	) );
	return ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : false;
}

// -------------------------------------------------------------------------
// Unique validation helpers
// -------------------------------------------------------------------------

/**
 * Check if email is taken by another user.
 *
 * @param string $email           Email to check.
 * @param int    $exclude_user_id Current user ID to exclude.
 * @return bool True if taken.
 */
function mici_is_email_taken( $email, $exclude_user_id ) {
	$existing = email_exists( $email );
	return $existing && $existing !== $exclude_user_id;
}

/**
 * Check if phone is taken by another user.
 *
 * @param string $phone           Normalized phone.
 * @param int    $exclude_user_id Current user ID to exclude.
 * @return bool True if taken.
 */
function mici_is_phone_taken( $phone, $exclude_user_id ) {
	$users = get_users( array(
		'meta_key'   => '_mici_phone',
		'meta_value' => $phone,
		'number'     => 1,
		'exclude'    => array( $exclude_user_id ),
	) );
	return ! empty( $users );
}

// -------------------------------------------------------------------------
// POST handler: update profile
// -------------------------------------------------------------------------

/**
 * Process profile update form submission (PRG pattern).
 */
function mici_handle_profile_update() {
	if ( ! isset( $_POST['mici_profile_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_profile_nonce'] ) ), 'mici_update_profile' ) ) {
		wp_die( __( 'Phiên làm việc hết hạn.', 'mici-ads' ) );
	}

	$user    = wp_get_current_user();
	$user_id = $user->ID;
	$redirect_base = mici_get_profile_page_url() ?: home_url( '/' );

	$name  = isset( $_POST['mici_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_name'] ) ) : '';
	$email = isset( $_POST['mici_email'] ) ? sanitize_email( wp_unslash( $_POST['mici_email'] ) ) : '';
	$phone = isset( $_POST['mici_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_phone'] ) ) : '';

	$errors = array();

	// Validate name.
	if ( empty( $name ) ) {
		$errors[] = __( 'Vui lòng nhập họ tên.', 'mici-ads' );
	}

	// Validate email.
	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors[] = __( 'Vui lòng nhập email hợp lệ.', 'mici-ads' );
	} elseif ( mici_is_email_taken( $email, $user_id ) ) {
		$errors[] = __( 'Email này đã được sử dụng bởi tài khoản khác.', 'mici-ads' );
	}

	// Validate phone (unique if provided).
	if ( ! empty( $phone ) ) {
		$phone = mici_normalize_phone( $phone );
		if ( mici_is_phone_taken( $phone, $user_id ) ) {
			$errors[] = __( 'Số điện thoại này đã được sử dụng bởi tài khoản khác.', 'mici-ads' );
		}
	}

	if ( ! empty( $errors ) ) {
		set_transient( 'mici_profile_errors_' . $user_id, $errors, 60 );
		wp_safe_redirect( $redirect_base );
		exit;
	}

	// Update name.
	wp_update_user( array(
		'ID'           => $user_id,
		'display_name' => $name,
		'first_name'   => $name,
	) );

	// Update phone.
	if ( ! empty( $phone ) ) {
		update_user_meta( $user_id, '_mici_phone', $phone );
	} else {
		delete_user_meta( $user_id, '_mici_phone' );
	}

	// Handle email change.
	$email_msg = '';
	if ( strtolower( $email ) !== strtolower( $user->user_email ) ) {
		// Store pending email, send confirmation to NEW address.
		update_user_meta( $user_id, '_mici_pending_email', $email );
		$token = mici_generate_confirmation_token( $user_id );
		mici_send_email_change_confirmation( $user_id, $email, $token );
		$email_msg = 'pending';
	}

	$args = array( 'mici_profile_updated' => '1' );
	if ( $email_msg ) {
		$args['email_pending'] = '1';
	}
	wp_safe_redirect( add_query_arg( $args, $redirect_base ) );
	exit;
}
add_action( 'admin_post_mici_update_profile', 'mici_handle_profile_update' );

/**
 * Send confirmation email to the new email address.
 *
 * @param int    $user_id  User ID.
 * @param string $new_email New email address.
 * @param string $token     Confirmation token.
 */
function mici_send_email_change_confirmation( $user_id, $new_email, $token ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}

	$confirm_url = add_query_arg( array(
		'mici_confirm'       => $token,
		'mici_email_change'  => '1',
	), home_url( '/' ) );

	$subject = __( 'Xác nhận thay đổi email — Mici Ads', 'mici-ads' );
	$message = sprintf(
		__( "Xin chào %1\$s,\n\nBạn đã yêu cầu thay đổi email sang: %2\$s\n\nVui lòng nhấn liên kết sau để xác nhận:\n%3\$s\n\nLiên kết có hiệu lực trong 24 giờ.\n\nTrân trọng,\nMici Ads Team", 'mici-ads' ),
		$user->display_name,
		$new_email,
		$confirm_url
	);

	wp_mail( $new_email, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
}

/**
 * Handle email change confirmation via ?mici_confirm=TOKEN&mici_email_change=1.
 */
function mici_handle_email_change_confirmation() {
	if ( empty( $_GET['mici_confirm'] ) || empty( $_GET['mici_email_change'] ) ) {
		return;
	}

	$token = sanitize_text_field( wp_unslash( $_GET['mici_confirm'] ) );
	$users = get_users( array(
		'meta_key'   => '_mici_confirm_token',
		'meta_value' => $token,
		'number'     => 1,
	) );

	if ( empty( $users ) ) {
		wp_die( __( 'Liên kết xác nhận không hợp lệ.', 'mici-ads' ), '', array( 'response' => 400 ) );
	}

	$user    = $users[0];
	$expires = (int) get_user_meta( $user->ID, '_mici_confirm_expires', true );

	if ( time() > $expires ) {
		wp_die( __( 'Liên kết xác nhận đã hết hạn.', 'mici-ads' ), '', array( 'response' => 410 ) );
	}

	$new_email = get_user_meta( $user->ID, '_mici_pending_email', true );
	if ( empty( $new_email ) ) {
		wp_die( __( 'Không tìm thấy email mới.', 'mici-ads' ), '', array( 'response' => 400 ) );
	}

	// Update email + cleanup.
	wp_update_user( array( 'ID' => $user->ID, 'user_email' => $new_email ) );
	delete_user_meta( $user->ID, '_mici_pending_email' );
	delete_user_meta( $user->ID, '_mici_confirm_token' );
	delete_user_meta( $user->ID, '_mici_confirm_expires' );

	$redirect = add_query_arg( 'mici_email_confirmed', '1', mici_get_profile_page_url() ?: home_url( '/' ) );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'template_redirect', 'mici_handle_email_change_confirmation', 5 );
