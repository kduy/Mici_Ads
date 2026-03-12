<?php
/**
 * Pods Compatibility — get_field() wrapper
 *
 * Provides an ACF-compatible get_field() function backed by Pods / post meta.
 * Templates stay unchanged; only the data source changes.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// Only define if ACF is NOT active (avoid conflicts).
if ( ! function_exists( 'get_field' ) ) :

	/**
	 * Map of "group" field names to their sub-field keys.
	 * get_field('hero_item_1') → assemble sub-meta values into array.
	 */
	function mici_pods_group_map() {
		static $map = null;
		if ( $map !== null ) {
			return $map;
		}

		$map = array();

		// Hero gallery items (4).
		for ( $i = 1; $i <= 4; $i++ ) {
			$map[ 'hero_item_' . $i ] = array(
				'image'      => 'image',
				'label'      => 'text',
				'sublabel'   => 'text',
				'background' => 'text',
				'text_color' => 'text',
			);
		}

		// Services (4).
		for ( $i = 1; $i <= 4; $i++ ) {
			$map[ 'service_' . $i ] = array(
				'image'       => 'image',
				'title'       => 'text',
				'description' => 'text',
				'tags'        => 'text',
			);
		}

		// Benefits (6).
		for ( $i = 1; $i <= 6; $i++ ) {
			$map[ 'benefit_' . $i ] = array(
				'title'       => 'text',
				'description' => 'text',
			);
		}

		// Testimonials (4).
		for ( $i = 1; $i <= 4; $i++ ) {
			$map[ 'testimonial_' . $i ] = array(
				'quote'          => 'text',
				'name'           => 'text',
				'role'           => 'text',
				'avatar_initial' => 'text',
				'rating'         => 'text',
			);
		}

		// FAQ (8).
		for ( $i = 1; $i <= 8; $i++ ) {
			$map[ 'faq_' . $i ] = array(
				'question' => 'text',
				'answer'   => 'text',
			);
		}

		return $map;
	}

	/**
	 * Convert an attachment ID to an ACF-compatible image array.
	 *
	 * @param int $attachment_id Attachment post ID.
	 * @return array|null
	 */
	function mici_pods_image_array( $attachment_id ) {
		if ( ! $attachment_id ) {
			return null;
		}
		$attachment_id = (int) $attachment_id;
		$url           = wp_get_attachment_url( $attachment_id );
		if ( ! $url ) {
			return null;
		}
		return array(
			'ID'    => $attachment_id,
			'url'   => $url,
			'alt'   => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			'sizes' => array(
				'medium_large' => wp_get_attachment_image_url( $attachment_id, 'medium_large' ) ?: $url,
				'medium'       => wp_get_attachment_image_url( $attachment_id, 'medium' ) ?: $url,
				'thumbnail'    => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) ?: $url,
				'large'        => wp_get_attachment_image_url( $attachment_id, 'large' ) ?: $url,
			),
		);
	}

	/**
	 * ACF-compatible get_field() backed by post meta.
	 *
	 * @param string   $name    Field name.
	 * @param int|null $post_id Post ID (defaults to current post).
	 * @return mixed
	 */
	function get_field( $name, $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		if ( ! $post_id ) {
			return '';
		}

		// Check if this is a "group" field name.
		$groups = mici_pods_group_map();
		if ( isset( $groups[ $name ] ) ) {
			$result   = array();
			$has_data = false;
			foreach ( $groups[ $name ] as $sub_key => $sub_type ) {
				$meta_key = $name . '_' . $sub_key;
				$val      = get_post_meta( $post_id, $meta_key, true );
				if ( 'image' === $sub_type && $val ) {
					$val = mici_pods_image_array( $val );
				}
				$result[ $sub_key ] = $val;
				if ( ! empty( $val ) ) {
					$has_data = true;
				}
			}
			return $has_data ? $result : null;
		}

		// Check if it's a portfolio image field (returns image array).
		if ( preg_match( '/^portfolio_image_\d+$/', $name ) ) {
			$val = get_post_meta( $post_id, $name, true );
			return $val ? mici_pods_image_array( $val ) : null;
		}

		// Default: return raw meta value.
		return get_post_meta( $post_id, $name, true );
	}

endif;
