<?php
/**
 * Template Part: Landing Hero
 *
 * Displays the hero section with social proof, heading, CTA buttons,
 * and a gallery preview track. Supports ACF fields with hardcoded fallbacks.
 *
 * @package MiciAds
 */

// ACF fields with fallbacks.
$proof_text          = function_exists( 'get_field' ) ? get_field( 'proof_text' ) : '';
$hero_title          = function_exists( 'get_field' ) ? get_field( 'hero_title' ) : '';
$hero_subtitle       = function_exists( 'get_field' ) ? get_field( 'hero_subtitle' ) : '';
$cta_primary_text    = function_exists( 'get_field' ) ? get_field( 'cta_primary_text' ) : '';
$cta_primary_url     = function_exists( 'get_field' ) ? get_field( 'cta_primary_url' ) : '';
$cta_secondary_text  = function_exists( 'get_field' ) ? get_field( 'cta_secondary_text' ) : '';
$cta_secondary_url   = function_exists( 'get_field' ) ? get_field( 'cta_secondary_url' ) : '';

// Apply defaults.
$proof_text         = $proof_text ?: '500+ khách hàng hài lòng';
$hero_title         = $hero_title ?: 'Thiết kế, in ấn, website<br>và <em>marketing trọn gói.</em>';
$hero_subtitle      = $hero_subtitle ?: 'Từ thực đơn, bảng hiệu đến website và quảng cáo online — tất cả trong một dịch vụ dành riêng cho tiệm nail và nhà hàng.';
$cta_primary_text   = $cta_primary_text ?: 'Liên hệ tư vấn';
$cta_primary_url    = $cta_primary_url ?: '#contact';
$cta_secondary_text = $cta_secondary_text ?: 'Xem mẫu thiết kế';
$cta_secondary_url  = $cta_secondary_url ?: '#portfolio';

// ACF group fields 'hero_item_1' through 'hero_item_4'.
// Each group: image, label, sublabel, background, text_color.
$default_gallery = [
	[ 'type' => 'card',  'label' => 'OLIVIA',     'sublabel' => 'Nail Art Salon',      'bg' => 'linear-gradient(135deg, #1a1a2e, #16213e)', 'color' => '#f4c2c2' ],
	[ 'type' => 'image', 'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=300&fit=crop', 'image_alt' => 'Menu design' ],
	[ 'type' => 'image', 'image_url' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=400&h=300&fit=crop', 'image_alt' => 'Branding design' ],
	[ 'type' => 'card',  'label' => 'Rosa Maria',  'sublabel' => 'Filipino Restaurant', 'bg' => 'linear-gradient(135deg, #fefae0, #f5f0e8)', 'color' => 'var(--warm-800)' ],
];

$hero_gallery = [];
if ( function_exists( 'get_field' ) ) {
	for ( $i = 1; $i <= 4; $i++ ) {
		$item = get_field( 'hero_item_' . $i );
		if ( ! empty( $item ) && is_array( $item ) ) {
			$img   = $item['image'] ?? null;
			$label = $item['label'] ?? '';
			if ( $img ) {
				$hero_gallery[] = [
					'type'      => 'image',
					'image_url' => esc_url( is_array( $img ) ? $img['url'] : $img ),
					'image_alt' => $label ?: ( is_array( $img ) ? ( $img['alt'] ?: '' ) : '' ),
				];
			} elseif ( $label ) {
				$hero_gallery[] = [
					'type'     => 'card',
					'label'    => $label,
					'sublabel' => $item['sublabel'] ?? '',
					'bg'       => $item['background'] ?? 'linear-gradient(135deg, #1a1a2e, #16213e)',
					'color'    => $item['text_color'] ?? '#f4c2c2',
				];
			}
		}
	}
}
if ( empty( $hero_gallery ) ) {
	$hero_gallery = $default_gallery;
}

// SVG star helper — reused 5x.
$star_svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--gold-400)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
?>
<section class="l-hero">
  <div class="l-hero__container">
    <!-- Social proof -->
    <div class="l-hero__proof">
      <div class="l-hero__avatars">
        <span class="l-hero__avatar" style="background: var(--nail-pink);">H</span>
        <span class="l-hero__avatar" style="background: var(--gold-100);">L</span>
        <span class="l-hero__avatar" style="background: var(--rest-cream);">T</span>
      </div>
      <div class="l-hero__stars">
        <?php echo str_repeat( $star_svg, 5 ); ?>
      </div>
      <span class="l-hero__proof-text"><?php echo esc_html( $proof_text ); ?></span>
    </div>

    <!-- Main heading -->
    <h1 class="l-hero__title">
      <?php echo wp_kses_post( $hero_title ); ?>
    </h1>

    <p class="l-hero__subtitle">
      <?php echo esc_html( $hero_subtitle ); ?>
    </p>

    <div class="l-hero__actions">
      <a href="<?php echo esc_url( $cta_primary_url ); ?>" class="l-btn l-btn--primary">
        <?php echo esc_html( $cta_primary_text ); ?>
      </a>
      <a href="<?php echo esc_url( $cta_secondary_url ); ?>" class="l-btn l-btn--outline">
        <?php echo esc_html( $cta_secondary_text ); ?>
      </a>
    </div>
  </div>

  <!-- Portfolio preview row (ACF: hero_gallery repeater) -->
  <div class="l-hero__gallery">
    <div class="l-hero__gallery-track">
      <?php foreach ( $hero_gallery as $item ) : ?>
        <div class="l-hero__gallery-item">
          <?php if ( 'card' === $item['type'] ) : ?>
            <div class="l-hero__gallery-card" style="background: <?php echo esc_attr( $item['bg'] ); ?>;">
              <span style="color: <?php echo esc_attr( $item['color'] ); ?>; font-family: var(--font-serif); font-size: 1.4rem;"><?php echo esc_html( $item['label'] ); ?></span>
              <?php if ( ! empty( $item['sublabel'] ) ) : ?>
                <small style="color: #999; display: block; margin-top: 4px;"><?php echo esc_html( $item['sublabel'] ); ?></small>
              <?php endif; ?>
            </div>
          <?php else : ?>
            <img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['image_alt'] ); ?>" class="l-hero__gallery-img">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
