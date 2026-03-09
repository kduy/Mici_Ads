<?php
/**
 * Mici Ads Theme — Custom Post Type: Design
 *
 * Registers 'design' CPT and its meta box with all design fields.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// CPT Registration
// -------------------------------------------------------------------------

/**
 * Register the 'design' custom post type.
 */
function mici_register_cpt_design() {
	$labels = array(
		'name'               => esc_html__( 'Designs', 'mici-ads' ),
		'singular_name'      => esc_html__( 'Design', 'mici-ads' ),
		'add_new'            => esc_html__( 'Add New', 'mici-ads' ),
		'add_new_item'       => esc_html__( 'Add New Design', 'mici-ads' ),
		'edit_item'          => esc_html__( 'Edit Design', 'mici-ads' ),
		'new_item'           => esc_html__( 'New Design', 'mici-ads' ),
		'view_item'          => esc_html__( 'View Design', 'mici-ads' ),
		'search_items'       => esc_html__( 'Search Designs', 'mici-ads' ),
		'not_found'          => esc_html__( 'No designs found', 'mici-ads' ),
		'not_found_in_trash' => esc_html__( 'No designs found in trash', 'mici-ads' ),
	);

	register_post_type(
		'design',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-art',
			'supports'      => array( 'title', 'thumbnail', 'custom-fields' ),
			'show_in_rest'  => true,
			'rewrite'       => array( 'slug' => 'designs' ),
			'menu_position' => 5,
		)
	);
}
add_action( 'init', 'mici_register_cpt_design' );

// -------------------------------------------------------------------------
// Meta Box
// -------------------------------------------------------------------------

/**
 * Register the design meta box.
 */
function mici_add_design_meta_box() {
	add_meta_box(
		'mici_design_fields',
		esc_html__( 'Design Details', 'mici-ads' ),
		'mici_render_design_meta_box',
		'design',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mici_add_design_meta_box' );

/**
 * Render design meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function mici_render_design_meta_box( $post ) {
	wp_nonce_field( 'mici_design_save', 'mici_design_nonce' );

	$industry = get_post_meta( $post->ID, '_design_industry', true );
	$category = get_post_meta( $post->ID, '_design_category', true );
	$style    = get_post_meta( $post->ID, '_design_style', true );
	$theme    = get_post_meta( $post->ID, '_design_theme', true );
	$logo     = get_post_meta( $post->ID, '_design_logo', true );
	$sub      = get_post_meta( $post->ID, '_design_sub', true );
	$detail   = get_post_meta( $post->ID, '_design_detail', true );
	$colors   = array(
		get_post_meta( $post->ID, '_design_color_1', true ),
		get_post_meta( $post->ID, '_design_color_2', true ),
		get_post_meta( $post->ID, '_design_color_3', true ),
		get_post_meta( $post->ID, '_design_color_4', true ),
	);

	$industry_options = array( 'nail', 'beauty', 'restaurant', 'cafe', 'others' );
	$category_options = array( 'menu', 'flyer', 'logo', 'loyalty-card', 'voucher', 'website' );
	$style_options    = array(
		'feminine', 'professional', 'modern', 'elegant', 'minimalist',
		'colorful', 'simple', 'beautiful', 'aesthetic', 'clean',
		'pastel', 'luxury', 'bold', 'vibrant', 'gold', 'minimal',
		'creative', 'classic',
	);
	$theme_options    = array(
		'card-theme-1', 'card-theme-2', 'card-theme-3', 'card-theme-4', 'card-theme-5',
		'card-theme-6', 'card-theme-7', 'card-theme-8', 'card-theme-9', 'card-theme-10',
		'card-theme-11', 'card-theme-12', 'card-theme-13', 'card-theme-14', 'card-theme-15',
	);

	?>
	<table class="form-table">
		<tr>
			<th><?php esc_html_e( 'Industry', 'mici-ads' ); ?></th>
			<td><?php mici_render_select( 'mici_industry', $industry, $industry_options ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Category', 'mici-ads' ); ?></th>
			<td><?php mici_render_select( 'mici_category', $category, $category_options ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Style', 'mici-ads' ); ?></th>
			<td><?php mici_render_select( 'mici_style', $style, $style_options ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Card Theme', 'mici-ads' ); ?></th>
			<td><?php mici_render_select( 'mici_theme', $theme, $theme_options ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Logo Text', 'mici-ads' ); ?></th>
			<td><input type="text" name="mici_logo" value="<?php echo esc_attr( $logo ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Subtitle', 'mici-ads' ); ?></th>
			<td><input type="text" name="mici_sub" value="<?php echo esc_attr( $sub ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Detail', 'mici-ads' ); ?></th>
			<td><input type="text" name="mici_detail" value="<?php echo esc_attr( $detail ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Colors', 'mici-ads' ); ?></th>
			<td>
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
					<label><?php printf( esc_html__( 'Color %d', 'mici-ads' ), $i ); ?>
						<input type="color" name="mici_color_<?php echo esc_attr( $i ); ?>"
							value="<?php echo esc_attr( $colors[ $i - 1 ] ?: '#ffffff' ); ?>">
					</label>&nbsp;
				<?php endfor; ?>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Render a select field.
 *
 * @param string $name    Field name attribute.
 * @param string $current Currently selected value.
 * @param array  $options Available options.
 */
function mici_render_select( $name, $current, $options ) {
	echo '<select name="' . esc_attr( $name ) . '">';
	echo '<option value="">' . esc_html__( '— Select —', 'mici-ads' ) . '</option>';
	foreach ( $options as $option ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $option ),
			selected( $current, $option, false ),
			esc_html( $option )
		);
	}
	echo '</select>';
}

/** Save design meta — nonce verified, autosave skipped, capability checked. */
function mici_save_design_meta( $post_id ) {
	if ( ! isset( $_POST['mici_design_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_design_nonce'] ) ), 'mici_design_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'industry', 'category', 'style', 'theme', 'logo', 'sub', 'detail' ) as $field ) {
		$value = isset( $_POST[ 'mici_' . $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'mici_' . $field ] ) ) : '';
		update_post_meta( $post_id, '_design_' . $field, $value );
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		$value = isset( $_POST[ 'mici_color_' . $i ] ) ? sanitize_hex_color( wp_unslash( $_POST[ 'mici_color_' . $i ] ) ) : '#ffffff';
		update_post_meta( $post_id, '_design_color_' . $i, $value );
	}
}
add_action( 'save_post_design', 'mici_save_design_meta' );
