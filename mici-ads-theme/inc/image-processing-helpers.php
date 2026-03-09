<?php
/**
 * Mici Ads Theme — Image Processing Shared Helpers
 *
 * Utility functions used by the image processing meta box and AJAX handlers:
 * Imagick detection, file saving, gallery integration, validation.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if the Imagick PHP extension is available.
 *
 * @return bool
 */
function mici_has_imagick() {
	return extension_loaded( 'imagick' ) && class_exists( 'Imagick' );
}

/**
 * Generate a unique file path inside the current WP uploads directory.
 *
 * @param string $original_name Original filename (without path).
 * @param string $suffix        Descriptive suffix (e.g. 'compressed-75').
 * @param string $ext           File extension without dot (e.g. 'jpg').
 * @return array { 'path' => absolute path, 'url' => public URL }
 */
function mici_get_unique_upload_path( $original_name, $suffix, $ext ) {
	$uploads  = wp_upload_dir();
	$base     = pathinfo( $original_name, PATHINFO_FILENAME );
	$filename = sanitize_file_name( $base . '-' . $suffix . '.' . $ext );
	$filepath = trailingslashit( $uploads['path'] ) . $filename;

	// Ensure unique filename if already exists.
	$counter = 1;
	while ( file_exists( $filepath ) ) {
		$filename = sanitize_file_name( $base . '-' . $suffix . '-' . $counter . '.' . $ext );
		$filepath = trailingslashit( $uploads['path'] ) . $filename;
		$counter++;
	}

	return array(
		'path' => $filepath,
		'url'  => trailingslashit( $uploads['url'] ) . $filename,
	);
}

/**
 * Register a processed file as a WordPress media attachment.
 *
 * @param string $file_path      Absolute path to the processed file.
 * @param int    $parent_post_id Parent post ID (Design CPT).
 * @param string $title          Attachment title.
 * @return int|WP_Error New attachment ID or error.
 */
function mici_save_processed_image( $file_path, $parent_post_id, $title ) {
	$filetype = wp_check_filetype( basename( $file_path ), null );

	$upload_dir = wp_upload_dir();
	$file_url   = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $file_path );

	$attachment_data = array(
		'guid'           => $file_url,
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment_data, $file_path, $parent_post_id );

	if ( ! is_wp_error( $attach_id ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $attach_id, $file_path );
		wp_update_attachment_metadata( $attach_id, $metadata );
	}

	return $attach_id;
}

/**
 * Validate that an attachment exists and matches allowed MIME types.
 *
 * @param int   $attachment_id Attachment ID.
 * @param array $allowed_mimes Allowed MIME types (e.g. ['image/jpeg', 'image/png']).
 * @return bool|string True if valid, error message string if not.
 */
function mici_validate_attachment( $attachment_id, $allowed_mimes ) {
	$file = get_attached_file( $attachment_id );
	if ( ! $file || ! file_exists( $file ) ) {
		return __( 'Attachment file not found.', 'mici-ads' );
	}

	$mime = get_post_mime_type( $attachment_id );
	if ( ! in_array( $mime, $allowed_mimes, true ) ) {
		return sprintf(
			/* translators: %s: comma-separated MIME types */
			__( 'Invalid file type. Allowed: %s', 'mici-ads' ),
			implode( ', ', $allowed_mimes )
		);
	}

	return true;
}

/**
 * Append attachment IDs to a Design's gallery meta, respecting the 30-image limit.
 *
 * @param int   $post_id Design post ID.
 * @param array $new_ids Array of attachment IDs to add.
 * @return int Number of IDs actually added.
 */
function mici_add_to_design_gallery( $post_id, $new_ids ) {
	$existing = get_post_meta( $post_id, '_design_gallery', true );
	$current  = $existing ? array_filter( explode( ',', $existing ) ) : array();
	$added    = 0;

	foreach ( $new_ids as $id ) {
		$id = (string) absint( $id );
		if ( count( $current ) >= 30 || in_array( $id, $current, true ) ) {
			continue;
		}
		$current[] = $id;
		$added++;
	}

	update_post_meta( $post_id, '_design_gallery', implode( ',', $current ) );
	return $added;
}
