<?php
/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

?>
    </div><!-- #site-content -->
    <?php

    /**
     * Before Footer
     */
    do_action( 'hd_before_footer' );

    ?>
    <footer class="site-footer" <?php echo \Cores\Helper::microdata( 'footer' ); ?>>
        <?php

        /**
         * Footer
         *
         * @see __hd_construct_footer_widgets - 5
         * @see __hd_construct_footer_credit - 10
         */
        do_action( 'hd_footer' );

        ?>
    </footer><!-- .site-footer -->
    <?php

    /**
     * After Footer
     */
    do_action( 'hd_after_footer' );

    /**
     * Footer
     *
     * @see __wp_footer - 98
     */
    wp_footer();

    ?>
</body>
</html>
