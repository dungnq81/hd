<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( '_wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns = apply_filters('woocommerce_product_thumbnails_columns', 6);
$post_thumbnail_id = $product->get_image_id();
$attachment_ids = $product->get_gallery_image_ids();

$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
		'swiper-product-gallery',
	)
);

$outstanding_features = \get_field('outstanding_features',$product->ID) ?? '';

?>
<div class="woocommerce-product-gallery-wrapper">
    <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
        <div class="woocommerce-product-gallery__wrapper wpg__images">
            <div class="swiper swiper-images">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-images-first">
		                <?php
		                if ( $product->get_image_id() ) :
			                $html = _wc_get_gallery_image_html( $post_thumbnail_id, true, true, true );
		                else :
			                $html = '<div class="woocommerce-product-gallery__image--placeholder">';
			                $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			                $html .= '</div>';
		                endif;

		                echo apply_filters( 'woocommerce_single_product_image_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		                ?>
                    </div>
	                <?php
	                if ( $attachment_ids && $product->get_image_id() ) {
		                foreach ( $attachment_ids as $attachment_id ) {
			                echo '<div class="swiper-slide">';
			                echo _wc_get_gallery_image_html( $attachment_id, true, true, true );
			                echo '</div>';
		                }
	                }
	                ?>
                </div>
                <div class="swiper-controls">
                    <div class="swiper-button swiper-button-prev" data-glyph=""></div>
                    <div class="swiper-button swiper-button-next" data-glyph=""></div>
                </div>
            </div>
        </div>
	    <?php if ( $attachment_ids ) : ?>
        <div class="woocommerce-product-gallery__wrapper wpg__thumbs">
            <div class="swiper swiper-thumbs">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-thumbs-first">
			            <?php
			            if ( $product->get_image_id() ) :
				            $html = _wc_get_gallery_image_html( $post_thumbnail_id, false, true );
			            else :
				            $html = '<div class="woocommerce-product-gallery__image--placeholder">';
				            $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'wpa-gallery' ) );
				            $html .= '</div>';
			            endif;

			            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_ids );
			            ?>
                    </div>

		            <?php do_action( 'woocommerce_product_thumbnails' ); ?>

                </div>
                <div class="swiper-controls">
                    <div class="swiper-button swiper-button-prev" data-glyph=""></div>
                    <div class="swiper-button swiper-button-next" data-glyph=""></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

	<?php if ( $outstanding_features ) : ?>
    <div class="product-outstanding-features">
        <div class="title"><?php echo __( 'Outstanding Features', TEXT_DOMAIN );?></div>
        <div class="inner">
            <?php echo $outstanding_features ?>
        </div>
    </div>
	<?php endif; ?>

</div>
