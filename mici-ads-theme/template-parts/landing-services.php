<?php
/**
 * Template Part: Landing Services
 *
 * 4 service cards in a sticky card stack layout.
 * Uses ACF repeater 'services' with fallback to hardcoded data.
 *
 * @package MiciAds
 */

// Check for ACF repeater data.
$section_title = function_exists( 'get_field' ) ? get_field( 'section_title' ) : '';
$section_title = $section_title ?: 'Bốn dịch vụ, <em>một đối tác</em> — mọi thứ bạn cần để <em>thu hút khách hàng.</em>';

// Shared check icon SVG.
$check_icon = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="var(--gold-400)"/><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2"/></svg>';

// Fallback service data.
$default_services = [
	[
		'image_url'   => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=600&h=400&fit=crop',
		'image_alt'   => 'Thiết kế và in ấn',
		'title'       => 'Thiết kế & In ấn',
		'description' => 'Thực đơn, tờ rơi, name card, bảng hiệu, standee — thiết kế chuyên nghiệp và in ấn chất lượng cao dành riêng cho tiệm nail và nhà hàng.',
		'tags'        => [ 'Thực đơn', 'Tờ rơi & Poster', 'Bảng hiệu' ],
	],
	[
		'image_url'   => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
		'image_alt'   => 'Thiết kế website',
		'title'       => 'Website',
		'description' => 'Website giới thiệu, đặt bàn, đặt lịch online cho nhà hàng, tiệm nail và quán cafe. Giao diện đẹp, tối ưu di động.',
		'tags'        => [ 'Landing Page', 'Responsive', 'SEO' ],
	],
	[
		'image_url'   => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=600&h=400&fit=crop',
		'image_alt'   => 'Chụp ảnh và dàn dựng video',
		'title'       => 'Chụp ảnh & Video',
		'description' => 'Chụp ảnh sản phẩm, không gian quán và dàn dựng video quảng cáo chuyên nghiệp. Hình ảnh đẹp, thu hút khách hàng hiệu quả.',
		'tags'        => [ 'Ảnh sản phẩm', 'Video quảng cáo', 'Reels' ],
	],
	[
		'image_url'   => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=400&fit=crop',
		'image_alt'   => 'Online marketing',
		'title'       => 'Online Marketing',
		'description' => 'Quản lý fanpage, chạy quảng cáo Facebook & Google, SEO và chiến lược marketing online giúp tăng khách hàng bền vững.',
		'tags'        => [ 'Facebook Ads', 'Google Ads', 'Social Media' ],
	],
];

// Use ACF repeater if available, otherwise fallback.
$services = [];
if ( function_exists( 'get_field' ) && have_rows( 'services' ) ) {
	while ( have_rows( 'services' ) ) {
		the_row();
		$services[] = [
			'image_url'   => get_sub_field( 'image_url' ) ?: '',
			'image_alt'   => get_sub_field( 'title' ) ?: '',
			'title'       => get_sub_field( 'title' ) ?: '',
			'description' => get_sub_field( 'description' ) ?: '',
			'tags'        => array_map( 'trim', explode( ',', get_sub_field( 'tags' ) ?: '' ) ),
		];
	}
}

if ( empty( $services ) ) {
	$services = $default_services;
}
?>

<!-- Services intro heading -->
<section id="services" class="l-services-intro">
  <h2 class="l-services-intro__title scroll-hidden">
    <?php echo wp_kses_post( $section_title ); ?>
  </h2>
</section>

<!-- Service Cards -->
<section class="l-services">
  <div class="l-services__container">
    <div class="l-services__grid">
      <?php foreach ( $services as $service ) : ?>
        <div class="l-service-card scroll-hidden">
          <div class="l-service-card__image">
            <img src="<?php echo esc_url( $service['image_url'] ); ?>" alt="<?php echo esc_attr( $service['image_alt'] ); ?>">
          </div>
          <div class="l-service-card__content">
            <h3 class="l-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
            <p class="l-service-card__desc"><?php echo esc_html( $service['description'] ); ?></p>
            <div class="l-service-card__tags">
              <?php foreach ( $service['tags'] as $tag ) : ?>
                <?php if ( trim( $tag ) ) : ?>
                  <span class="l-tag"><?php echo $check_icon; ?><?php echo esc_html( trim( $tag ) ); ?></span>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
