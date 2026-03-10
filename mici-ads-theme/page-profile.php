<?php
/**
 * Template Name: Profile
 *
 * User profile page for Mici Ads.
 * Uses [mici_profile] shortcode for rendering.
 *
 * @package MiciAds
 */

get_header();
?>

<section class="auth-page">
  <div class="auth-page__container">
    <div class="auth-page__brand">
      <img src="<?php echo esc_url( MICI_THEME_URI . '/images/logo-mici-ads.svg' ); ?>"
           alt="Mici Ads" class="auth-page__logo" width="120">
      <h1 class="auth-page__title"><?php esc_html_e( 'Tài khoản của bạn', 'mici-ads' ); ?></h1>
    </div>
    <?php echo do_shortcode( '[mici_profile]' ); ?>
  </div>
</section>

<?php get_footer(); ?>
