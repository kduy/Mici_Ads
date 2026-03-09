<?php
/**
 * Mici Ads Theme — Design Gallery Meta Box
 *
 * Adds a multi-image gallery uploader to the Design CPT edit screen.
 * Stores attachment IDs as comma-separated string in _design_gallery meta.
 * Supports up to 30 images per design card.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue WP media uploader + gallery admin script on design edit screens.
 *
 * @param string $hook Current admin page hook.
 */
function mici_enqueue_gallery_admin_scripts( $hook ) {
	global $post_type;
	if ( 'design' !== $post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'mici-admin-gallery',
		MICI_THEME_URI . '/js/admin-gallery.js',
		array( 'jquery' ),
		MICI_THEME_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'mici_enqueue_gallery_admin_scripts' );

/**
 * Register gallery meta box.
 */
function mici_add_gallery_meta_box() {
	add_meta_box(
		'mici_design_gallery',
		esc_html__( 'Design Gallery (up to 30 images)', 'mici-ads' ),
		'mici_render_gallery_meta_box',
		'design',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'mici_add_gallery_meta_box' );

/**
 * Render gallery meta box with thumbnail previews and add/remove buttons.
 *
 * @param WP_Post $post Current post object.
 */
function mici_render_gallery_meta_box( $post ) {
	wp_nonce_field( 'mici_gallery_save', 'mici_gallery_nonce' );
	$gallery_ids = get_post_meta( $post->ID, '_design_gallery', true );
	$ids_array   = $gallery_ids ? array_filter( explode( ',', $gallery_ids ) ) : array();
	?>
	<div id="mici-gallery-container">
		<div id="mici-gallery-images" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
			<?php foreach ( $ids_array as $att_id ) :
				$img_url = wp_get_attachment_image_url( intval( $att_id ), 'thumbnail' );
				if ( ! $img_url ) {
					continue;
				}
			?>
				<div class="mici-gallery-item" data-id="<?php echo esc_attr( $att_id ); ?>" style="position:relative;">
					<img src="<?php echo esc_url( $img_url ); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
					<button type="button" class="mici-gallery-remove" style="position:absolute;top:-6px;right:-6px;background:#e00;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">&times;</button>
				</div>
			<?php endforeach; ?>
		</div>
		<input type="hidden" name="mici_gallery_ids" id="mici-gallery-ids" value="<?php echo esc_attr( $gallery_ids ); ?>">
		<button type="button" id="mici-gallery-add" class="button"><?php esc_html_e( 'Add Gallery Images', 'mici-ads' ); ?></button>
		<p class="description"><?php esc_html_e( 'Select up to 30 images. Click × to remove.', 'mici-ads' ); ?></p>
	</div>
	<?php
}

/**
 * Save gallery attachment IDs on post save.
 *
 * @param int $post_id Post ID.
 */
function mici_save_gallery_meta( $post_id ) {
	if ( ! isset( $_POST['mici_gallery_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_gallery_nonce'] ) ), 'mici_gallery_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw   = isset( $_POST['mici_gallery_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['mici_gallery_ids'] ) ) : '';
	$clean = array_filter( array_map( 'intval', explode( ',', $raw ) ) );
	$clean = array_slice( $clean, 0, 30 ); // Enforce 30-image limit.

	update_post_meta( $post_id, '_design_gallery', implode( ',', $clean ) );
}
add_action( 'save_post_design', 'mici_save_gallery_meta' );
