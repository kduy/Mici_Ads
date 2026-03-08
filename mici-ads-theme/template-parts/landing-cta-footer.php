<?php
/**
 * Template Part: Landing CTA Footer
 *
 * Simple CTA section above the contact form.
 * Uses ACF fields cta_title and cta_subtitle with hardcoded fallbacks.
 *
 * @package MiciAds
 */

$cta_title    = function_exists( 'get_field' ) ? get_field( 'cta_title' ) : '';
$cta_subtitle = function_exists( 'get_field' ) ? get_field( 'cta_subtitle' ) : '';

$cta_title    = $cta_title ?: 'Thiết kế, website, ảnh, marketing —<br><em>bắt đầu ngay hôm nay.</em>';
$cta_subtitle = $cta_subtitle ?: 'Liên hệ tư vấn miễn phí';
?>
<section class="l-cta-footer">
  <div class="l-cta-footer__container scroll-hidden">
    <h2 class="l-cta-footer__title"><?php echo wp_kses_post( $cta_title ); ?></h2>
    <a href="#contact" class="l-btn l-btn--dark"><?php echo esc_html( $cta_subtitle ); ?></a>
  </div>
</section>
