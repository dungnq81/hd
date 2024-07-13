<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post-type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion, WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs, the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'shop' );

// template-parts/parts/archive-title.php
the_archive_title_theme();

/**
 * Hook: woocommerce_before_main_content.
 *
 * @see woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @see woocommerce_breadcrumb - 20 (removed by Theme)
 * @see WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>
<section class="section archive archive-product">
	<div class="container">
        <?php
        $page_title = woocommerce_page_title( false );
        if ( is_shop() ) {
	        $shop_page_id = wc_get_page_id( 'shop' );
	        $page_title = Helper::get_field( 'alternative_title', $shop_page_id ) ?: $page_title;
        }
        ?>
        <h1 class="heading-title"><?= $page_title ?></h1>

		<?php
		/**
		 * Hook: woocommerce_archive_description.
		 *
		 * @since 1.6.2.
		 * @see woocommerce_taxonomy_archive_description - 10
		 * @see woocommerce_product_archive_description - 10
		 */
		do_action( 'woocommerce_archive_description' );

		?>
        <div class="grid-products">
            <?php

            $is_sidebar = false;
            if ( is_active_sidebar( 'product-archive-sidebar' ) && ! is_search() ) {
	            $is_sidebar = true;

	            echo '<div class="sidebar-col">';
	            dynamic_sidebar( 'product-archive-sidebar' );
	            echo '</div>';

	            echo '<div class="content-col">';
            }

            // loop check...
            if ( woocommerce_product_loop() ) {

	            /**
	             * Hook: woocommerce_before_shop_loop.
	             *
	             * @see woocommerce_output_all_notices - 10
	             * @see woocommerce_result_count - 20
	             * @see woocommerce_catalog_ordering - 30
	             */
	            do_action( 'woocommerce_before_shop_loop' );

	            woocommerce_product_loop_start();

	            if ( wc_get_loop_prop( 'total' ) ) {
		            while ( have_posts() ) { // Start the Loop.
                        the_post();

			            /**
			             * Hook: woocommerce_shop_loop.
			             */
			            do_action( 'woocommerce_shop_loop' );

			            wc_get_template_part( 'content', 'product' );

		            } // End the loop.
		            wp_reset_postdata();
	            }

	            woocommerce_product_loop_end();

	            /**
	             * Hook: woocommerce_after_shop_loop.
	             *
	             * @see woocommerce_pagination - 10
	             */
	            do_action( 'woocommerce_after_shop_loop' );
            } else {
	            /**
	             * Hook: woocommerce_no_products_found.
	             *
	             * @see wc_no_products_found - 10
	             */
	            do_action( 'woocommerce_no_products_found' );
            }

            if ( $is_sidebar ) { echo '</div>';  }

            ?>
        </div>
	</div>
</section>
<?php

/**
 * Hook: woocommerce_after_main_content.
 *
 * @see woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

// footer
get_footer( 'page' );
