<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------------------



// ------------------------------------------------------

/**
 * Add default product tabs to product pages.
 *
 * @param array $tabs Array of tabs.
 *
 * @return array
 */
function woocommerce_default_product_tabs( $tabs = [] ): array {
	global $product, $post;

	// Description tab - shows product content.
	if ( $post->post_content ) {
		$tabs['description'] = array(
			'title'    => __( 'Description', 'woocommerce' ),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab',
		);
	}

	// Additional information tab - shows attributes.
	if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
		$tabs['additional_information'] = array(
			'title'    => __( 'Additional information', 'woocommerce' ),
			'priority' => 20,
			'callback' => 'woocommerce_product_additional_information_tab',
		);
	}

	// Reviews tab - shows comments.
	if ( comments_open() ) {
		$tabs['reviews'] = array(
			/* translators: %s: reviews count */
			'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
			'priority' => 30,
			'callback' => 'comments_template',
		);
	}

	return $tabs;
}

// ------------------------------------------------------

if ( ! function_exists( '__woocommerce_product_get_rating_html' ) ) {
	add_filter( 'woocommerce_product_get_rating_html', '__woocommerce_product_get_rating_html', 10, 3 );

	/**
	 * @param $html
	 * @param $rating
	 * @param $count
	 *
	 * @return string
	 */
	function __woocommerce_product_get_rating_html( $html, $rating, $count ): string {
		$return = '';

		if ( 0 < $rating ) {
			$label = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );

			$return .= '<div class="loop-stars-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
			$return .= wc_get_star_rating_html( $rating, $count );
			$return .= '</div>';
		}

		return $return;
	}
}

// ------------------------------------------------------

/**
 * Get the product thumbnail, or the placeholder if not set.
 *
 * @param string $size (default: 'woocommerce_thumbnail').
 * @param array $attr Image attributes.
 * @param bool $placeholder True to return $placeholder if no image is found, or false to return an empty string.
 *
 * @return string
 */
function woocommerce_get_product_thumbnail( $size = 'medium', $attr = [], $placeholder = true ): string {
	global $product;

	if ( ! is_array( $attr ) ) {
		$attr = [];
	}

	if ( ! is_bool( $placeholder ) ) {
		$placeholder = true;
	}

	$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

	$scale_class = 'scale';
	$ratio_class = Helper::aspectRatioClass( 'product' );

	return $product ? '<div class="cover thumbnails"><span class="' . $scale_class . ' after-overlay res ' . $ratio_class . '">' . $product->get_image( $image_size, $attr, $placeholder ) . '</span></div>' : '';
}

// ------------------------------------------------------

/**
 * Show the product title in the product loop. By default, this is an H2.
 */
function woocommerce_template_loop_product_title(): void {
	echo '<h3 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
