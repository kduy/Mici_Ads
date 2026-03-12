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
define( 'MICI_THEME_VERSION', '1.5.0' );
define( 'MICI_THEME_DIR', get_template_directory() );
define( 'MICI_THEME_URI', get_template_directory_uri() );

// Load modules.
require_once MICI_THEME_DIR . '/inc/theme-setup.php';
require_once MICI_THEME_DIR . '/inc/enqueue-assets.php';
require_once MICI_THEME_DIR . '/inc/cpt-design.php';
require_once MICI_THEME_DIR . '/inc/cpt-design-gallery.php';
require_once MICI_THEME_DIR . '/inc/pods-compat-get-field.php';
require_once MICI_THEME_DIR . '/inc/pods-fields.php';
require_once MICI_THEME_DIR . '/inc/migrate-designs.php';
require_once MICI_THEME_DIR . '/inc/user-roles-and-capabilities.php';
require_once MICI_THEME_DIR . '/inc/email-confirmation.php';
require_once MICI_THEME_DIR . '/inc/auth-forms.php';
require_once MICI_THEME_DIR . '/inc/auth-shortcode-renderer.php';
require_once MICI_THEME_DIR . '/inc/smtp-configuration.php';

// User profile & likes.
require_once MICI_THEME_DIR . '/inc/profile-form-handler.php';
require_once MICI_THEME_DIR . '/inc/profile-shortcode-renderer.php';
require_once MICI_THEME_DIR . '/inc/password-reset-customization.php';
require_once MICI_THEME_DIR . '/inc/ajax-like-design.php';

// Image processing tools (Design CPT admin).
require_once MICI_THEME_DIR . '/inc/image-processing-helpers.php';
require_once MICI_THEME_DIR . '/inc/image-processing-meta-box.php';
require_once MICI_THEME_DIR . '/inc/ajax-compress-image.php';
require_once MICI_THEME_DIR . '/inc/ajax-pdf-to-jpeg.php';
require_once MICI_THEME_DIR . '/inc/ajax-apply-watermark.php';
