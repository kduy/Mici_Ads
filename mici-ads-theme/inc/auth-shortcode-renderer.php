<?php
/**
 * Mici Ads Theme — Auth Shortcode Renderer
 *
 * Renders the [mici_auth_forms] shortcode:
 *   - Tabbed login / signup forms for guests
 *   - Forgot / reset password forms
 *   - Logged-in account summary with profile link
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render tabbed login/signup forms.
 *
 * @return string HTML output.
 */
function mici_auth_forms_shortcode() {
	// Already logged in — show account info.
	if ( is_user_logged_in() ) {
		return mici_render_account_info();
	}

	$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'login';

	ob_start();
	?>
	<div class="mici-auth">
		<?php mici_render_auth_notices(); ?>

		<div class="mici-auth__tabs">
			<button class="mici-auth__tab <?php echo 'login' === $tab ? 'mici-auth__tab--active' : ''; ?>"
				data-tab="login"><?php esc_html_e( 'Đăng nhập', 'mici-ads' ); ?></button>
			<button class="mici-auth__tab <?php echo 'signup' === $tab ? 'mici-auth__tab--active' : ''; ?>"
				data-tab="signup"><?php esc_html_e( 'Đăng ký', 'mici-ads' ); ?></button>
		</div>

		<!-- Login Form -->
		<form class="mici-auth__form <?php echo 'login' === $tab ? 'mici-auth__form--active' : ''; ?>"
			id="miciLoginForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mici_login">
			<?php wp_nonce_field( 'mici_login', 'mici_login_nonce' ); ?>
			<input type="hidden" name="mici_redirect" value="<?php echo esc_url( mici_get_templates_page_url() ); ?>">

			<div class="mici-auth__field">
				<label for="mici-login-cred"><?php esc_html_e( 'Email hoặc số điện thoại', 'mici-ads' ); ?></label>
				<input type="text" id="mici-login-cred" name="mici_credential" required
					placeholder="email@example.com" autocomplete="username"
					value="<?php echo esc_attr( isset( $_GET['credential'] ) ? sanitize_text_field( wp_unslash( $_GET['credential'] ) ) : '' ); ?>">
			</div>

			<div class="mici-auth__field">
				<label for="mici-login-pw"><?php esc_html_e( 'Mật khẩu', 'mici-ads' ); ?></label>
				<input type="password" id="mici-login-pw" name="mici_password" required
					placeholder="••••••" autocomplete="current-password">
			</div>

			<button type="submit" class="mici-auth__submit"><?php esc_html_e( 'Đăng nhập', 'mici-ads' ); ?></button>

			<p class="mici-auth__link">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'forgot', mici_get_auth_page_url() ?: home_url( '/' ) ) ); ?>"
					data-tab="forgot"><?php esc_html_e( 'Quên mật khẩu?', 'mici-ads' ); ?>
				</a>
			</p>
		</form>

		<!-- Signup Form -->
		<form class="mici-auth__form <?php echo 'signup' === $tab ? 'mici-auth__form--active' : ''; ?>"
			id="miciSignupForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mici_signup">
			<?php wp_nonce_field( 'mici_signup', 'mici_signup_nonce' ); ?>

			<div class="mici-auth__field">
				<label for="mici-signup-name"><?php esc_html_e( 'Họ tên', 'mici-ads' ); ?></label>
				<input type="text" id="mici-signup-name" name="mici_name" required
					placeholder="Nguyễn Văn A" autocomplete="name">
			</div>

			<div class="mici-auth__field">
				<label for="mici-signup-email"><?php esc_html_e( 'Email', 'mici-ads' ); ?> <span class="required">*</span></label>
				<input type="email" id="mici-signup-email" name="mici_email" required
					placeholder="email@example.com" autocomplete="email"
					value="<?php echo esc_attr( isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '' ); ?>">
			</div>

			<div class="mici-auth__field">
				<label for="mici-signup-phone"><?php esc_html_e( 'Số điện thoại', 'mici-ads' ); ?> <span class="optional">(<?php esc_html_e( 'không bắt buộc', 'mici-ads' ); ?>)</span></label>
				<input type="tel" id="mici-signup-phone" name="mici_phone"
					placeholder="+84 xxx xxx xxx" autocomplete="tel">
			</div>

			<div class="mici-auth__field">
				<label for="mici-signup-pw"><?php esc_html_e( 'Mật khẩu', 'mici-ads' ); ?> <span class="required">*</span></label>
				<input type="password" id="mici-signup-pw" name="mici_password" required minlength="6"
					placeholder="Ít nhất 6 ký tự" autocomplete="new-password">
			</div>

			<button type="submit" class="mici-auth__submit"><?php esc_html_e( 'Đăng ký', 'mici-ads' ); ?></button>
		</form>

		<!-- Forgot Password Form -->
		<form class="mici-auth__form mici-auth__form--forgot <?php echo 'forgot' === $tab ? 'mici-auth__form--active' : ''; ?>"
			id="miciForgotForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mici_forgot_password">
			<?php wp_nonce_field( 'mici_forgot_password', 'mici_forgot_nonce' ); ?>

			<?php if ( ! empty( $_GET['sent'] ) ) : ?>
				<div class="mici-auth__notice mici-auth__notice--success">
					<p><?php esc_html_e( 'Nếu email tồn tại, liên kết đặt lại mật khẩu đã được gửi.', 'mici-ads' ); ?></p>
				</div>
			<?php endif; ?>

			<?php
			$forgot_err = get_transient( 'mici_forgot_error' );
			if ( $forgot_err ) {
				delete_transient( 'mici_forgot_error' );
				echo '<div class="mici-auth__notice mici-auth__notice--error"><p>' . esc_html( $forgot_err ) . '</p></div>';
			}
			?>

			<p style="color:rgba(255,255,255,.6);font-size:13px;margin-bottom:16px;">
				<?php esc_html_e( 'Nhập email để nhận liên kết đặt lại mật khẩu.', 'mici-ads' ); ?>
			</p>

			<div class="mici-auth__field">
				<label for="mici-forgot-email"><?php esc_html_e( 'Email', 'mici-ads' ); ?></label>
				<input type="email" id="mici-forgot-email" name="mici_email" required
					placeholder="email@example.com" autocomplete="email">
			</div>

			<button type="submit" class="mici-auth__submit"><?php esc_html_e( 'Gửi liên kết', 'mici-ads' ); ?></button>
			<a href="<?php echo esc_url( mici_get_auth_page_url() ?: home_url( '/' ) ); ?>" class="mici-auth__back-link">
				<?php esc_html_e( '← Quay lại đăng nhập', 'mici-ads' ); ?>
			</a>
		</form>

		<?php // Reset password form (shown when clicking link from email). ?>
		<?php if ( 'reset' === $tab && ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) : ?>
		<form class="mici-auth__form mici-auth__form--reset mici-auth__form--active"
			id="miciResetForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mici_reset_password">
			<input type="hidden" name="mici_key" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['key'] ) ) ); ?>">
			<input type="hidden" name="mici_login" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['login'] ) ) ); ?>">
			<?php wp_nonce_field( 'mici_reset_password', 'mici_reset_nonce' ); ?>

			<?php
			$reset_errors = get_transient( 'mici_reset_errors' );
			if ( $reset_errors ) {
				delete_transient( 'mici_reset_errors' );
				echo '<div class="mici-auth__notice mici-auth__notice--error">';
				foreach ( $reset_errors as $err ) {
					echo '<p>' . esc_html( $err ) . '</p>';
				}
				echo '</div>';
			}
			?>

			<div class="mici-auth__field">
				<label for="mici-reset-pw"><?php esc_html_e( 'Mật khẩu mới', 'mici-ads' ); ?></label>
				<input type="password" id="mici-reset-pw" name="mici_password" required minlength="6"
					placeholder="Ít nhất 6 ký tự" autocomplete="new-password">
			</div>
			<div class="mici-auth__field">
				<label for="mici-reset-pw2"><?php esc_html_e( 'Xác nhận mật khẩu', 'mici-ads' ); ?></label>
				<input type="password" id="mici-reset-pw2" name="mici_password_confirm" required minlength="6"
					placeholder="Nhập lại mật khẩu" autocomplete="new-password">
			</div>

			<button type="submit" class="mici-auth__submit"><?php esc_html_e( 'Đặt mật khẩu mới', 'mici-ads' ); ?></button>
		</form>
		<?php endif; ?>

		<?php if ( ! empty( $_GET['reset_success'] ) ) : ?>
			<div class="mici-auth__notice mici-auth__notice--success">
				<p><?php esc_html_e( 'Mật khẩu đã được đặt lại thành công! Bạn có thể đăng nhập.', 'mici-ads' ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<script>
	/* Tab switcher for auth forms */
	document.querySelectorAll('.mici-auth__tab').forEach(function(tab) {
		tab.addEventListener('click', function() {
			var target = this.dataset.tab;
			document.querySelectorAll('.mici-auth__tab').forEach(function(t) { t.classList.remove('mici-auth__tab--active'); });
			document.querySelectorAll('.mici-auth__form').forEach(function(f) { f.classList.remove('mici-auth__form--active'); });
			this.classList.add('mici-auth__tab--active');
			var formMap = {login:'miciLoginForm',signup:'miciSignupForm',forgot:'miciForgotForm'};
			var el = document.getElementById(formMap[target]);
			if(el) el.classList.add('mici-auth__form--active');
		});
	});
	/* Forgot password link triggers tab switch */
	document.querySelectorAll('[data-tab="forgot"]').forEach(function(a){
		a.addEventListener('click',function(e){
			e.preventDefault();
			document.querySelectorAll('.mici-auth__tab').forEach(function(t){t.classList.remove('mici-auth__tab--active');});
			document.querySelectorAll('.mici-auth__form').forEach(function(f){f.classList.remove('mici-auth__form--active');});
			document.getElementById('miciForgotForm').classList.add('mici-auth__form--active');
		});
	});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'mici_auth_forms', 'mici_auth_forms_shortcode' );

/**
 * Render status notices (errors, success messages).
 */
function mici_render_auth_notices() {
	// Signup success.
	if ( ! empty( $_GET['mici_signup_success'] ) ) {
		echo '<div class="mici-auth__notice mici-auth__notice--success">';
		esc_html_e( 'Đăng ký thành công! Vui lòng kiểm tra email để xác nhận tài khoản.', 'mici-ads' );
		echo '</div>';
		return;
	}

	// Email confirmed.
	if ( ! empty( $_GET['mici_confirmed'] ) ) {
		echo '<div class="mici-auth__notice mici-auth__notice--success">';
		esc_html_e( 'Email đã xác nhận thành công! Bạn có thể đăng nhập ngay.', 'mici-ads' );
		echo '</div>';
		return;
	}

	// Signup errors.
	$email  = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
	$errors = get_transient( 'mici_signup_errors_' . wp_hash( $email ) );
	if ( $errors ) {
		delete_transient( 'mici_signup_errors_' . wp_hash( $email ) );
		echo '<div class="mici-auth__notice mici-auth__notice--error">';
		foreach ( $errors as $err ) {
			echo '<p>' . esc_html( $err ) . '</p>';
		}
		echo '</div>';
		return;
	}

	// Login error.
	$cred  = isset( $_GET['credential'] ) ? sanitize_text_field( wp_unslash( $_GET['credential'] ) ) : '';
	$error = get_transient( 'mici_login_error_' . wp_hash( $cred ) );
	if ( $error ) {
		delete_transient( 'mici_login_error_' . wp_hash( $cred ) );
		echo '<div class="mici-auth__notice mici-auth__notice--error"><p>' . esc_html( $error ) . '</p></div>';
	}
}

/**
 * Render logged-in account summary with profile link.
 *
 * @return string HTML.
 */
function mici_render_account_info() {
	$user = wp_get_current_user();
	$role = mici_get_user_role();

	$role_labels = array(
		'mici_vip'        => __( 'VIP', 'mici-ads' ),
		'mici_registered' => __( 'Thành viên', 'mici-ads' ),
		'mici_inactive'   => __( 'Chưa kích hoạt', 'mici-ads' ),
		'guest'           => __( 'Khách', 'mici-ads' ),
	);

	$label       = isset( $role_labels[ $role ] ) ? $role_labels[ $role ] : __( 'Thành viên', 'mici-ads' );
	$logout_url  = wp_logout_url( mici_get_auth_page_url() ?: home_url( '/' ) );
	$profile_url = function_exists( 'mici_get_profile_page_url' ) ? mici_get_profile_page_url() : false;

	ob_start();
	?>
	<div class="mici-auth mici-auth--logged-in">
		<div class="mici-auth__user">
			<div class="mici-auth__avatar"><?php echo esc_html( mb_substr( $user->display_name, 0, 1 ) ); ?></div>
			<div>
				<strong><?php echo esc_html( $user->display_name ); ?></strong>
				<span class="mici-auth__role-badge"><?php echo esc_html( $label ); ?></span>
			</div>
		</div>
		<?php if ( $profile_url ) : ?>
			<a href="<?php echo esc_url( $profile_url ); ?>" class="mici-auth__submit">
				<?php esc_html_e( 'Chỉnh sửa hồ sơ', 'mici-ads' ); ?>
			</a>
		<?php endif; ?>
		<a href="<?php echo esc_url( $logout_url ); ?>" class="mici-auth__submit mici-auth__submit--outline">
			<?php esc_html_e( 'Đăng xuất', 'mici-ads' ); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}
