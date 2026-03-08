<?php
/**
 * Template Part: Landing Trust Bar
 *
 * Marquee strip of client logos with trust statement above.
 * Supports ACF trust_text field with hardcoded fallback.
 *
 * @package MiciAds
 */

$trust_text = function_exists( 'get_field' ) ? get_field( 'trust_text' ) : '';
$trust_text = $trust_text ?: 'Được tin tưởng bởi các tiệm nail, nhà hàng và quán cafe trên toàn quốc';
?>
<section class="l-trust">
  <p class="l-trust__text"><?php echo esc_html( $trust_text ); ?></p>
  <div class="l-trust__logos">
    <div class="l-trust__track">
      <span class="l-trust__logo">Olivia Nails</span>
      <span class="l-trust__divider"></span>
      <span class="l-trust__logo">Pho Ha Noi</span>
      <span class="l-trust__divider"></span>
      <span class="l-trust__logo">BREW Coffee</span>
      <span class="l-trust__divider"></span>
      <span class="l-trust__logo">Rosa Maria</span>
      <span class="l-trust__divider"></span>
      <span class="l-trust__logo">Nail Luxury</span>
      <span class="l-trust__divider"></span>
    </div>
  </div>
</section>
