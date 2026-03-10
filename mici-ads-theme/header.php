<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="header">
	<div class="header__container">

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo">
			<span class="header__logo-icon">M</span>
			<span class="header__logo-text"><?php bloginfo( 'name' ); ?></span>
		</a>

		<nav class="header__nav header__nav--pill" aria-label="<?php esc_attr_e( 'Primary Navigation', 'mici-ads' ); ?>">

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
				class="header__nav-link<?php echo is_front_page() ? ' header__nav-link--active' : ''; ?>">
				<?php esc_html_e( 'Trang chủ', 'mici-ads' ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/#services' ) ); ?>"
				class="header__nav-link">
				<?php esc_html_e( 'Dịch vụ', 'mici-ads' ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/#portfolio' ) ); ?>"
				class="header__nav-link<?php echo is_page_template( 'page-templates.php' ) ? ' header__nav-link--active' : ''; ?>">
				<?php esc_html_e( 'Mẫu thiết kế', 'mici-ads' ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/#testimonials' ) ); ?>"
				class="header__nav-link">
				<?php esc_html_e( 'Đánh giá', 'mici-ads' ); ?>
			</a>

			<a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>"
				class="header__nav-link">
				<?php esc_html_e( 'FAQ', 'mici-ads' ); ?>
			</a>

			<?php if ( is_user_logged_in() && function_exists( 'mici_get_profile_page_url' ) ) : ?>
				<a href="<?php echo esc_url( mici_get_profile_page_url() ?: home_url( '/' ) ); ?>"
					class="header__nav-link<?php echo is_page_template( 'page-profile.php' ) ? ' header__nav-link--active' : ''; ?>">
					<?php esc_html_e( 'Tài khoản', 'mici-ads' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( function_exists( 'mici_get_auth_page_url' ) && mici_get_auth_page_url() ? mici_get_auth_page_url() : wp_login_url() ); ?>"
					class="header__nav-link">
					<?php esc_html_e( 'Đăng nhập', 'mici-ads' ); ?>
				</a>
			<?php endif; ?>

			<a href="tel:+49123456789" class="header__nav-link header__nav-link--cta">+49 123 456 789</a>

		</nav>

		<button class="header__menu-btn" aria-label="<?php esc_attr_e( 'Toggle menu', 'mici-ads' ); ?>">
			<span></span><span></span><span></span>
		</button>

	</div>
</header>
