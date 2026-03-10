<?php
/**
 * Mici Ads Theme — Profile Shortcode Renderer
 *
 * Renders [mici_profile] shortcode with tabbed UI:
 * - Info tab: edit name, email, phone
 * - Liked Designs tab: grid of liked designs
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render profile shortcode.
 *
 * @return string HTML output.
 */
function mici_profile_shortcode() {
	// Redirect guests to auth page.
	if ( ! is_user_logged_in() ) {
		$auth_url = mici_get_auth_page_url();
		if ( $auth_url ) {
			wp_safe_redirect( $auth_url );
			exit;
		}
		return '<p>' . esc_html__( 'Vui lòng đăng nhập.', 'mici-ads' ) . '</p>';
	}

	$user    = wp_get_current_user();
	$user_id = $user->ID;
	$role    = mici_get_user_role();
	$phone   = get_user_meta( $user_id, '_mici_phone', true );
	$pending = get_user_meta( $user_id, '_mici_pending_email', true );

	$role_labels = array(
		'mici_vip'        => __( 'VIP', 'mici-ads' ),
		'mici_registered' => __( 'Thành viên', 'mici-ads' ),
		'mici_inactive'   => __( 'Chưa kích hoạt', 'mici-ads' ),
	);
	$role_label = $role_labels[ $role ] ?? __( 'Thành viên', 'mici-ads' );

	$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'info';
	$logout_url = wp_logout_url( mici_get_auth_page_url() ?: home_url( '/' ) );

	ob_start();
	?>
	<div class="mici-auth mici-profile">
		<?php mici_render_profile_notices( $user_id ); ?>

		<!-- User header -->
		<div class="mici-profile__header">
			<div class="mici-auth__avatar"><?php echo esc_html( mb_substr( $user->display_name, 0, 1 ) ); ?></div>
			<div>
				<strong><?php echo esc_html( $user->display_name ); ?></strong>
				<span class="mici-auth__role-badge"><?php echo esc_html( $role_label ); ?></span>
			</div>
		</div>

		<!-- Tabs -->
		<div class="mici-auth__tabs">
			<button class="mici-auth__tab<?php echo 'info' === $tab ? ' mici-auth__tab--active' : ''; ?>"
				data-tab="info"><?php esc_html_e( 'Thông tin', 'mici-ads' ); ?></button>
			<button class="mici-auth__tab<?php echo 'likes' === $tab ? ' mici-auth__tab--active' : ''; ?>"
				data-tab="likes"><?php esc_html_e( 'Yêu thích', 'mici-ads' ); ?></button>
		</div>

		<!-- Info tab -->
		<form class="mici-auth__form mici-profile__form<?php echo 'info' === $tab ? ' mici-auth__form--active' : ''; ?>"
			id="profileInfoForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mici_update_profile">
			<?php wp_nonce_field( 'mici_update_profile', 'mici_profile_nonce' ); ?>

			<div class="mici-auth__field">
				<label for="mici-profile-name"><?php esc_html_e( 'Họ tên', 'mici-ads' ); ?></label>
				<input type="text" id="mici-profile-name" name="mici_name"
					value="<?php echo esc_attr( $user->display_name ); ?>" required autocomplete="name">
			</div>

			<div class="mici-auth__field">
				<label for="mici-profile-email"><?php esc_html_e( 'Email', 'mici-ads' ); ?></label>
				<input type="email" id="mici-profile-email" name="mici_email"
					value="<?php echo esc_attr( $user->user_email ); ?>" required autocomplete="email">
				<?php if ( $pending ) : ?>
					<p class="mici-profile__pending-email">
						<?php printf( esc_html__( 'Đang chờ xác nhận: %s', 'mici-ads' ), '<strong>' . esc_html( $pending ) . '</strong>' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="mici-auth__field">
				<label for="mici-profile-phone"><?php esc_html_e( 'Số điện thoại', 'mici-ads' ); ?></label>
				<input type="tel" id="mici-profile-phone" name="mici_phone"
					value="<?php echo esc_attr( $phone ); ?>" placeholder="+84 xxx xxx xxx" autocomplete="tel">
			</div>

			<button type="submit" class="mici-auth__submit"><?php esc_html_e( 'Cập nhật', 'mici-ads' ); ?></button>
		</form>

		<!-- Likes tab -->
		<div class="mici-auth__form mici-profile__likes<?php echo 'likes' === $tab ? ' mici-auth__form--active' : ''; ?>"
			id="profileLikes" data-user-id="<?php echo esc_attr( $user_id ); ?>">
			<?php mici_render_liked_designs( $user_id ); ?>
		</div>

		<!-- Forgot password link + logout -->
		<div class="mici-profile__actions">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'forgot', mici_get_auth_page_url() ?: home_url( '/' ) ) ); ?>"
				class="mici-profile__link"><?php esc_html_e( 'Đổi mật khẩu', 'mici-ads' ); ?></a>
			<a href="<?php echo esc_url( $logout_url ); ?>" class="mici-auth__submit mici-auth__submit--outline">
				<?php esc_html_e( 'Đăng xuất', 'mici-ads' ); ?>
			</a>
		</div>
	</div>

	<script>
	document.querySelectorAll('.mici-auth__tab').forEach(function(t){
		t.addEventListener('click',function(){
			document.querySelectorAll('.mici-auth__tab').forEach(function(b){b.classList.remove('mici-auth__tab--active');});
			document.querySelectorAll('.mici-auth__form, .mici-profile__likes').forEach(function(f){f.classList.remove('mici-auth__form--active');});
			this.classList.add('mici-auth__tab--active');
			var target=this.dataset.tab==='info'?'profileInfoForm':'profileLikes';
			document.getElementById(target).classList.add('mici-auth__form--active');
		});
	});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'mici_profile', 'mici_profile_shortcode' );

/**
 * Render profile notices from query params and transients.
 *
 * @param int $user_id Current user ID.
 */
function mici_render_profile_notices( $user_id ) {
	if ( ! empty( $_GET['mici_profile_updated'] ) ) {
		$msg = __( 'Thông tin đã được cập nhật.', 'mici-ads' );
		if ( ! empty( $_GET['email_pending'] ) ) {
			$msg .= ' ' . __( 'Vui lòng kiểm tra email mới để xác nhận thay đổi.', 'mici-ads' );
		}
		echo '<div class="mici-auth__notice mici-auth__notice--success"><p>' . esc_html( $msg ) . '</p></div>';
	}

	if ( ! empty( $_GET['mici_email_confirmed'] ) ) {
		echo '<div class="mici-auth__notice mici-auth__notice--success"><p>' . esc_html__( 'Email đã được cập nhật thành công!', 'mici-ads' ) . '</p></div>';
	}

	$errors = get_transient( 'mici_profile_errors_' . $user_id );
	if ( $errors ) {
		delete_transient( 'mici_profile_errors_' . $user_id );
		echo '<div class="mici-auth__notice mici-auth__notice--error">';
		foreach ( $errors as $err ) {
			echo '<p>' . esc_html( $err ) . '</p>';
		}
		echo '</div>';
	}
}

/**
 * Render liked designs grid.
 *
 * @param int $user_id User ID.
 */
function mici_render_liked_designs( $user_id ) {
	$liked_ids = get_user_meta( $user_id, '_mici_liked_designs', true );
	$liked_ids = is_array( $liked_ids ) ? $liked_ids : array();

	if ( empty( $liked_ids ) ) {
		echo '<p class="mici-profile__empty">' . esc_html__( 'Bạn chưa thích mẫu thiết kế nào.', 'mici-ads' ) . '</p>';
		return;
	}

	$query = new WP_Query( array(
		'post_type'      => 'design',
		'post__in'       => $liked_ids,
		'posts_per_page' => count( $liked_ids ),
		'orderby'        => 'post__in',
		'no_found_rows'  => true,
	) );

	if ( ! $query->have_posts() ) {
		echo '<p class="mici-profile__empty">' . esc_html__( 'Bạn chưa thích mẫu thiết kế nào.', 'mici-ads' ) . '</p>';
		return;
	}

	echo '<div class="mici-profile__liked-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
		$title = get_the_title();
		?>
		<div class="mici-profile__liked-card" data-design-id="<?php echo esc_attr( get_the_ID() ); ?>">
			<?php if ( $thumb ) : ?>
				<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php else : ?>
				<div class="mici-profile__liked-placeholder"><?php echo esc_html( mb_substr( $title, 0, 1 ) ); ?></div>
			<?php endif; ?>
			<span class="mici-profile__liked-title"><?php echo esc_html( $title ); ?></span>
			<button type="button" class="mici-profile__unlike-btn" data-design-id="<?php echo esc_attr( get_the_ID() ); ?>"
				title="<?php esc_attr_e( 'Bỏ thích', 'mici-ads' ); ?>">♥</button>
		</div>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
}
