<?php
/**
 * Mici Ads Theme — Asset Enqueuing
 *
 * Loads CSS/JS conditionally per page context.
 * - All pages: styles.css, Google Fonts
 * - Front page: landing.css, home.js, visual-effects.js, landing-faq.js, Turnstile
 * - Templates page: design-modal.css, main.js (+ localized data), design-modal.js, visual-effects.js
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/** Google Fonts URL shared across enqueue calls. */
const MICI_GOOGLE_FONTS_URL = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap';

/**
 * Enqueue all theme assets.
 */
function mici_enqueue_assets() {
	// --- Global assets ---
	wp_enqueue_style(
		'mici-fonts',
		MICI_GOOGLE_FONTS_URL,
		array(),
		null // External resource; no version hash.
	);

	wp_enqueue_style(
		'mici-styles',
		MICI_THEME_URI . '/css/styles.css',
		array(),
		MICI_THEME_VERSION
	);

	// --- Front page only ---
	if ( is_front_page() ) {
		mici_enqueue_front_page_assets();
		return;
	}

	// --- Auth page ---
	if ( is_page_template( 'page-auth.php' ) ) {
		wp_enqueue_style(
			'mici-auth',
			MICI_THEME_URI . '/css/auth.css',
			array( 'mici-styles' ),
			MICI_THEME_VERSION
		);
		return;
	}

	// --- Profile page ---
	if ( is_page_template( 'page-profile.php' ) ) {
		wp_enqueue_style( 'mici-auth', MICI_THEME_URI . '/css/auth.css', array( 'mici-styles' ), MICI_THEME_VERSION );
		wp_enqueue_style( 'mici-profile', MICI_THEME_URI . '/css/profile.css', array( 'mici-auth' ), MICI_THEME_VERSION );
		mici_enqueue_likes_script();
		return;
	}

	// --- Templates page only ---
	if ( is_page_template( 'page-templates.php' ) ) {
		mici_enqueue_templates_page_assets();
		mici_enqueue_likes_script();
	}
}
add_action( 'wp_enqueue_scripts', 'mici_enqueue_assets' );

/**
 * Enqueue landing page assets (front page).
 */
function mici_enqueue_front_page_assets() {
	wp_enqueue_style(
		'mici-landing',
		MICI_THEME_URI . '/css/landing.css',
		array( 'mici-styles' ),
		MICI_THEME_VERSION
	);

	wp_enqueue_script(
		'mici-home',
		MICI_THEME_URI . '/js/home.js',
		array(),
		MICI_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'mici-visual-effects',
		MICI_THEME_URI . '/js/visual-effects.js',
		array(),
		MICI_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'mici-landing-faq',
		MICI_THEME_URI . '/js/landing-faq.js',
		array(),
		MICI_THEME_VERSION,
		true
	);

	// WPForms style overrides.
	wp_enqueue_style(
		'mici-wpforms-overrides',
		MICI_THEME_URI . '/css/wpforms-overrides.css',
		array( 'mici-styles' ),
		MICI_THEME_VERSION
	);

	// Cloudflare Turnstile CAPTCHA script.
	wp_enqueue_script(
		'cf-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null,
		true
	);
}

/**
 * Enqueue templates page assets and localize portfolio data.
 */
function mici_enqueue_templates_page_assets() {
	wp_enqueue_style(
		'mici-design-modal',
		MICI_THEME_URI . '/css/design-modal.css',
		array( 'mici-styles' ),
		MICI_THEME_VERSION
	);

	wp_enqueue_script(
		'mici-main',
		MICI_THEME_URI . '/js/main.js',
		array(),
		MICI_THEME_VERSION,
		true
	);

	// Inject auth state before main.js runs.
	$user_role = function_exists( 'mici_get_user_role' ) ? mici_get_user_role() : 'guest';
	$auth_url  = function_exists( 'mici_get_auth_page_url' ) ? mici_get_auth_page_url() : '';
	$login_url = $auth_url ? $auth_url : wp_login_url( get_permalink() );

	wp_add_inline_script(
		'mici-main',
		'window.miciUserLoggedIn = ' . ( is_user_logged_in() ? 'true' : 'false' ) . ';'
		. 'window.miciUserRole = ' . wp_json_encode( $user_role ) . ';'
		. 'window.miciLoginUrl = ' . wp_json_encode( esc_url( $login_url ) ) . ';',
		'before'
	);

	// Localize portfolio data from CPT 'design'.
	wp_localize_script( 'mici-main', 'miciDesigns', mici_get_designs_data() );

	wp_enqueue_script(
		'mici-design-modal',
		MICI_THEME_URI . '/js/design-modal.js',
		array( 'mici-main' ),
		MICI_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'mici-visual-effects',
		MICI_THEME_URI . '/js/visual-effects.js',
		array(),
		MICI_THEME_VERSION,
		true
	);
}

/**
 * Build portfolio designs array from CPT posts.
 *
 * @return array Flat array of design objects for JS.
 */
function mici_get_designs_data() {
	$query = new WP_Query(
		array(
			'post_type'      => 'design',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();

			// Get featured image URL (used as card photo when available).
			$thumb_url = get_the_post_thumbnail_url( $id, 'medium_large' );

			// Get gallery image URLs (up to 30) for detail modal carousel.
			$gallery_ids  = get_post_meta( $id, '_design_gallery', true );
			$gallery_urls = array();
			if ( $gallery_ids ) {
				$ids_arr = array_filter( array_map( 'intval', explode( ',', $gallery_ids ) ) );
				foreach ( array_slice( $ids_arr, 0, 30 ) as $att_id ) {
					$url = wp_get_attachment_image_url( $att_id, 'large' );
					if ( $url ) {
						$gallery_urls[] = esc_url( $url );
					}
				}
			}

			$items[] = array(
				'id'        => $id,
				'name'      => get_the_title(),
				'image'     => $thumb_url ? esc_url( $thumb_url ) : '',
				'images'    => $gallery_urls,
				'isPremium' => ( '1' === get_post_meta( $id, '_design_premium', true ) ),
				'industry'  => esc_html( get_post_meta( $id, '_design_industry', true ) ),
				'category' => esc_html( get_post_meta( $id, '_design_category', true ) ),
				'style'    => esc_html( get_post_meta( $id, '_design_style', true ) ),
				'theme'    => esc_html( get_post_meta( $id, '_design_theme', true ) ),
				'logo'     => esc_html( get_post_meta( $id, '_design_logo', true ) ),
				'sub'      => esc_html( get_post_meta( $id, '_design_sub', true ) ),
				'detail'   => esc_html( get_post_meta( $id, '_design_detail', true ) ),
				'colors'   => array(
					esc_attr( get_post_meta( $id, '_design_color_1', true ) ),
					esc_attr( get_post_meta( $id, '_design_color_2', true ) ),
					esc_attr( get_post_meta( $id, '_design_color_3', true ) ),
					esc_attr( get_post_meta( $id, '_design_color_4', true ) ),
				),
			);
		}
		wp_reset_postdata();
	}

	return $items;
}

/**
 * Enqueue likes script with localized data.
 * Used on templates page and profile page.
 */
function mici_enqueue_likes_script() {
	wp_enqueue_style( 'mici-profile', MICI_THEME_URI . '/css/profile.css', array( 'mici-styles' ), MICI_THEME_VERSION );

	wp_enqueue_script(
		'mici-profile-likes',
		MICI_THEME_URI . '/js/profile-likes.js',
		array(),
		MICI_THEME_VERSION,
		true
	);

	$liked_ids = function_exists( 'mici_get_user_liked_designs' ) ? mici_get_user_liked_designs() : array();
	$auth_url  = function_exists( 'mici_get_auth_page_url' ) ? mici_get_auth_page_url() : '';

	wp_localize_script( 'mici-profile-likes', 'miciLikes', array(
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'mici_like_design' ),
		'likedIds' => $liked_ids,
		'loggedIn' => is_user_logged_in(),
		'loginUrl' => $auth_url ? $auth_url : wp_login_url(),
	) );
}
