<?php
/**
 * The template for displaying the header
 * This is the template that displays all the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />

	<?php

	/**
	 * @see __wp_head - 1
     * @see __external_fonts - 10
	 */
    wp_head();
    ?>

</head>
<body <?php body_class(); ?> <?php echo Helper::microdata( 'body' ); ?>>
    <?php

    /**
     * @see \Themes\Optimizer::body_scripts_top__hook - 99
     */
    do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

    /**
     * @see hd_skip_to_content_link - 2
     * @see hd_off_canvas_menu - 10
     */
    do_action( 'hd_before_header' );

    ?>
    <div class="site-outer">
        <?php

        /**
         * @see hd_construct_header - 10
         */
        do_action( 'hd_header' );

        do_action( 'hd_after_header' );

        ?>
        <div class="site-page">
	        <?php

	        do_action( 'hd_inside_site_page' );

	        ?>
            <div class="site-content" id="site-content">
            <?php

            do_action( 'hd_inside_site_content' );
