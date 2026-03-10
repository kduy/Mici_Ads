<?php
/**
 * Template Part: Landing Portfolio Marquee
 *
 * 3-column vertical marquee with images per column.
 * Middle column has --reverse class for counter-scroll effect.
 * Supports ACF repeater 'portfolio_columns' with 3 sub-repeaters,
 * or a single 'portfolio_images' gallery field auto-distributed across columns.
 * Falls back to hardcoded Unsplash images.
 *
 * @package MiciAds
 */

$templates_url = get_permalink( get_page_by_path( 'templates' ) ) ?: home_url( '/templates/' );

// Fallback: 3 columns of images.
$default_columns = [
	[
		[ 'url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=650&fit=crop', 'alt' => 'Menu design cho nhà hàng' ],
		[ 'url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&h=400&fit=crop', 'alt' => 'Branding tiệm nail' ],
		[ 'url' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?w=500&h=500&fit=crop', 'alt' => 'Poster quảng cáo' ],
		[ 'url' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=500&h=400&fit=crop', 'alt' => 'Social media content' ],
	],
	[
		[ 'url' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=500&h=400&fit=crop', 'alt' => 'Thiết kế bảng hiệu' ],
		[ 'url' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&h=650&fit=crop', 'alt' => 'Website nhà hàng' ],
		[ 'url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=500&h=500&fit=crop', 'alt' => 'Ảnh sản phẩm' ],
		[ 'url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=500&h=400&fit=crop', 'alt' => 'Tờ rơi quảng cáo' ],
	],
	[
		[ 'url' => 'https://images.unsplash.com/photo-1561715276-a2d087060f1d?w=500&h=650&fit=crop', 'alt' => 'Video marketing' ],
		[ 'url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=650&fit=crop', 'alt' => 'Menu design cho nhà hàng 2' ],
		[ 'url' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=500&h=400&fit=crop', 'alt' => 'Thiết kế bảng hiệu 2' ],
		[ 'url' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&h=650&fit=crop', 'alt' => 'Website nhà hàng 2' ],
	],
];

// ACF: 12 individual image fields — auto-distribute across 3 columns.
$columns = [];
if ( function_exists( 'get_field' ) ) {
	$has_any = false;
	$columns = [ [], [], [] ];
	for ( $i = 1; $i <= 12; $i++ ) {
		$img = get_field( 'portfolio_image_' . $i );
		if ( ! empty( $img ) && is_array( $img ) ) {
			$has_any  = true;
			$col_idx  = ( $i - 1 ) % 3;
			$columns[ $col_idx ][] = [
				'url' => $img['sizes']['medium_large'] ?? $img['url'],
				'alt' => $img['alt'] ?: '',
			];
		}
	}
	if ( ! $has_any ) {
		$columns = [];
	}
}
if ( empty( $columns ) ) {
	$columns = $default_columns;
}
?>
<section id="portfolio" class="l-portfolio">
  <div class="l-portfolio__container">
    <div class="l-portfolio__grid">

      <?php foreach ( $columns as $col_idx => $images ) : ?>
        <div class="l-portfolio__col<?php echo 1 === $col_idx ? ' l-portfolio__col--reverse' : ''; ?>">
          <div class="l-portfolio__track">
            <?php foreach ( $images as $img ) : ?>
              <div class="l-portfolio__item">
                <img src="<?php echo esc_url( $img['url'] ); ?>"
                     alt="<?php echo esc_attr( $img['alt'] ); ?>">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

    </div>

    <div class="l-portfolio__cta">
      <a href="<?php echo esc_url( $templates_url ); ?>" class="l-btn l-btn--primary">
        <?php esc_html_e( 'Xem tất cả mẫu thiết kế', 'mici-ads' ); ?>
      </a>
    </div>
  </div>
</section>
