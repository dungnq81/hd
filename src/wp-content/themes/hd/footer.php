<?php
/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 *
 * @package HD
 */

\defined( 'ABSPATH' ) || die;

?>
            </div><!-- .site-content -->
        </div><!-- .site-page -->
        <?php

        do_action( 'hd_before_footer' );

        ?>
        <div class="site-footer">
            <?php

            do_action( 'hd_before_footer_content' );

            /**
             * @see hd_construct_footer_widgets - 5
             * @see hd_construct_footer - 10
             */
            do_action( 'hd_footer' );

            do_action( 'hd_after_footer_content' );

            ?>
        </div>
        <?php

        do_action( 'hd_after_footer' );

        ?>
    </div><!-- .site-outer -->

    <?php

    /**
     * @see __wp_footer - 98
     */
    wp_footer();

    ?>

</body>
</html>
