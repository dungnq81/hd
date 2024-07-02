<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion, WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs, the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

// header
get_header( 'shop' );

// template-parts/parts/page-title.php
the_page_title_theme();

/**
 * woocommerce_before_main_content hook.
 *
 * @see woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @see woocommerce_breadcrumb - 20 - Removed by Theme
 */
do_action( 'woocommerce_before_main_content' );

?>
<section class="section singular product">
	<?php

	/* Start the Loop */
	while ( have_posts() ) :
        the_post();

		wc_get_template_part( 'content', 'single-product' );

		// end of the loop.
	endwhile;
	wp_reset_postdata();

	?>
</section>
<?php

/**
 * woocommerce_after_main_content hook.
 *
 * @see woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

// footer
get_footer( 'page' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
