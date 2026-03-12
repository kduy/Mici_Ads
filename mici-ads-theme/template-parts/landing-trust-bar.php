<?php
/**
 * Template Part: Landing Trust Bar
 *
 * Marquee strip of client logos with trust statement above.
 * Logos uploaded via Pods trust_logo_N fields; falls back to text names.
 *
 * @package MiciAds
 */

$trust_text = function_exists( 'get_field' ) ? get_field( 'trust_text' ) : '';
$trust_text = $trust_text ?: 'Được tin tưởng bởi các tiệm nail, nhà hàng và quán cafe trên toàn quốc';

// Collect trust logos (up to 8).
$logos = array();
if ( function_exists( 'get_field' ) ) {
	for ( $i = 1; $i <= 30; $i++ ) {
		$image = get_field( 'trust_logo_' . $i . '_image' );
		$name  = get_field( 'trust_logo_' . $i . '_name' );
		if ( ! empty( $image ) ) {
			$logos[] = array(
				'url' => is_array( $image ) ? $image['url'] : wp_get_attachment_url( $image ),
				'alt' => $name ?: ( is_array( $image ) ? $image['alt'] : '' ),
			);
		}
	}
}

// Fallback: text-only client names when no logos uploaded.
$fallback_clients = array( 'Olivia Nails', 'Pho Ha Noi', 'BREW Coffee', 'Rosa Maria', 'Nail Luxury' );
$has_logos         = ! empty( $logos );
?>
<section class="l-trust">
  <p class="l-trust__text"><?php echo esc_html( $trust_text ); ?></p>
  <div class="l-trust__logos">
    <div class="l-trust__track">
      <?php if ( $has_logos ) : ?>
        <?php foreach ( $logos as $logo ) : ?>
          <img class="l-trust__logo-img" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" loading="lazy" />
        <?php endforeach; ?>
      <?php else : ?>
        <?php foreach ( $fallback_clients as $client ) : ?>
          <span class="l-trust__logo"><?php echo esc_html( $client ); ?></span>
          <span class="l-trust__divider"></span>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
