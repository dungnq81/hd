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
 * @see __hd_home_title - 10
 */
do_action( 'home_before_section' );

?>
<section class="section archive blog-page">
    <div class="container">
        <?php

        /**
         * Hook: Home content
         *
         * @see __hd_home_header - 10
         * @see __hd_home_content - 11
         */
        do_action( 'home_content' );

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
