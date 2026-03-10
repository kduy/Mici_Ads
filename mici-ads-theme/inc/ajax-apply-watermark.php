<?php
/**
 * Mici Ads Theme — AJAX: Apply Watermark
 *
 * Composites a watermark image onto a source image with configurable
 * opacity and position (center, bottom-right, tiled).
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler: apply watermark to an image attachment.
 *
 * POST params: attachment_id, watermark_id (0=default logo), opacity (5-100),
 *              position (center|bottom-right|tiled), post_id.
 */
function mici_ajax_apply_watermark() {
	check_ajax_referer( 'mici_image_processing' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	$attachment_id = absint( $_POST['attachment_id'] ?? 0 );
	$watermark_id  = absint( $_POST['watermark_id'] ?? 0 );
	$opacity       = max( 5, min( 100, absint( $_POST['opacity'] ?? 30 ) ) );
	$position      = sanitize_text_field( $_POST['position'] ?? 'center' );
	$post_id       = absint( $_POST['post_id'] ?? 0 );

	if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( __( 'Permission denied.', 'mici-ads' ) );
	}

	if ( ! in_array( $position, array( 'center', 'bottom-right', 'tiled' ), true ) ) {
		$position = 'center';
	}

	$allowed = array( 'image/jpeg', 'image/png', 'image/webp' );
	$valid   = mici_validate_attachment( $attachment_id, $allowed );
	if ( true !== $valid ) {
		wp_send_json_error( $valid );
	}

	$source_path = get_attached_file( $attachment_id );
	$original_name = basename( $source_path );

	// Load watermark image.
	if ( $watermark_id > 0 ) {
		$wm_valid = mici_validate_attachment( $watermark_id, array( 'image/png', 'image/jpeg', 'image/webp' ) );
		if ( true !== $wm_valid ) {
			wp_send_json_error( __( 'Invalid watermark image.', 'mici-ads' ) );
		}
		$wm_path = get_attached_file( $watermark_id );
	} else {
		$wm_path = MICI_THEME_DIR . '/images/watermark-mici-ads.png';
	}

	if ( ! file_exists( $wm_path ) ) {
		wp_send_json_error( __( 'Watermark file not found.', 'mici-ads' ) );
	}

	// Determine output format from source.
	$source_ext = strtolower( pathinfo( $source_path, PATHINFO_EXTENSION ) );
	$out_ext    = in_array( $source_ext, array( 'jpg', 'jpeg', 'png', 'webp' ), true ) ? $source_ext : 'jpg';
	if ( 'jpeg' === $out_ext ) {
		$out_ext = 'jpg';
	}

	$dest = mici_get_unique_upload_path( $original_name, 'watermarked', $out_ext );

	$success = false;

	if ( mici_has_imagick() ) {
		$success = mici_watermark_imagick( $source_path, $wm_path, $dest['path'], $opacity, $position );
	}

	if ( ! $success ) {
		$success = mici_watermark_gd( $source_path, $wm_path, $dest['path'], $opacity, $position );
	}

	if ( ! $success || ! file_exists( $dest['path'] ) ) {
		wp_send_json_error( __( 'Watermark processing failed.', 'mici-ads' ) );
	}

	$title  = pathinfo( $original_name, PATHINFO_FILENAME ) . ' (watermarked)';
	$attach = mici_save_processed_image( $dest['path'], $post_id, $title );

	if ( is_wp_error( $attach ) ) {
		wp_send_json_error( $attach->get_error_message() );
	}

	wp_send_json_success( array(
		'attachment_id' => $attach,
		'url'           => $dest['url'],
	) );
}
add_action( 'wp_ajax_mici_apply_watermark', 'mici_ajax_apply_watermark' );

/**
 * Apply watermark using Imagick.
 *
 * @return bool Success.
 */
function mici_watermark_imagick( $src, $wm_path, $dest, $opacity, $position ) {
	try {
		$img = new Imagick( $src );
		$wm  = new Imagick( $wm_path );

		// Scale watermark to 30% of source width.
		$target_w = (int) ( $img->getImageWidth() * 0.3 );
		$wm->scaleImage( $target_w, 0 );

		// Ensure alpha channel exists, then apply opacity.
		if ( ! $wm->getImageAlphaChannel() ) {
			$wm->setImageAlphaChannel( Imagick::ALPHACHANNEL_SET );
		}
		$wm->evaluateImage( Imagick::EVALUATE_MULTIPLY, $opacity / 100, Imagick::CHANNEL_ALPHA );

		$img_w = $img->getImageWidth();
		$img_h = $img->getImageHeight();
		$wm_w  = $wm->getImageWidth();
		$wm_h  = $wm->getImageHeight();

		if ( 'tiled' === $position ) {
			for ( $y = 0; $y < $img_h; $y += $wm_h + 40 ) {
				for ( $x = 0; $x < $img_w; $x += $wm_w + 40 ) {
					$img->compositeImage( $wm, Imagick::COMPOSITE_OVER, $x, $y );
				}
			}
		} else {
			if ( 'bottom-right' === $position ) {
				$x = $img_w - $wm_w - 20;
				$y = $img_h - $wm_h - 20;
			} else {
				// Center.
				$x = (int) ( ( $img_w - $wm_w ) / 2 );
				$y = (int) ( ( $img_h - $wm_h ) / 2 );
			}
			$img->compositeImage( $wm, Imagick::COMPOSITE_OVER, $x, $y );
		}

		$img->writeImage( $dest );
		$wm->clear();
		$wm->destroy();
		$img->clear();
		$img->destroy();

		return true;
	} catch ( Exception $e ) {
		return false;
	}
}

/**
 * Apply watermark using GD (fallback).
 *
 * @return bool Success.
 */
function mici_watermark_gd( $src, $wm_path, $dest, $opacity, $position ) {
	$src_img = mici_gd_load( $src );
	$wm_img  = mici_gd_load( $wm_path );
	if ( ! $src_img || ! $wm_img ) {
		return false;
	}

	$img_w = imagesx( $src_img );
	$img_h = imagesy( $src_img );
	$wm_ow = imagesx( $wm_img );
	$wm_oh = imagesy( $wm_img );

	// Scale watermark to 30% of source width.
	$target_w = (int) ( $img_w * 0.3 );
	$target_h = (int) ( $wm_oh * ( $target_w / max( $wm_ow, 1 ) ) );
	$wm_scaled = imagecreatetruecolor( $target_w, $target_h );
	imagealphablending( $wm_scaled, false );
	imagesavealpha( $wm_scaled, true );
	imagecopyresampled( $wm_scaled, $wm_img, 0, 0, 0, 0, $target_w, $target_h, $wm_ow, $wm_oh );
	imagedestroy( $wm_img );

	$wm_w = $target_w;
	$wm_h = $target_h;

	if ( 'tiled' === $position ) {
		for ( $y = 0; $y < $img_h; $y += $wm_h + 40 ) {
			for ( $x = 0; $x < $img_w; $x += $wm_w + 40 ) {
				imagecopymerge( $src_img, $wm_scaled, $x, $y, 0, 0, $wm_w, $wm_h, $opacity );
			}
		}
	} else {
		if ( 'bottom-right' === $position ) {
			$x = $img_w - $wm_w - 20;
			$y = $img_h - $wm_h - 20;
		} else {
			$x = (int) ( ( $img_w - $wm_w ) / 2 );
			$y = (int) ( ( $img_h - $wm_h ) / 2 );
		}
		imagecopymerge( $src_img, $wm_scaled, $x, $y, 0, 0, $wm_w, $wm_h, $opacity );
	}

	$ext = strtolower( pathinfo( $dest, PATHINFO_EXTENSION ) );
	if ( 'png' === $ext ) {
		$result = imagepng( $src_img, $dest );
	} elseif ( 'webp' === $ext && function_exists( 'imagewebp' ) ) {
		$result = imagewebp( $src_img, $dest );
	} else {
		$result = imagejpeg( $src_img, $dest, 90 );
	}

	imagedestroy( $src_img );
	imagedestroy( $wm_scaled );

	return $result;
}

/**
 * Load an image file into a GD resource.
 *
 * @param string $path Image file path.
 * @return resource|GdImage|false
 */
function mici_gd_load( $path ) {
	$info = getimagesize( $path );
	if ( ! $info ) {
		return false;
	}
	switch ( $info[2] ) {
		case IMAGETYPE_JPEG:
			return imagecreatefromjpeg( $path );
		case IMAGETYPE_PNG:
			return imagecreatefrompng( $path );
		case IMAGETYPE_WEBP:
			return function_exists( 'imagecreatefromwebp' ) ? imagecreatefromwebp( $path ) : false;
		default:
			return false;
	}
}
