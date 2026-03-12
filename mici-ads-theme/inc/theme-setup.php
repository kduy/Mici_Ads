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

/**
 * Allow SVG uploads in WordPress Media Library.
 *
 * @param array $mimes Allowed MIME types.
 * @return array
 */
function mici_allow_svg_uploads( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'mici_allow_svg_uploads' );

/**
 * Fix SVG file type detection (WordPress 5.3+ validates MIME on upload).
 *
 * @param array  $data     File data.
 * @param string $file     Full path to file.
 * @param string $filename The file name.
 * @param array  $mimes    Allowed MIME types.
 * @return array
 */
function mici_fix_svg_mime_type( $data, $file, $filename, $mimes ) {
	$ext = pathinfo( $filename, PATHINFO_EXTENSION );
	if ( 'svg' === strtolower( $ext ) ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = 'svg';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'mici_fix_svg_mime_type', 10, 4 );

/**
 * Skip image processing for SVGs — WordPress image editor cannot resize
 * vector files and throws "cannot process the image" errors without this.
 *
 * @param array $metadata Attachment metadata.
 * @param int   $attachment_id Attachment ID.
 * @return array
 */
function mici_skip_svg_image_processing( $metadata, $attachment_id ) {
	$mime = get_post_mime_type( $attachment_id );
	if ( 'image/svg+xml' === $mime ) {
		$file = get_attached_file( $attachment_id );
		if ( $file ) {
			$metadata = array(
				'width'  => 0,
				'height' => 0,
				'file'   => _wp_relative_upload_path( $file ),
			);
		}
	}
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'mici_skip_svg_image_processing', 10, 2 );

/**
 * Prevent WordPress from trying to scale down SVG uploads (big_image_size_threshold).
 *
 * @param int    $threshold Max pixel dimension.
 * @param array  $imagesize Image dimensions.
 * @param string $file      File path.
 * @return int|false
 */
function mici_disable_svg_big_image_scaling( $threshold, $imagesize, $file ) {
	$ext = pathinfo( $file, PATHINFO_EXTENSION );
	if ( 'svg' === strtolower( $ext ) ) {
		return false; // Disable scaling for SVGs.
	}
	return $threshold;
}
add_filter( 'big_image_size_threshold', 'mici_disable_svg_big_image_scaling', 10, 3 );

/**
 * Auto-activate Pods plugin if installed but not active.
 */
function mici_auto_activate_pods() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$pods_plugin = 'pods/init.php';
	if ( file_exists( WP_PLUGIN_DIR . '/' . $pods_plugin ) && ! is_plugin_active( $pods_plugin ) ) {
		activate_plugin( $pods_plugin );
	}
}
add_action( 'admin_init', 'mici_auto_activate_pods' );
