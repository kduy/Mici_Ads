<?php
/**
 * Template Part: Landing Tagline
 *
 * Simple one-line tagline section. Supports ACF tagline_text field.
 *
 * @package MiciAds
 */

$tagline = function_exists( 'get_field' ) ? get_field( 'tagline_text' ) : '';
$tagline = $tagline ?: 'In ấn <em>chuẩn,</em> website <em>đẹp,</em> marketing <em>hiệu quả.</em>';
?>
<section class="l-tagline">
  <p class="l-tagline__text scroll-hidden"><?php echo wp_kses_post( $tagline ); ?></p>
</section>
