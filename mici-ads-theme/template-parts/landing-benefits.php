<?php
/**
 * Template Part: Landing Benefits
 *
 * 6 benefit cards with SVG icons. Uses ACF repeater 'benefits'
 * with hardcoded fallback for all 6 cards.
 *
 * @package MiciAds
 */

$section_title = function_exists( 'get_field' ) ? get_field( 'benefits_section_title' ) : '';
$section_title = $section_title ?: 'Một đối tác, <em>bốn giải pháp</em> trọn gói.';

// Hardcoded benefit SVG icons (inline, stroke-based).
$icons = [
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
];

$default_benefits = [
	[
		'title' => 'Thiết kế & In ấn chuẩn',
		'desc'  => 'Thực đơn, tờ rơi, bảng hiệu — thiết kế riêng cho ngành nail và nhà hàng, file chuẩn in ấn chất lượng cao.',
	],
	[
		'title' => 'Website tối ưu di động',
		'desc'  => 'Website giới thiệu, đặt bàn, đặt lịch online — giao diện đẹp, tải nhanh, tối ưu SEO cho Google.',
	],
	[
		'title' => 'Hình ảnh & Video chuyên nghiệp',
		'desc'  => 'Chụp ảnh sản phẩm, không gian quán và quay video quảng cáo giúp thu hút khách hàng ngay từ cái nhìn đầu tiên.',
	],
	[
		'title' => 'Marketing tăng trưởng',
		'desc'  => 'Chạy quảng cáo Facebook & Google, quản lý fanpage, SEO — chiến lược online giúp tăng khách hàng bền vững.',
	],
	[
		'title' => 'Giao nhanh 24-48h',
		'desc'  => 'Bản mẫu thiết kế đầu tiên trong 24-48h. Website lên sóng trong 5-7 ngày. Tốc độ nhanh, chất lượng không giảm.',
	],
	[
		'title' => 'Chỉnh sửa đến khi hài lòng',
		'desc'  => 'Hỗ trợ tận tình từ thiết kế đến marketing. Chỉnh sửa không giới hạn cho đến khi bạn hoàn toàn ưng ý.',
	],
];

// Use ACF repeater if available.
$benefits = [];
if ( function_exists( 'get_field' ) && have_rows( 'benefits' ) ) {
	$i = 0;
	while ( have_rows( 'benefits' ) ) {
		the_row();
		$benefits[] = [
			'icon'  => get_sub_field( 'icon' ) ?: ( $icons[ $i ] ?? '' ),
			'title' => get_sub_field( 'title' ) ?: '',
			'desc'  => get_sub_field( 'description' ) ?: '',
		];
		$i++;
	}
}

if ( empty( $benefits ) ) {
	// Merge icon into default data.
	foreach ( $default_benefits as $idx => $b ) {
		$benefits[] = array_merge( $b, [ 'icon' => $icons[ $idx ] ?? '' ] );
	}
}
?>
<section class="l-benefits">
  <div class="l-benefits__container">
    <span class="agency__label scroll-hidden"><?php esc_html_e( 'Tại sao chọn Mici Ads', 'mici-ads' ); ?></span>
    <h2 class="l-benefits__title scroll-hidden">
      <?php echo wp_kses_post( $section_title ); ?>
    </h2>
    <div class="l-benefits__grid">
      <?php foreach ( $benefits as $benefit ) : ?>
        <div class="l-benefit-card scroll-hidden">
          <div class="l-benefit-card__icon" style="background: var(--gold-50);">
            <?php echo $benefit['icon']; ?>
          </div>
          <h3 class="l-benefit-card__title"><?php echo esc_html( $benefit['title'] ); ?></h3>
          <p class="l-benefit-card__desc"><?php echo esc_html( $benefit['desc'] ); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
