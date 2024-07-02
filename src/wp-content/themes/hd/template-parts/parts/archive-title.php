<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$object = get_queried_object();

// breadcrumb bg default
$breadcrumb_class = '';
$breadcrumb_bg    = Helper::getThemeMod( 'breadcrumb_bg_setting' );
if ( $breadcrumb_bg ) {
	$breadcrumb_class = ' has-background';
}

// breadcrumb of page
$image_for_banner = \get_field( 'image_for_banner', $object ) ?? false;

// title
$archive_title = '';
if ( Helper::is_woocommerce_active() && is_shop() ) {
	$shop_page_id     = wc_get_page_id( 'shop' );
	$archive_title    = \get_field( 'alternative_title', $shop_page_id ) ?: get_the_title( $shop_page_id );
	$image_for_banner = \get_field( 'image_for_banner', $shop_page_id ) ?? false;
}

if ( $image_for_banner ) {
	$breadcrumb_class = ' has-background';
	$breadcrumb_bg    = $image_for_banner;
}

if ( is_search() ) {
	$archive_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', TEXT_DOMAIN ), get_search_query() );
	if ( get_query_var( 'paged' ) ) {
		$archive_title .= sprintf( __( '&nbsp;&ndash; page %s', TEXT_DOMAIN ), get_query_var( 'paged' ) );
	}
}

$archive_title = ! empty( $archive_title ) ? $archive_title : get_the_archive_title();

?>
<section class="section section-title<?= $breadcrumb_class ?>">

    <?php if ( $breadcrumb_bg ) { echo '<span class="cover breadcrumb-bg">' . wp_get_attachment_image( $breadcrumb_bg, 'widescreen' ) . '</span>'; } ?>

    <div class="container">
        <p class="breadcrumb-title"><?php echo $archive_title; ?></p>

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
