<?php
/**
 * Mici Ads Theme — Front-end Authentication Forms
 *
 * Provides shortcodes and handlers for:
 *   [mici_auth_forms] — tabbed login / signup with forgot-password link
 *
 * Signup creates a WP user with mici_inactive role, sends confirmation email.
 * Login authenticates via email OR phone number + password.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// Helper: get auth page URL (page using page-auth.php template)
// -------------------------------------------------------------------------

/**
 * Get the URL of the page using the Auth page template.
 *
 * @return string|false Page URL or false.
 */
function mici_get_auth_page_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-auth.php',
			'number'     => 1,
		)
	);
	return ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : false;
}

// -------------------------------------------------------------------------
// Signup handler (POST)
// -------------------------------------------------------------------------

/**
 * Process signup form submission.
 */
function mici_handle_signup() {
	if ( ! isset( $_POST['mici_signup_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_signup_nonce'] ) ), 'mici_signup' ) ) {
		return;
	}

	$email    = isset( $_POST['mici_email'] ) ? sanitize_email( wp_unslash( $_POST['mici_email'] ) ) : '';
	$phone    = isset( $_POST['mici_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_phone'] ) ) : '';
	$name     = isset( $_POST['mici_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_name'] ) ) : '';
	$password = isset( $_POST['mici_password'] ) ? $_POST['mici_password'] : ''; // phpcs:ignore -- raw password.

	$errors = array();

	// Validate required fields.
	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors[] = __( 'Vui lòng nhập email hợp lệ.', 'mici-ads' );
	}
	if ( empty( $password ) || strlen( $password ) < 6 ) {
		$errors[] = __( 'Mật khẩu phải có ít nhất 6 ký tự.', 'mici-ads' );
	}
	if ( empty( $name ) ) {
		$errors[] = __( 'Vui lòng nhập họ tên.', 'mici-ads' );
	}

	// Check duplicate email.
	if ( empty( $errors ) && email_exists( $email ) ) {
		$errors[] = __( 'Email này đã được đăng ký.', 'mici-ads' );
	}

	// Check duplicate phone (if provided).
	if ( ! empty( $phone ) ) {
		$phone = mici_normalize_phone( $phone );
		$existing = get_users(
			array(
				'meta_key'   => '_mici_phone',
				'meta_value' => $phone,
				'number'     => 1,
			)
		);
		if ( ! empty( $existing ) ) {
			$errors[] = __( 'Số điện thoại này đã được đăng ký.', 'mici-ads' );
		}
	}

	if ( ! empty( $errors ) ) {
		// Store errors in transient for display after redirect.
		set_transient( 'mici_signup_errors_' . wp_hash( $email ), $errors, 60 );
		$redirect = add_query_arg(
			array( 'tab' => 'signup', 'email' => rawurlencode( $email ) ),
			mici_get_auth_page_url() ?: home_url( '/' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	// Create user with email as username (sanitized).
	$username = sanitize_user( $email, true );
	$user_id  = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		set_transient( 'mici_signup_errors_' . wp_hash( $email ), array( $user_id->get_error_message() ), 60 );
		$redirect = add_query_arg( array( 'tab' => 'signup' ), mici_get_auth_page_url() ?: home_url( '/' ) );
		wp_safe_redirect( $redirect );
		exit;
	}

	// Set role to inactive until email confirmed.
	$user = new WP_User( $user_id );
	$user->set_role( 'mici_inactive' );

	// Store display name and phone.
	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
			'first_name'   => $name,
		)
	);
	if ( ! empty( $phone ) ) {
		update_user_meta( $user_id, '_mici_phone', $phone );
	}

	// Generate token and send confirmation email.
	$token = mici_generate_confirmation_token( $user_id );
	mici_send_confirmation_email( $user_id, $token );

	// Redirect with success.
	$redirect = add_query_arg( 'mici_signup_success', '1', mici_get_auth_page_url() ?: home_url( '/' ) );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_nopriv_mici_signup', 'mici_handle_signup' );
add_action( 'admin_post_mici_signup', 'mici_handle_signup' );

// -------------------------------------------------------------------------
// Login handler (POST)
// -------------------------------------------------------------------------

/**
 * Process login form submission.
 */
function mici_handle_login() {
	if ( ! isset( $_POST['mici_login_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_login_nonce'] ) ), 'mici_login' ) ) {
		return;
	}

	$credential = isset( $_POST['mici_credential'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_credential'] ) ) : '';
	$password   = isset( $_POST['mici_password'] ) ? $_POST['mici_password'] : ''; // phpcs:ignore

	// Resolve credential: could be email or phone number.
	$username = $credential;
	if ( ! is_email( $credential ) ) {
		// Try phone number lookup.
		$phone = mici_normalize_phone( $credential );
		$users = get_users(
			array(
				'meta_key'   => '_mici_phone',
				'meta_value' => $phone,
				'number'     => 1,
			)
		);
		if ( ! empty( $users ) ) {
			$username = $users[0]->user_login;
		}
	}

	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => true,
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		set_transient( 'mici_login_error_' . wp_hash( $credential ), $user->get_error_message(), 60 );
		$redirect = add_query_arg(
			array( 'tab' => 'login', 'credential' => rawurlencode( $credential ) ),
			mici_get_auth_page_url() ?: home_url( '/' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	// Redirect to templates page or referrer.
	$redirect = isset( $_POST['mici_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['mici_redirect'] ) ) : '';
	if ( empty( $redirect ) ) {
		// Find templates page.
		$pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'page-templates.php',
				'number'     => 1,
			)
		);
		$redirect = ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : home_url( '/' );
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_nopriv_mici_login', 'mici_handle_login' );
add_action( 'admin_post_mici_login', 'mici_handle_login' );

// -------------------------------------------------------------------------
// Phone number normalization
// -------------------------------------------------------------------------

/**
 * Normalize phone number: strip spaces/dashes, keep digits and leading +.
 *
 * @param string $phone Raw phone input.
 * @return string Normalized phone.
 */
function mici_normalize_phone( $phone ) {
	return preg_replace( '/[^\d+]/', '', trim( $phone ) );
}

// -------------------------------------------------------------------------
// Shortcode: [mici_auth_forms]
// -------------------------------------------------------------------------

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
				<a href="<?php echo esc_url( wp_lostpassword_url( mici_get_auth_page_url() ?: home_url( '/' ) ) ); ?>">
					<?php esc_html_e( 'Quên mật khẩu?', 'mici-ads' ); ?>
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
	</div>

	<script>
	/* Tab switcher for auth forms */
	document.querySelectorAll('.mici-auth__tab').forEach(function(tab) {
		tab.addEventListener('click', function() {
			var target = this.dataset.tab;
			document.querySelectorAll('.mici-auth__tab').forEach(function(t) { t.classList.remove('mici-auth__tab--active'); });
			document.querySelectorAll('.mici-auth__form').forEach(function(f) { f.classList.remove('mici-auth__form--active'); });
			this.classList.add('mici-auth__tab--active');
			document.getElementById(target === 'login' ? 'miciLoginForm' : 'miciSignupForm').classList.add('mici-auth__form--active');
		});
	});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'mici_auth_forms', 'mici_auth_forms_shortcode' );

// -------------------------------------------------------------------------
// Render helpers
// -------------------------------------------------------------------------

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
 * Render logged-in account summary.
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

	$label      = isset( $role_labels[ $role ] ) ? $role_labels[ $role ] : __( 'Thành viên', 'mici-ads' );
	$logout_url = wp_logout_url( mici_get_auth_page_url() ?: home_url( '/' ) );

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
		<a href="<?php echo esc_url( $logout_url ); ?>" class="mici-auth__submit mici-auth__submit--outline">
			<?php esc_html_e( 'Đăng xuất', 'mici-ads' ); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get the URL of the templates page.
 *
 * @return string URL.
 */
function mici_get_templates_page_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates.php',
			'number'     => 1,
		)
	);
	return ! empty( $pages ) ? get_permalink( $pages[0]->ID ) : home_url( '/' );
}
