<?php
/**
 * Mici Ads Theme — Theme Setup
 *
 * Registers theme supports and navigation menus.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and nav menus.
 */
function mici_theme_setup() {
	// Allow WordPress to manage the document title.
	add_theme_support( 'title-tag' );

	// Enable featured images.
	add_theme_support( 'post-thumbnails' );

	// Use HTML5 markup for core elements.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Navigation', 'mici-ads' ),
		)
	);
}
add_action( 'after_setup_theme', 'mici_theme_setup' );

/**
 * Hide the WordPress admin bar for non-administrator users.
 * Custom roles (mici_vip, mici_registered) should not see the admin bar.
 *
 * @param bool $show Whether to show the admin bar.
 * @return bool
 */
function mici_hide_admin_bar_for_non_admins( $show ) {
	return current_user_can( 'manage_options' ) ? $show : false;
}
add_filter( 'show_admin_bar', 'mici_hide_admin_bar_for_non_admins' );

/**
 * Auto-create required pages (Auth, Profile) if they don't exist.
 * Runs once on init; uses option flag to skip repeat queries.
 */
function mici_ensure_required_pages() {
	// Only run in admin or on first front-end load after deploy.
	if ( get_option( 'mici_pages_created_v160' ) ) {
		return;
	}

	// Ensure a static Front Page exists and is assigned.
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( ! $front_page_id || 'page' !== get_option( 'show_on_front' ) ) {
		// Look for existing page titled "Trang chủ" or "Home".
		$home = get_page_by_path( 'trang-chu' );
		if ( ! $home ) {
			$home = get_page_by_path( 'home' );
		}

		if ( $home ) {
			$front_page_id = $home->ID;
		} else {
			$front_page_id = wp_insert_post( array(
				'post_title'   => __( 'Trang chủ', 'mici-ads' ),
				'post_name'    => 'trang-chu',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			) );
		}

		if ( $front_page_id && ! is_wp_error( $front_page_id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_page_id );
		}
	}

	// Ensure Profile page exists.
	$required_pages = array(
		array(
			'template' => 'page-profile.php',
			'title'    => __( 'Tài khoản', 'mici-ads' ),
			'slug'     => 'tai-khoan',
		),
	);

	foreach ( $required_pages as $page ) {
		$existing = get_pages( array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => $page['template'],
			'number'     => 1,
		) );

		if ( empty( $existing ) ) {
			$page_id = wp_insert_post( array(
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			) );

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $page['template'] );
			}
		}
	}

	update_option( 'mici_pages_created_v160', true );
}
add_action( 'init', 'mici_ensure_required_pages' );
