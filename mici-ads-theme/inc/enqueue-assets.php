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

	// --- Templates page only ---
	if ( is_page_template( 'page-templates.php' ) ) {
		mici_enqueue_templates_page_assets();
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

	// Inject login state and login URL before main.js runs.
	$login_url = wp_login_url( get_permalink() );
	wp_add_inline_script(
		'mici-main',
		'window.miciUserLoggedIn = ' . ( is_user_logged_in() ? 'true' : 'false' ) . ';'
		. 'window.miciLoginUrl = "' . esc_url( $login_url ) . '";',
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

			$items[] = array(
				'id'       => $id,
				'name'     => get_the_title(),
				'industry' => esc_html( get_post_meta( $id, '_design_industry', true ) ),
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
