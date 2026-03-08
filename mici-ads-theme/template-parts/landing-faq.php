<?php
/**
 * Template Part: Landing FAQ
 *
 * 5 accordion FAQ items with aria-expanded.
 * Uses ACF repeater 'faq_items' with hardcoded fallback.
 *
 * @package MiciAds
 */

$section_title = function_exists( 'get_field' ) ? get_field( 'faq_section_title' ) : '';
$section_title = $section_title ?: '<em>Câu hỏi</em> thường gặp.';

$default_faqs = [
	[
		'question' => 'Mici Ads cung cấp những dịch vụ nào?',
		'answer'   => 'Chúng tôi cung cấp 4 dịch vụ chính: Thiết kế & In ấn (thực đơn, tờ rơi, bảng hiệu), Website (landing page, đặt lịch online), Chụp ảnh & Video (sản phẩm, quảng cáo), và Online Marketing (Facebook Ads, Google Ads, SEO).',
	],
	[
		'question' => 'Thời gian hoàn thành mỗi dịch vụ bao lâu?',
		'answer'   => 'Thiết kế & In ấn: 24-48h cho bản mẫu đầu tiên. Website: 5-7 ngày lên sóng. Chụp ảnh & Video: 2-3 ngày sau buổi chụp. Marketing: thiết lập chiến dịch trong 3-5 ngày.',
	],
	[
		'question' => 'Tôi có thể đặt riêng từng dịch vụ không?',
		'answer'   => 'Hoàn toàn được. Bạn có thể chọn riêng Thiết kế & In ấn, Website, Chụp ảnh & Video hoặc Online Marketing — hoặc kết hợp nhiều dịch vụ để được ưu đãi trọn gói.',
	],
	[
		'question' => 'Chi phí dịch vụ Online Marketing như thế nào?',
		'answer'   => 'Phí quản lý marketing được tính theo tháng, tùy quy mô chiến dịch. Bạn chỉ trả phí dịch vụ cố định, ngân sách quảng cáo do bạn quyết định. Liên hệ để nhận báo giá chi tiết.',
	],
	[
		'question' => 'Sau khi hoàn thành tôi nhận được gì?',
		'answer'   => 'Thiết kế: file gốc (AI/PSD) + file in ấn. Website: domain, hosting và quyền quản trị. Ảnh & Video: file gốc chất lượng cao. Marketing: báo cáo chi tiết hiệu quả chiến dịch hàng tháng.',
	],
];

// Use ACF repeater if available.
$faqs = [];
if ( function_exists( 'get_field' ) && have_rows( 'faq_items' ) ) {
	while ( have_rows( 'faq_items' ) ) {
		the_row();
		$faqs[] = [
			'question' => get_sub_field( 'question' ) ?: '',
			'answer'   => get_sub_field( 'answer' ) ?: '',
		];
	}
}

if ( empty( $faqs ) ) {
	$faqs = $default_faqs;
}

// Plus/cross icon for accordion toggle.
$plus_icon = '<svg class="l-faq__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
?>
<section id="faq" class="l-faq">
  <div class="l-faq__container">
    <h2 class="l-faq__title scroll-hidden">
      <?php echo wp_kses_post( $section_title ); ?>
    </h2>
    <div class="l-faq__list">
      <?php foreach ( $faqs as $faq ) : ?>
        <div class="l-faq__item">
          <button class="l-faq__question" aria-expanded="false">
            <span><?php echo esc_html( $faq['question'] ); ?></span>
            <?php echo $plus_icon; ?>
          </button>
          <div class="l-faq__answer">
            <p><?php echo esc_html( $faq['answer'] ); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
