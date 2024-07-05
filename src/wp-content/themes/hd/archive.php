<?php
/**
 * The template for displaying Archive pages.
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

// header
get_header( 'archive' );

/**
 * Hook: archive_before_section.
 *
 * @see __hd_archive_title - 10
 */
do_action( 'archive_before_section' );

?>
<section class="section archive">
    <div class="container">
        <?php

        /**
         * Hook: archive_content
         *
         * @see __hd_archive_header - 10
         * @see __hd_archive_content - 11
         */
        do_action( 'archive_content' );

        ?>
    </div>
</section>
<?php

/**
 * Hook: archive_after_section.
 */
do_action( 'archive_after_section' );

// footer
get_footer( 'archive' );
