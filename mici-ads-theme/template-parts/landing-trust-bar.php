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

// ACF repeater 'trust_clients' — each row: name (text), logo (image, optional).
$default_clients = [ 'Olivia Nails', 'Pho Ha Noi', 'BREW Coffee', 'Rosa Maria', 'Nail Luxury' ];

$trust_clients = [];
if ( function_exists( 'get_field' ) && have_rows( 'trust_clients' ) ) {
	while ( have_rows( 'trust_clients' ) ) {
		the_row();
		$name = get_sub_field( 'name' );
		if ( $name ) {
			$trust_clients[] = $name;
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
