<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$object = get_queried_object();

// breadcrumb bg default
$breadcrumb_class = '';
$breadcrumb_bg = Helper::getThemeMod( 'breadcrumb_bg_setting' );
if ( $breadcrumb_bg ) {
	$breadcrumb_class = ' has-background';
}

// breadcrumb of page
$image_for_banner = \get_field( 'image_for_banner', $object->ID ) ?? false;
if ( $image_for_banner ) {
	$breadcrumb_class = ' has-background';
	$breadcrumb_bg = $image_for_banner;
}

// title
$title = \get_field( 'alternative_title', $object->ID ) ?: $object->post_title;

if ( is_search() ) {
	$title = sprintf( __( 'Search Results for: %s', TEXT_DOMAIN ), get_search_query() );
}

?>
<section class="section section-title<?= $breadcrumb_class ?>">

    <?php if ( $breadcrumb_bg ) { echo '<span class="cover breadcrumb-bg">' . wp_get_attachment_image( $breadcrumb_bg, 'widescreen' ) . '</span>'; } ?>

    <div class="container">
        <p class="breadcrumb-title"><?php echo $title; ?></p>

	    <?php
	    if ( method_exists( Helper::class, 'breadcrumbs' ) ) :
		    Helper::breadcrumbs();
        elseif ( function_exists( 'woocommerce_breadcrumb' ) ) :
		    woocommerce_breadcrumb();
        elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) :
		    rank_math_the_breadcrumbs();
	    endif;
	    ?>

    </div>
</section>