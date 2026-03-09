<?php
/**
 * Template Name: Authentication
 *
 * Login, signup, and account page for Mici Ads.
 * Uses [mici_auth_forms] shortcode for form rendering.
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
      <h1 class="auth-page__title"><?php esc_html_e( 'Chào mừng đến Mici Ads', 'mici-ads' ); ?></h1>
      <p class="auth-page__subtitle">
        <?php esc_html_e( 'Đăng nhập để khám phá toàn bộ kho mẫu thiết kế chuyên nghiệp', 'mici-ads' ); ?>
      </p>
    </div>
    <?php echo do_shortcode( '[mici_auth_forms]' ); ?>
  </div>
</section>

<?php get_footer(); ?>
