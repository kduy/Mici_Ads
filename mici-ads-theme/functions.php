<?php
/**
 * Mici Ads Theme — Functions Bootstrapper
 *
 * Defines theme constants and loads inc/ modules.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// Theme constants.
define( 'MICI_THEME_VERSION', '1.3.0' );
define( 'MICI_THEME_DIR', get_template_directory() );
define( 'MICI_THEME_URI', get_template_directory_uri() );

// Load modules.
require_once MICI_THEME_DIR . '/inc/theme-setup.php';
require_once MICI_THEME_DIR . '/inc/enqueue-assets.php';
require_once MICI_THEME_DIR . '/inc/cpt-design.php';
require_once MICI_THEME_DIR . '/inc/cpt-design-gallery.php';
require_once MICI_THEME_DIR . '/inc/acf-fields.php';
require_once MICI_THEME_DIR . '/inc/migrate-designs.php';
require_once MICI_THEME_DIR . '/inc/user-roles-and-capabilities.php';
require_once MICI_THEME_DIR . '/inc/email-confirmation.php';
require_once MICI_THEME_DIR . '/inc/auth-forms.php';
