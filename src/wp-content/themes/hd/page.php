<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package eHD
 * @since 1.0.0
 */

\defined( 'ABSPATH' ) || die;

get_header();

the_content();

// homepage widget
if ( is_active_sidebar( 'hd-home-sidebar' ) ) :
	dynamic_sidebar( 'hd-home-sidebar' );
endif;

?>
    <div class="layout-demo flex-container">
        <div class="!flex flex-x gap">
            <div class="cell cell-1 m-4 t-3 d-2" style="background-color: #0a4b78;">cell-1</div>
            <div class="cell cell-2 m-4 t-3 d-2" style="background-color: #0c6ca0;">cell-2</div>
            <div class="cell cell-3 m-4 t-3 d-2" style="background-color: #00a32a;">cell-3</div>
            <div class="cell cell-4 m-4 t-3 d-2" style="background-color: #0c88b4;">cell-4</div>
            <div class="cell cell-5" style="background-color: #6f42c1;">cell-5</div>
            <div class="cell cell-6" style="background-color: #8a6d3b;">cell-6</div>
            <div class="cell cell-7" style="background-color: #f4a224;">cell-7</div>
            <div class="cell cell-8" style="background-color: darkred;">cell-8</div>
            <div class="cell cell-9" style="background-color: #ff2222;">cell-9</div>
            <div class="cell cell-10" style="background-color: mediumblue;">cell-10</div>
            <div class="cell cell-11" style="background-color: purple;">cell-11</div>
            <div class="cell cell-12" style="background-color: yellowgreen;">cell-12</div>
        </div>
    </div>
<?php

get_footer();
