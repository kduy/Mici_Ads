<?php
/**
 * Mici Ads Theme — Image Processing Meta Box
 *
 * Adds the "Image Processing Tools" meta box to Design CPT edit screens.
 * Enqueues admin scripts/styles and renders the tabbed UI
 * for compress, PDF-to-JPEG, and watermark tools.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue admin scripts and styles for image processing on Design edit screens.
 *
 * @param string $hook Current admin page hook.
 */
function mici_enqueue_image_processing_scripts( $hook ) {
	global $post_type;
	if ( 'design' !== $post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_style(
		'mici-admin-image-processing',
		MICI_THEME_URI . '/css/admin-image-processing.css',
		array(),
		MICI_THEME_VERSION
	);

	wp_enqueue_script(
		'mici-admin-image-processing',
		MICI_THEME_URI . '/js/admin-image-processing.js',
		array( 'jquery' ),
		MICI_THEME_VERSION,
		true
	);

	$default_wm = MICI_THEME_URI . '/images/watermark-mici-ads.png';

	wp_localize_script( 'mici-admin-image-processing', 'miciImageProc', array(
		'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
		'nonce'            => wp_create_nonce( 'mici_image_processing' ),
		'postId'           => get_the_ID(),
		'defaultWatermark' => $default_wm,
		'hasImagick'       => mici_has_imagick(),
		'i18n'             => array(
			'processing'   => __( 'Đang xử lý...', 'mici-ads' ),
			'noImagick'    => __( 'PDF processing requires Imagick extension.', 'mici-ads' ),
			'selectImage'  => __( 'Chọn hình ảnh', 'mici-ads' ),
			'selectPdf'    => __( 'Chọn file PDF', 'mici-ads' ),
			'addToGallery' => __( 'Thêm vào Gallery', 'mici-ads' ),
			'addAllGallery' => __( 'Thêm tất cả vào Gallery', 'mici-ads' ),
			'saved'        => __( 'Đã lưu!', 'mici-ads' ),
			'error'        => __( 'Lỗi:', 'mici-ads' ),
		),
	) );
}
add_action( 'admin_enqueue_scripts', 'mici_enqueue_image_processing_scripts' );

/**
 * Register the Image Processing Tools meta box.
 */
function mici_add_image_processing_meta_box() {
	add_meta_box(
		'mici_image_processing',
		esc_html__( 'Image Processing Tools', 'mici-ads' ),
		'mici_render_image_processing_meta_box',
		'design',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'mici_add_image_processing_meta_box' );

/**
 * Render the tabbed image processing meta box UI.
 *
 * @param WP_Post $post Current post object.
 */
function mici_render_image_processing_meta_box( $post ) {
	?>
	<div class="mici-proc">
		<div class="mici-proc__tabs">
			<button type="button" class="mici-proc__tab mici-proc__tab--active" data-panel="compress">
				<?php esc_html_e( 'Nén ảnh', 'mici-ads' ); ?>
			</button>
			<button type="button" class="mici-proc__tab" data-panel="pdf">
				<?php esc_html_e( 'PDF → JPEG', 'mici-ads' ); ?>
			</button>
			<button type="button" class="mici-proc__tab" data-panel="watermark">
				<?php esc_html_e( 'Watermark', 'mici-ads' ); ?>
			</button>
		</div>

		<?php mici_render_compress_panel(); ?>
		<?php mici_render_pdf_panel(); ?>
		<?php mici_render_watermark_panel(); ?>
	</div>
	<?php
}

/** Render the compress tab panel. */
function mici_render_compress_panel() {
	?>
	<div class="mici-proc__panel mici-proc__panel--active" data-panel="compress">
		<div class="mici-proc__source">
			<button type="button" class="button mici-proc__pick" data-target="compress" data-type="image">
				<?php esc_html_e( 'Chọn hình ảnh', 'mici-ads' ); ?>
			</button>
			<span class="mici-proc__filename" data-target="compress"></span>
			<div class="mici-proc__preview" data-target="compress"></div>
		</div>
		<input type="hidden" class="mici-proc__input-id" data-target="compress" value="">

		<div class="mici-proc__controls">
			<label><?php esc_html_e( 'Chất lượng:', 'mici-ads' ); ?>
				<input type="range" class="mici-proc__range" data-target="compress-quality" min="10" max="100" value="75">
				<span class="mici-proc__range-val">75%</span>
			</label>
			<label><?php esc_html_e( 'Định dạng:', 'mici-ads' ); ?>
				<select class="mici-proc__select" data-target="compress-format">
					<option value="jpeg">JPEG</option>
					<option value="png">PNG</option>
					<option value="webp">WebP</option>
				</select>
			</label>
		</div>

		<button type="button" class="button button-primary mici-proc__run" data-action="mici_compress_image">
			<?php esc_html_e( 'Nén ảnh', 'mici-ads' ); ?>
		</button>
		<div class="mici-proc__status" data-target="compress"></div>
		<div class="mici-proc__result" data-target="compress"></div>
	</div>
	<?php
}

/** Render the PDF-to-JPEG tab panel. */
function mici_render_pdf_panel() {
	?>
	<div class="mici-proc__panel" data-panel="pdf">
		<?php if ( ! mici_has_imagick() ) : ?>
			<p class="mici-proc__notice"><?php esc_html_e( 'PDF processing requires the Imagick PHP extension.', 'mici-ads' ); ?></p>
		<?php endif; ?>

		<div class="mici-proc__source">
			<button type="button" class="button mici-proc__pick" data-target="pdf" data-type="application/pdf">
				<?php esc_html_e( 'Chọn file PDF', 'mici-ads' ); ?>
			</button>
			<span class="mici-proc__filename" data-target="pdf"></span>
		</div>
		<input type="hidden" class="mici-proc__input-id" data-target="pdf" value="">

		<div class="mici-proc__controls">
			<label><?php esc_html_e( 'DPI:', 'mici-ads' ); ?>
				<input type="number" class="mici-proc__number" data-target="pdf-dpi" min="72" max="300" value="150" step="1">
			</label>
			<label><?php esc_html_e( 'Chất lượng:', 'mici-ads' ); ?>
				<input type="range" class="mici-proc__range" data-target="pdf-quality" min="10" max="100" value="85">
				<span class="mici-proc__range-val">85%</span>
			</label>
		</div>

		<button type="button" class="button button-primary mici-proc__run" data-action="mici_pdf_to_jpeg" <?php disabled( ! mici_has_imagick() ); ?>>
			<?php esc_html_e( 'Chuyển đổi PDF', 'mici-ads' ); ?>
		</button>
		<div class="mici-proc__status" data-target="pdf"></div>
		<div class="mici-proc__result" data-target="pdf"></div>
	</div>
	<?php
}

/** Render the watermark tab panel. */
function mici_render_watermark_panel() {
	?>
	<div class="mici-proc__panel" data-panel="watermark">
		<div class="mici-proc__source">
			<button type="button" class="button mici-proc__pick" data-target="watermark" data-type="image">
				<?php esc_html_e( 'Chọn hình ảnh', 'mici-ads' ); ?>
			</button>
			<span class="mici-proc__filename" data-target="watermark"></span>
			<div class="mici-proc__preview" data-target="watermark"></div>
		</div>
		<input type="hidden" class="mici-proc__input-id" data-target="watermark" value="">

		<div class="mici-proc__controls">
			<label><?php esc_html_e( 'Watermark:', 'mici-ads' ); ?>
				<select class="mici-proc__select" data-target="wm-source">
					<option value="default"><?php esc_html_e( 'Logo Mici Ads', 'mici-ads' ); ?></option>
					<option value="custom"><?php esc_html_e( 'Tùy chọn...', 'mici-ads' ); ?></option>
				</select>
			</label>
			<div class="mici-proc__wm-custom" style="display:none;">
				<button type="button" class="button mici-proc__pick" data-target="wm-custom" data-type="image">
					<?php esc_html_e( 'Chọn watermark', 'mici-ads' ); ?>
				</button>
				<span class="mici-proc__filename" data-target="wm-custom"></span>
			</div>
			<input type="hidden" class="mici-proc__input-id" data-target="wm-custom" value="">

			<label><?php esc_html_e( 'Độ mờ:', 'mici-ads' ); ?>
				<input type="range" class="mici-proc__range" data-target="wm-opacity" min="5" max="100" value="30">
				<span class="mici-proc__range-val">30%</span>
			</label>
			<label><?php esc_html_e( 'Vị trí:', 'mici-ads' ); ?>
				<select class="mici-proc__select" data-target="wm-position">
					<option value="center"><?php esc_html_e( 'Giữa', 'mici-ads' ); ?></option>
					<option value="bottom-right"><?php esc_html_e( 'Góc dưới phải', 'mici-ads' ); ?></option>
					<option value="tiled"><?php esc_html_e( 'Lặp lại', 'mici-ads' ); ?></option>
				</select>
			</label>
		</div>

		<button type="button" class="button button-primary mici-proc__run" data-action="mici_apply_watermark">
			<?php esc_html_e( 'Áp dụng Watermark', 'mici-ads' ); ?>
		</button>
		<div class="mici-proc__status" data-target="watermark"></div>
		<div class="mici-proc__result" data-target="watermark"></div>
	</div>
	<?php
}
