<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

if ( $product->is_on_sale() ) {

	if ( $product->is_type( 'simple' ) ) {
		$sale_flash = _wc_sale_flash_percent( $product );
		echo apply_filters( 'woocommerce_sale_flash', '<div class="saleoff onsale"><span>-' . $sale_flash . '%</span></div>', $post, $product );
	} else {
		echo apply_filters( 'woocommerce_sale_flash', '<div class="saleoff onsale"><span>' . esc_html__( 'Sale!', 'woocommerce' ) . '</span></div>', $post, $product );
	}
}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
