<?php
/**
 * Mici Ads Theme — Custom User Roles & Capabilities
 *
 * Registers three custom roles for tiered portfolio access:
 *   mici_vip        → sees all cards (regular + premium)
 *   mici_registered  → sees 100% regular cards
 *   mici_inactive    → login blocked, same as guest
 *
 * Roles are added on theme activation and removed on deactivation.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom roles on theme activation.
 */
function mici_register_custom_roles() {
	// VIP: full access including premium cards.
	add_role(
		'mici_vip',
		__( 'Mici VIP', 'mici-ads' ),
		array(
			'read'         => true,
			'view_regular' => true,
			'view_premium' => true,
		)
	);

	// Registered: full regular card access, no premium.
	add_role(
		'mici_registered',
		__( 'Mici Registered', 'mici-ads' ),
		array(
			'read'         => true,
			'view_regular' => true,
		)
	);

	// Inactive: login blocked by auth filter, effectively same as guest.
	add_role(
		'mici_inactive',
		__( 'Mici Inactive', 'mici-ads' ),
		array(
			'read' => true,
		)
	);
}

/**
 * Remove custom roles on theme deactivation (cleanup).
 */
function mici_remove_custom_roles() {
	remove_role( 'mici_vip' );
	remove_role( 'mici_registered' );
	remove_role( 'mici_inactive' );
}

// Register roles on theme switch (activation).
add_action( 'after_switch_theme', 'mici_register_custom_roles' );

// Ensure roles exist on every init (safe for already-existing roles).
add_action( 'init', function () {
	if ( ! get_role( 'mici_registered' ) ) {
		mici_register_custom_roles();
	}
} );

/**
 * Block inactive users from logging in.
 *
 * @param WP_User|WP_Error $user     User object or error.
 * @param string           $password Password (unused).
 * @return WP_User|WP_Error
 */
function mici_block_inactive_login( $user, $password ) {
	if ( is_wp_error( $user ) ) {
		return $user;
	}

	if ( in_array( 'mici_inactive', (array) $user->roles, true ) ) {
		return new WP_Error(
			'mici_inactive',
			__( 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.', 'mici-ads' )
		);
	}

	return $user;
}
add_filter( 'wp_authenticate_user', 'mici_block_inactive_login', 10, 2 );

/**
 * Get the Mici role slug for the current user (or 'guest').
 *
 * @return string One of: 'mici_vip', 'mici_registered', 'mici_inactive', 'administrator', 'guest'.
 */
function mici_get_user_role() {
	if ( ! is_user_logged_in() ) {
		return 'guest';
	}

	$user  = wp_get_current_user();
	$roles = (array) $user->roles;

	// Admins get VIP-level access.
	if ( in_array( 'administrator', $roles, true ) ) {
		return 'mici_vip';
	}

	$mici_roles = array( 'mici_vip', 'mici_registered', 'mici_inactive' );
	foreach ( $mici_roles as $role ) {
		if ( in_array( $role, $roles, true ) ) {
			return $role;
		}
	}

	// Fallback: any other WP role gets registered-level access.
	return 'mici_registered';
}
