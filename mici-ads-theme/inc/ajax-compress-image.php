<?php
/**
 * Mici Ads Theme — AJAX: Compress Image
 *
 * Handles image compression via Imagick (GD fallback).
 * Strips EXIF, converts format, adjusts quality.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler: compress an image attachment.
 *
 * POST params: attachment_id, quality (10-100), format (jpeg|png|webp), post_id.
 */
function mici_ajax_compress_image() {
	check_ajax_referer( 'mici_image_processing' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	$attachment_id = absint( $_POST['attachment_id'] ?? 0 );
	$quality       = max( 10, min( 100, absint( $_POST['quality'] ?? 75 ) ) );
	$format        = sanitize_text_field( $_POST['format'] ?? 'jpeg' );
	$post_id       = absint( $_POST['post_id'] ?? 0 );

	if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	if ( ! in_array( $format, array( 'jpeg', 'png', 'webp' ), true ) ) {
		$format = 'jpeg';
	}

	$allowed = array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif' );
	$valid   = mici_validate_attachment( $attachment_id, $allowed );
	if ( true !== $valid ) {
		wp_send_json_error( $valid );
	}

	$source_path   = get_attached_file( $attachment_id );
	$original_size = filesize( $source_path );
	$original_name = basename( $source_path );

	$ext_map = array( 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp' );
	$dest    = mici_get_unique_upload_path( $original_name, 'compressed-' . $quality, $ext_map[ $format ] );

	$success = false;

	// Try Imagick first.
	if ( mici_has_imagick() ) {
		try {
			$im = new Imagick( $source_path );
			$im->stripImage();
			$im->setImageFormat( $format );
			if ( 'png' !== $format ) {
				$im->setImageCompressionQuality( $quality );
			}
			$success = $im->writeImage( $dest['path'] );
			$im->clear();
			$im->destroy();
		} catch ( Exception $e ) {
			$success = false;
		}
	}

	// GD fallback.
	if ( ! $success ) {
		$editor = wp_get_image_editor( $source_path );
		if ( is_wp_error( $editor ) ) {
			wp_send_json_error( $editor->get_error_message() );
		}
		$editor->set_quality( $quality );
		$mime_map = array( 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
		$saved    = $editor->save( $dest['path'], $mime_map[ $format ] );
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error( $saved->get_error_message() );
		}
		$success = true;
	}

	if ( ! $success || ! file_exists( $dest['path'] ) ) {
		wp_send_json_error( __( 'Compression failed.', 'mici-ads' ) );
	}

	$new_size = filesize( $dest['path'] );
	$title    = pathinfo( $original_name, PATHINFO_FILENAME ) . ' (compressed)';
	$attach   = mici_save_processed_image( $dest['path'], $post_id, $title );

	if ( is_wp_error( $attach ) ) {
		wp_send_json_error( $attach->get_error_message() );
	}

	$savings = $original_size > 0 ? round( ( 1 - $new_size / $original_size ) * 100 ) : 0;

	wp_send_json_success( array(
		'attachment_id' => $attach,
		'url'           => $dest['url'],
		'filename'      => basename( $dest['path'] ),
		'original_size' => $original_size,
		'new_size'      => $new_size,
		'savings_pct'   => max( 0, $savings ),
	) );
}
add_action( 'wp_ajax_mici_compress_image', 'mici_ajax_compress_image' );
