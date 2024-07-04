<?php
/**
 * The template for displaying the header
 * This is the template that displays all the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php

	/**
     * Head
     *
	 * @see __wp_head - 1
     * @see __external_fonts - 10
	 */
    wp_head();

    ?>
</head>
<body <?php body_class(); ?> <?php echo \Cores\Helper::microdata( 'body' ); ?>>
    <?php

    /**
     * @see \Themes\Optimizer::body_scripts_top__hook - 99
     */
    do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

    /**
     * Before Header
     *
     * @see __hd_skip_to_content_link - 2
     * @see __hd_off_canvas_menu - 10
     */
    do_action( 'hd_before_header' );

    ?>
    <header id="masthead" class="site-header" <?php echo \Cores\Helper::microdata( 'header' ); ?>>
        <?php

        /**
         * Header
         *
         * @see __hd_construct_header - 10
         */
        do_action( 'hd_header' );

        ?>
    </header><!-- #masthead -->
    <?php

    /**
     * After Header
     */
    do_action( 'hd_after_header' );

    ?>
    <div class="site-content" id="site-content">
        <?php

        /**
         * Inside Site Content
         */
        do_action( 'hd_inside_site_content' );
