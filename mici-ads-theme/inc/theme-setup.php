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
 * Auto-create required pages (Auth, Profile) if they don't exist.
 * Runs once on init; uses option flag to skip repeat queries.
 */
function mici_ensure_required_pages() {
	// Only run in admin or on first front-end load after deploy.
	if ( get_option( 'mici_pages_created_v150' ) ) {
		return;
	}

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

	update_option( 'mici_pages_created_v150', true );
}
add_action( 'init', 'mici_ensure_required_pages' );
