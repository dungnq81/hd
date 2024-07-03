<?php
/**
 * The template for displaying Home (Blog) pages.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'home' );

/**
 * Hook: home_before_section.
 *
 * @see __hd_page_title - 10
 */
do_action( 'home_before_section' );

?>
<section class="section archive blog-page">
    <div class="container">
        <?php

        /**
         * Hook: Home page content
         *
         * @see __hd_home_page_header - 10
         * @see __hd_home_page_content - 11
         */
        do_action( 'home_page_content' );

        ?>
    </div>
</section>
<?php

/**
 * Hook: home_after_section.
 */
do_action( 'home_after_section' );

// footer
get_footer( 'home' );
