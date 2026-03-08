<?php
/**
 * Template Part: Landing Testimonials
 *
 * 4 testimonial cards in a horizontal marquee scroll.
 * Uses ACF repeater 'testimonials' with hardcoded fallback.
 *
 * @package MiciAds
 */

$section_title = function_exists( 'get_field' ) ? get_field( 'testimonials_section_title' ) : '';
$section_title = $section_title ?: 'Khách hàng tại Đức nói gì về <em>dịch vụ của chúng tôi.</em>';

// Star SVG — reused per card.
$star = '<svg width="16" height="16" viewBox="0 0 24 24" fill="var(--gold-400)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

$default_testimonials = [
	[
		'quote'          => 'Mici thiết kế lại toàn bộ Speisekarte và bảng hiệu cho quán, khách Đức vào khen đẹp và chuyên nghiệp hơn hẳn. In ấn chất lượng, màu sắc chuẩn.',
		'name'           => 'Chị Hương',
		'role'           => 'Chủ quán Phở Hà Nội, Berlin — Thiết kế & In ấn',
		'avatar_initial' => 'H',
		'rating'         => 5,
	],
	[
		'quote'          => 'Website đặt Termin online giúp tiệm tôi tiếp nhận khách dễ dàng hơn. Khách Đức thích book qua web, không cần gọi điện nữa.',
		'name'           => 'Chị Linh',
		'role'           => 'Chủ tiệm Nail Luxury, München — Website',
		'avatar_initial' => 'L',
		'rating'         => 5,
	],
	[
		'quote'          => 'Bộ ảnh sản phẩm và video Reel Mici quay cho quán đẹp xuất sắc. Đăng lên Instagram và Facebook là khách hỏi liên tục, cả khách Đức lẫn Việt.',
		'name'           => 'Anh Tuấn',
		'role'           => 'Chủ quán BREW Coffee, Hamburg — Chụp ảnh & Video',
		'avatar_initial' => 'T',
		'rating'         => 5,
	],
	[
		'quote'          => 'Từ khi Mici chạy Google Ads và quản lý fanpage, lượng khách đến tiệm tăng gấp đôi chỉ trong 2 tháng. Nhiều khách Đức tìm thấy tiệm qua Google.',
		'name'           => 'Anh Minh',
		'role'           => 'Chủ tiệm Rosa Nails, Frankfurt — Online Marketing',
		'avatar_initial' => 'M',
		'rating'         => 5,
	],
];

// Use ACF repeater if available.
$testimonials = [];
if ( function_exists( 'get_field' ) && have_rows( 'testimonials' ) ) {
	while ( have_rows( 'testimonials' ) ) {
		the_row();
		$testimonials[] = [
			'quote'          => get_sub_field( 'quote' ) ?: '',
			'name'           => get_sub_field( 'name' ) ?: '',
			'role'           => get_sub_field( 'role' ) ?: '',
			'avatar_initial' => get_sub_field( 'avatar_initial' ) ?: '',
			'rating'         => (int) ( get_sub_field( 'rating' ) ?: 5 ),
		];
	}
}

if ( empty( $testimonials ) ) {
	$testimonials = $default_testimonials;
}
?>
<section id="testimonials" class="l-testimonials">
  <div class="l-testimonials__container">
    <h2 class="l-testimonials__title scroll-hidden">
      <?php echo wp_kses_post( $section_title ); ?>
    </h2>
    <div class="l-testimonials__scroll">
      <div class="l-testimonials__track">
        <?php foreach ( $testimonials as $t ) : ?>
          <div class="l-testimonial-card">
            <div class="l-testimonial-card__stars">
              <?php echo str_repeat( $star, min( 5, max( 1, (int) $t['rating'] ) ) ); ?>
            </div>
            <p class="l-testimonial-card__text"><?php echo esc_html( $t['quote'] ); ?></p>
            <div class="l-testimonial-card__author">
              <div class="l-testimonial-card__avatar"><?php echo esc_html( $t['avatar_initial'] ); ?></div>
              <div>
                <strong><?php echo esc_html( $t['name'] ); ?></strong><br>
                <span><?php echo esc_html( $t['role'] ); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
