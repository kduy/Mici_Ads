<?php
/**
 * Template Part: Landing Portfolio Marquee
 *
 * 3-column vertical marquee with 4 items per column.
 * Middle column has --reverse class for counter-scroll effect.
 * Static decorative images — CTA links to templates page.
 *
 * @package MiciAds
 */

$templates_url = get_permalink( get_page_by_path( 'templates' ) ) ?: home_url( '/templates/' );
?>
<section id="portfolio" class="l-portfolio">
  <div class="l-portfolio__container">
    <div class="l-portfolio__grid">

      <!-- Column 1: scrolls up -->
      <div class="l-portfolio__col">
        <div class="l-portfolio__track">
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=650&fit=crop"
                 alt="<?php esc_attr_e( 'Menu design cho nhà hàng', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&h=400&fit=crop"
                 alt="<?php esc_attr_e( 'Branding tiệm nail', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1583947215259-38e31be8751f?w=500&h=500&fit=crop"
                 alt="<?php esc_attr_e( 'Poster quảng cáo', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?w=500&h=400&fit=crop"
                 alt="<?php esc_attr_e( 'Social media content', 'mici-ads' ); ?>">
          </div>
        </div>
      </div>

      <!-- Column 2: scrolls down (reverse) -->
      <div class="l-portfolio__col l-portfolio__col--reverse">
        <div class="l-portfolio__track">
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=500&h=400&fit=crop"
                 alt="<?php esc_attr_e( 'Thiết kế bảng hiệu', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&h=650&fit=crop"
                 alt="<?php esc_attr_e( 'Website nhà hàng', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=500&h=500&fit=crop"
                 alt="<?php esc_attr_e( 'Ảnh sản phẩm', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=500&h=400&fit=crop"
                 alt="<?php esc_attr_e( 'Tờ rơi quảng cáo', 'mici-ads' ); ?>">
          </div>
        </div>
      </div>

      <!-- Column 3: scrolls up -->
      <div class="l-portfolio__col">
        <div class="l-portfolio__track">
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1561715276-a2d087060f1d?w=500&h=650&fit=crop"
                 alt="<?php esc_attr_e( 'Video marketing', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=650&fit=crop"
                 alt="<?php esc_attr_e( 'Menu design cho nhà hàng 2', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=500&h=400&fit=crop"
                 alt="<?php esc_attr_e( 'Thiết kế bảng hiệu 2', 'mici-ads' ); ?>">
          </div>
          <div class="l-portfolio__item">
            <img src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&h=650&fit=crop"
                 alt="<?php esc_attr_e( 'Website nhà hàng 2', 'mici-ads' ); ?>">
          </div>
        </div>
      </div>

    </div>

    <div class="l-portfolio__cta">
      <a href="<?php echo esc_url( $templates_url ); ?>" class="l-btn l-btn--primary">
        <?php esc_html_e( 'Xem tất cả mẫu thiết kế', 'mici-ads' ); ?>
      </a>
    </div>
  </div>
</section>
