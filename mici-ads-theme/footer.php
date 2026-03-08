<?php
/**
 * Mici Ads Theme — Footer Template
 *
 * Landing page: full 4-column footer with brand, contact, hours, legal.
 * Other pages:  minimal copyright footer.
 *
 * @package MiciAds
 */
?>

<?php if ( is_front_page() ) : ?>

<footer class="footer">
	<div class="footer__container">
		<div class="footer__grid">

			<!-- Brand -->
			<div class="footer__col">
				<div class="footer__brand">
					<span class="header__logo-icon">M</span>
					<span class="header__logo-text"><?php bloginfo( 'name' ); ?></span>
				</div>
				<p class="footer__desc">
					<?php esc_html_e( 'Dịch vụ thiết kế, website, chụp ảnh và marketing cho cộng đồng Việt tại Đức.', 'mici-ads' ); ?>
				</p>
			</div>

			<!-- Liên hệ -->
			<div class="footer__col">
				<h4 class="footer__heading"><?php esc_html_e( 'Liên hệ', 'mici-ads' ); ?></h4>
				<ul class="footer__list">
					<li><?php esc_html_e( 'Email: info@miciads.de', 'mici-ads' ); ?></li>
					<li>
						<?php esc_html_e( 'Tel: ', 'mici-ads' ); ?>
						<a href="tel:+49123456789" class="footer__link">+49 123 456 789</a>
					</li>
					<li><?php esc_html_e( 'München, Deutschland', 'mici-ads' ); ?></li>
				</ul>
			</div>

			<!-- Giờ làm việc -->
			<div class="footer__col">
				<h4 class="footer__heading"><?php esc_html_e( 'Giờ làm việc', 'mici-ads' ); ?></h4>
				<ul class="footer__list">
					<li><?php esc_html_e( 'Mo – Fr: 9:00 – 18:00', 'mici-ads' ); ?></li>
					<li><?php esc_html_e( 'Sa: 10:00 – 14:00', 'mici-ads' ); ?></li>
					<li><?php esc_html_e( 'So: Geschlossen', 'mici-ads' ); ?></li>
				</ul>
			</div>

			<!-- Rechtliches -->
			<div class="footer__col">
				<h4 class="footer__heading"><?php esc_html_e( 'Rechtliches', 'mici-ads' ); ?></h4>
				<ul class="footer__list">
					<li>
						<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>" class="footer__link">
							<?php esc_html_e( 'Impressum', 'mici-ads' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>" class="footer__link">
							<?php esc_html_e( 'Datenschutz', 'mici-ads' ); ?>
						</a>
					</li>
				</ul>
			</div>

		</div>

		<div class="footer__bottom">
			<p class="footer__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php bloginfo( 'name' ); ?>.
				<?php esc_html_e( 'Alle Rechte vorbehalten.', 'mici-ads' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php else : ?>

<footer class="footer footer--minimal">
	<div class="footer__container">
		<div class="footer__bottom">
			<p class="footer__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php bloginfo( 'name' ); ?>.
				<?php esc_html_e( 'Alle Rechte vorbehalten.', 'mici-ads' ); ?>
				&mdash;
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>" class="footer__link">Impressum</a>
				&middot;
				<a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>" class="footer__link">Datenschutz</a>
			</p>
		</div>
	</div>
</footer>

<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
