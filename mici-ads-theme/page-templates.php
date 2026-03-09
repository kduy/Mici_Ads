<?php
/**
 * Template Name: Design Templates
 *
 * Portfolio gallery page with Canva-style search, filter pills,
 * JS-populated gallery grid, login gate, and design detail modal.
 *
 * @package MiciAds
 */

get_header();
?>

<!-- Hero + Search (Canva-style) -->
<section class="hero" id="portfolio">
  <div class="hero__container">
    <h1 class="hero__title">
      <span class="hero__title-accent"><?php esc_html_e( 'Thiết kế', 'mici-ads' ); ?></span>
      <?php esc_html_e( 'tỏa sáng,', 'mici-ads' ); ?>
      <span class="hero__title-burst"><?php esc_html_e( 'doanh thu', 'mici-ads' ); ?></span>
      <?php esc_html_e( 'bùng nổ', 'mici-ads' ); ?>
    </h1>

    <!-- Tab pills -->
    <div class="hero__tabs">
      <button class="hero__tab">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/></svg>
        <?php esc_html_e( 'Mẫu thiết kế', 'mici-ads' ); ?>
      </button>
      <button class="hero__tab hero__tab--active">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <?php esc_html_e( 'Kho mẫu', 'mici-ads' ); ?>
      </button>
      <button class="hero__tab">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        <?php esc_html_e( 'Dịch vụ', 'mici-ads' ); ?>
      </button>
    </div>

    <!-- Search box -->
    <div class="hero__search">
      <svg class="hero__search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" class="hero__search-input" id="searchInput"
             placeholder="<?php esc_attr_e( 'Tìm kiếm mẫu thiết kế', 'mici-ads' ); ?>">
      <button class="hero__search-settings" aria-label="<?php esc_attr_e( 'Search settings', 'mici-ads' ); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 12h18M7.5 7.5l9 9M16.5 7.5l-9 9"/></svg>
      </button>
    </div>

    <!-- Row 1: Industry filter pills -->
    <div class="hero__filters">
      <button class="filter-pill filter-pill--clear" id="clearFilters" style="display:none"
              aria-label="<?php esc_attr_e( 'Clear filters', 'mici-ads' ); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
      <div class="filter-pill-group" id="industryFilters">
        <button class="filter-pill filter-pill--active" data-filter="all">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
          <?php esc_html_e( 'Tất cả', 'mici-ads' ); ?>
        </button>
        <button class="filter-pill" data-filter="nail"><?php esc_html_e( 'Tiệm Nail', 'mici-ads' ); ?></button>
        <button class="filter-pill" data-filter="beauty"><?php esc_html_e( 'Thẩm mỹ', 'mici-ads' ); ?></button>
        <button class="filter-pill" data-filter="restaurant"><?php esc_html_e( 'Nhà hàng', 'mici-ads' ); ?></button>
        <button class="filter-pill" data-filter="cafe"><?php esc_html_e( 'Quán cafe', 'mici-ads' ); ?></button>
        <button class="filter-pill" data-filter="others"><?php esc_html_e( 'Khác', 'mici-ads' ); ?></button>
      </div>
    </div>

    <!-- Row 2: Category & Style dropdowns -->
    <div class="hero__filters-secondary">
      <button class="filter-pill filter-pill--dropdown" id="categoryDropdown">
        <?php esc_html_e( 'Danh mục', 'mici-ads' ); ?>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <button class="filter-pill filter-pill--dropdown" id="styleDropdown">
        <?php esc_html_e( 'Phong cách', 'mici-ads' ); ?>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
      </button>
    </div>
  </div>

  <!-- Dropdown panels (hidden by default) -->
  <div class="dropdown-panel" id="categoryPanel">
    <div class="dropdown-panel__options" id="categoryFilters">
      <button class="filter-pill filter-pill--active" data-filter="all"><?php esc_html_e( 'Tất cả', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="menu"><?php esc_html_e( 'Thực đơn', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="flyer"><?php esc_html_e( 'Tờ rơi', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="logo"><?php esc_html_e( 'Logo', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="loyalty-card"><?php esc_html_e( 'Thẻ tích điểm', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="voucher"><?php esc_html_e( 'Phiếu quà tặng', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="website"><?php esc_html_e( 'Website', 'mici-ads' ); ?></button>
    </div>
  </div>
  <div class="dropdown-panel" id="stylePanel">
    <div class="dropdown-panel__options" id="styleFilters">
      <button class="filter-pill filter-pill--active" data-filter="all"><?php esc_html_e( 'Tất cả', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="feminine"><?php esc_html_e( 'Nữ tính', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="professional"><?php esc_html_e( 'Chuyên nghiệp', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="modern"><?php esc_html_e( 'Hiện đại', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="elegant"><?php esc_html_e( 'Sang trọng', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="minimalist"><?php esc_html_e( 'Tối giản', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="colorful"><?php esc_html_e( 'Nhiều màu', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="simple"><?php esc_html_e( 'Đơn giản', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="beautiful"><?php esc_html_e( 'Tinh tế', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="aesthetic"><?php esc_html_e( 'Thẩm mỹ', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="clean"><?php esc_html_e( 'Gọn gàng', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="pastel"><?php esc_html_e( 'Pastel', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="luxury"><?php esc_html_e( 'Cao cấp', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="bold"><?php esc_html_e( 'Nổi bật', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="vibrant"><?php esc_html_e( 'Rực rỡ', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="gold"><?php esc_html_e( 'Vàng gold', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="minimal"><?php esc_html_e( 'Tối giản', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="creative"><?php esc_html_e( 'Sáng tạo', 'mici-ads' ); ?></button>
      <button class="filter-pill" data-filter="classic"><?php esc_html_e( 'Cổ điển', 'mici-ads' ); ?></button>
    </div>
  </div>
</section>

<!-- Portfolio Gallery -->
<section class="gallery">
  <div class="gallery__container">
    <div class="gallery__header">
      <h2 class="gallery__title"><?php esc_html_e( 'Sản phẩm', 'mici-ads' ); ?></h2>
      <p class="gallery__count"><span id="resultCount">53</span> <?php esc_html_e( 'mẫu thiết kế', 'mici-ads' ); ?></p>
    </div>
    <div class="gallery__grid" id="galleryGrid">
      <!-- Gallery items injected by JS -->
    </div>

    <!-- Login gate: shown when guest has not logged in -->
    <div class="login-gate" id="loginGate">
      <div class="login-gate__fade"></div>
      <div class="login-gate__content">
        <div class="login-gate__icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            <circle cx="12" cy="16" r="1"/>
          </svg>
        </div>
        <h3 class="login-gate__title"><?php esc_html_e( 'Đăng nhập để xem thêm', 'mici-ads' ); ?></h3>
        <p class="login-gate__desc">
          <?php esc_html_e( 'Còn', 'mici-ads' ); ?>
          <span class="login-gate__count">0</span>
          <?php esc_html_e( 'mẫu thiết kế đang chờ bạn khám phá', 'mici-ads' ); ?>
        </p>
        <?php
        $auth_url = function_exists( 'mici_get_auth_page_url' ) ? mici_get_auth_page_url() : '';
        $login_link  = $auth_url ? add_query_arg( 'tab', 'login', $auth_url ) : wp_login_url( get_permalink() );
        $signup_link = $auth_url ? add_query_arg( 'tab', 'signup', $auth_url ) : wp_registration_url();
        ?>
        <a class="login-gate__btn" href="<?php echo esc_url( $login_link ); ?>">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          <?php esc_html_e( 'Đăng nhập', 'mici-ads' ); ?>
        </a>
        <a class="login-gate__btn login-gate__btn--outline" href="<?php echo esc_url( $signup_link ); ?>">
          <?php esc_html_e( 'Đăng ký miễn phí', 'mici-ads' ); ?>
        </a>
        <p class="login-gate__sub"><?php esc_html_e( 'Miễn phí', 'mici-ads' ); ?> &bull; <?php esc_html_e( 'Không cần thẻ tín dụng', 'mici-ads' ); ?></p>
      </div>
    </div>
  </div>
</section>

<!-- Design Detail Modal -->
<div class="modal-overlay" id="designModal">
  <div class="modal">
    <button class="modal__close" id="modalClose">&times;</button>
    <div class="modal__content" id="modalContent"></div>
  </div>
</div>

<?php get_footer(); ?>
