<?php
/**
 * Mici Ads Theme — AJAX: PDF to JPEG
 *
 * Converts each page of a PDF attachment to individual JPEG images
 * using Imagick + Ghostscript. Requires the Imagick PHP extension.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler: convert PDF pages to JPEG images.
 *
 * POST params: attachment_id, dpi (72-300), quality (10-100), post_id.
 */
function mici_ajax_pdf_to_jpeg() {
	check_ajax_referer( 'mici_image_processing' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	if ( ! mici_has_imagick() ) {
		wp_send_json_error( __( 'PDF processing requires Imagick extension.', 'mici-ads' ) );
	}

	$attachment_id = absint( $_POST['attachment_id'] ?? 0 );
	$dpi           = max( 72, min( 300, absint( $_POST['dpi'] ?? 150 ) ) );
	$quality       = max( 10, min( 100, absint( $_POST['quality'] ?? 85 ) ) );
	$post_id       = absint( $_POST['post_id'] ?? 0 );

	if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	$valid = mici_validate_attachment( $attachment_id, array( 'application/pdf' ) );
	if ( true !== $valid ) {
		wp_send_json_error( $valid );
	}

	$pdf_path      = get_attached_file( $attachment_id );
	$original_name = pathinfo( basename( $pdf_path ), PATHINFO_FILENAME );
	$max_pages     = 20;

	try {
		$im = new Imagick();
		$im->setResolution( $dpi, $dpi );
		$im->readImage( $pdf_path );
		$page_count = $im->getNumberImages();
		$im->clear();
		$im->destroy();
	} catch ( Exception $e ) {
		wp_send_json_error( __( 'Failed to read PDF: ', 'mici-ads' ) . $e->getMessage() );
	}

	$page_count = min( $page_count, $max_pages );
	$pages      = array();

	for ( $i = 0; $i < $page_count; $i++ ) {
		try {
			$im = new Imagick();
			$im->setResolution( $dpi, $dpi );
			$im->readImage( $pdf_path . '[' . $i . ']' );
			$im->setImageFormat( 'jpeg' );
			$im->setImageCompressionQuality( $quality );

			// Flatten transparency to white background.
			$im->setImageBackgroundColor( 'white' );
			$im = $im->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );

			$suffix = 'page-' . ( $i + 1 );
			$dest   = mici_get_unique_upload_path( $original_name, $suffix, 'jpg' );
			$im->writeImage( $dest['path'] );
			$im->clear();
			$im->destroy();

			$title  = $original_name . ' — Page ' . ( $i + 1 );
			$attach = mici_save_processed_image( $dest['path'], $post_id, $title );

			if ( ! is_wp_error( $attach ) ) {
				$pages[] = array(
					'attachment_id' => $attach,
					'url'           => $dest['url'],
					'page_num'      => $i + 1,
				);
			}
		} catch ( Exception $e ) {
			// Skip failed pages, continue with remaining.
			continue;
		}
	}

	if ( empty( $pages ) ) {
		wp_send_json_error( __( 'No pages could be converted.', 'mici-ads' ) );
	}

	wp_send_json_success( array( 'pages' => $pages ) );
}
add_action( 'wp_ajax_mici_pdf_to_jpeg', 'mici_ajax_pdf_to_jpeg' );
