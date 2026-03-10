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

// ACF textarea 'trust_clients_list' — one client name per line.
$default_clients = [ 'Olivia Nails', 'Pho Ha Noi', 'BREW Coffee', 'Rosa Maria', 'Nail Luxury' ];

$trust_clients = [];
if ( function_exists( 'get_field' ) ) {
	$raw = get_field( 'trust_clients_list' );
	if ( $raw ) {
		$lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
		if ( ! empty( $lines ) ) {
			$trust_clients = $lines;
		}
	}
}
if ( empty( $trust_clients ) ) {
	$trust_clients = $default_clients;
}
?>
<section class="l-trust">
  <p class="l-trust__text"><?php echo esc_html( $trust_text ); ?></p>
  <div class="l-trust__logos">
    <div class="l-trust__track">
      <?php foreach ( $trust_clients as $client ) : ?>
        <span class="l-trust__logo"><?php echo esc_html( $client ); ?></span>
        <span class="l-trust__divider"></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>
