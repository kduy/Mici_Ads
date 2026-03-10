<?php
/**
 * Mici Ads Theme — AJAX: Like/Unlike Design
 *
 * Toggles a design in the user's liked list (user meta: _mici_liked_designs).
 * Also maintains _design_like_count post meta for display.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler: toggle like on a design.
 *
 * POST params: design_id (int).
 * Returns: { liked: bool, count: int }.
 */
function mici_ajax_toggle_like_design() {
	check_ajax_referer( 'mici_like_design' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( __( 'Vui lòng đăng nhập.', 'mici-ads' ) );
	}

	$design_id = absint( $_POST['design_id'] ?? 0 );
	if ( ! $design_id || 'design' !== get_post_type( $design_id ) ) {
		wp_send_json_error( __( 'Thiết kế không hợp lệ.', 'mici-ads' ) );
	}

	$user_id   = get_current_user_id();
	$liked_ids = get_user_meta( $user_id, '_mici_liked_designs', true );
	$liked_ids = is_array( $liked_ids ) ? $liked_ids : array();

	$is_liked = in_array( $design_id, $liked_ids, true );

	if ( $is_liked ) {
		// Unlike: remove from list.
		$liked_ids = array_values( array_diff( $liked_ids, array( $design_id ) ) );
		$count_delta = -1;
	} else {
		// Like: add to list.
		$liked_ids[] = $design_id;
		$count_delta = 1;
	}

	update_user_meta( $user_id, '_mici_liked_designs', $liked_ids );

	// Update post like count.
	$current_count = (int) get_post_meta( $design_id, '_design_like_count', true );
	$new_count     = max( 0, $current_count + $count_delta );
	update_post_meta( $design_id, '_design_like_count', $new_count );

	wp_send_json_success( array(
		'liked' => ! $is_liked,
		'count' => $new_count,
	) );
}
add_action( 'wp_ajax_mici_toggle_like_design', 'mici_ajax_toggle_like_design' );

/**
 * Get liked design IDs for a user.
 *
 * @param int $user_id User ID (0 = current user).
 * @return array Array of design post IDs.
 */
function mici_get_user_liked_designs( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( ! $user_id ) {
		return array();
	}
	$liked = get_user_meta( $user_id, '_mici_liked_designs', true );
	return is_array( $liked ) ? $liked : array();
}
