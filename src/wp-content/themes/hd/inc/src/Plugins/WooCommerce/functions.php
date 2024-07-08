<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------------------

if ( ! function_exists( '_wc_cart_link' ) ) {

	/**
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 */
	function _wc_cart_link(): void {
		if ( ! _wc_cart_available() ) {
			return;
		}
		?>
        <a class="header-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php echo esc_attr__( 'View your shopping cart', TEXT_DOMAIN ); ?>">
				<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?>
            <span class="icon" data-glyph=""></span>
	            <span class="count"><?php echo wp_kses_data( sprintf( '%d', WC()->cart->get_cart_contents_count() ) ); ?></span>
	            <span class="txt"><?php echo __( 'Shopping cart', TEXT_DOMAIN ) ?></span>
	        </a>
		<?php
	}
}

// ------------------------------------------------------

if ( ! function_exists( '_wc_cart_available' ) ) {

	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @return bool
	 */
	function _wc_cart_available(): bool {
		$woo = \WC();

		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}
}

// ------------------------------------------------------

if ( ! function_exists( '_wc_get_gallery_image_html' ) ) {

	/**
	 * @param      $attachment_id
	 * @param bool $main_image
	 * @param bool $cover
	 * @param bool $lightbox
	 *
	 * @return string
	 */
	function _wc_get_gallery_image_html( $attachment_id, bool $main_image = false, bool $cover = false, bool $lightbox = false ): string {
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', [
			$gallery_thumbnail['width'],
			$gallery_thumbnail['height']
		] );

		$image_size    = apply_filters( 'woocommerce_gallery_image_size', $main_image ? 'woocommerce_single' : $thumbnail_size );
		$full_size     = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src      = wp_get_attachment_image_src( $attachment_id, $full_size );
		$alt_text      = Helper::esc_attr_strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

		$image = wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			apply_filters(
				'woocommerce_gallery_image_html_attachment_image_params',
				[
					'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
					'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
					'data-src'                => esc_url( $full_src[0] ),
					'data-large_image'        => esc_url( $full_src[0] ),
					'data-large_image_width'  => esc_attr( $full_src[1] ),
					'data-large_image_height' => esc_attr( $full_src[2] ),
					'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
				],
				$attachment_id,
				$image_size,
				$main_image
			)
		);

		$ratio_class = Helper::aspectRatioClass( 'product' );
		$auto        = $cover ? ' ' : ' auto ';

		if ( $lightbox ) {
			$popup_image = '<span data-rel="lightbox" class="image-popup" data-src="' . esc_url( $full_src[0] ) . '" data-glyph=""></span>';

			return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="wpg__image cover"><a class="res' . $auto . $ratio_class . '" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>' . $popup_image . '</div>';
		}

		return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image wpg__thumb cover"><a class="res' . $auto . $ratio_class . '" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
	}
}

// ------------------------------------------------------

if ( ! function_exists( '_wc_sale_flash_percent' ) ) {

	/**
	 * @param $product
	 *
	 * @return float|string
	 */
	function _wc_sale_flash_percent( $product ): float|string {
		global $product;
		$percent_off = '';

		if ( $product->is_on_sale() ) {

			if ( $product->is_type( 'variable' ) ) {
				$percent_off = ceil( 100 - ( $product->get_variation_sale_price() / $product->get_variation_regular_price( 'min' ) ) * 100 );
			} elseif ( $product->get_regular_price() && ! $product->is_type( 'grouped' ) ) {
				$percent_off = ceil( 100 - ( $product->get_sale_price() / $product->get_regular_price() ) * 100 );
			}
		}

		return $percent_off;
	}
}
