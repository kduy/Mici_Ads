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
