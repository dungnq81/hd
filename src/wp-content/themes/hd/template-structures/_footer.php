<?php
/**
 * Footer elements
 *
 * @author HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// wp_footer
// -----------------------------------------------

if ( ! function_exists( '__wp_footer' ) ) {
	add_action( 'wp_footer', '__wp_footer', 98 );

	/**
	 * Build the back to top button
	 *
	 * @return void
	 */
	function __wp_footer(): void {
		if ( apply_filters( 'hd_back_to_top', true ) ) {
			echo apply_filters( // phpcs:ignore
				'hd_back_to_top_output',
				sprintf(
					'<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="back-to-top toTop" data-scroll-speed="%2$s" data-start-scroll="%3$s" data-glyph=""></a>',
					esc_attr__( 'Scroll back to top', TEXT_DOMAIN ),
					absint( apply_filters( 'hd_back_to_top_scroll_speed', 400 ) ),
					absint( apply_filters( 'hd_back_to_top_start_scroll', 300 ) ),
				)
			);
		}
	}
}

// -----------------------------------------------
// hd_footer
// -----------------------------------------------

if ( ! function_exists( '__hd_construct_footer_widgets' ) ) {
	add_action( 'hd_footer', '__hd_construct_footer_widgets', 5 );

	/**
	 * Build our footer widgets
	 *
	 * @return void
	 */
	function __hd_construct_footer_widgets(): void {
		$rows    = (int) Helper::getThemeMod( 'footer_row_setting' );
		$regions = (int) Helper::getThemeMod( 'footer_col_setting' );

		$footer_container = Helper::getThemeMod( 'footer_container_setting' );

		// If no footer widgets exist, we don't need to continue
		if ( 1 > $rows || 1 > $regions ) {
			return;
		}

		?>
        <div id="footer-widgets" class="footer-widgets">
			<?php
			for ( $row = 1; $row <= $rows; $row ++ ) :

				// Defines the number of active columns in this footer row.
				for ( $region = $regions; 0 < $region; $region -- ) {
					if ( is_active_sidebar( 'hd-footer-' . esc_attr( $region + $regions * ( $row - 1 ) ) ) ) {
						$columns = $region;
						break;
					}
				}

				if ( isset( $columns ) ) :
                    echo '<div class="rows row-' . $row . '">';

					toggle_container( $footer_container );

                    echo '<div class="flex-x">';

                    for ( $column = 1; $column <= $columns; $column ++ ) :
	                    $footer_n = $column + $regions * ( $row - 1 );
	                    if ( is_active_sidebar( 'hd-footer-' . esc_attr( $footer_n ) ) ) :

		                    echo sprintf( '<div class="cell cell-%1$s">', esc_attr( $column ) );
		                    dynamic_sidebar( 'hd-footer-' . esc_attr( $footer_n ) );
		                    echo "</div>";

	                    endif;
                    endfor;

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                endif;
            endfor;
           ?>
        </div><!-- #footer-widgets-->
		<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_construct_footer_credit' ) ) {
	add_action( 'hd_footer', '__hd_construct_footer_credit', 10 );

	/**
	 * Build our footer
	 *
	 * @return void
	 */
	function __hd_construct_footer_credit(): void {
		$footer_container = Helper::getThemeMod( 'footer_container_setting' );

		?>
        <div id="footer-info" class="footer-info">
	        <?php

	        toggle_container( $footer_container );

	        /**
	         * @see __hd_before_credits - 15
	         */
	        do_action( 'hd_before_credits' );

	        /**
	         * @see __hd_credits - 10
	         */
	        do_action( 'hd_credits' );

	        echo '</div>';

            ?>
        </div><!-- #footer-info -->
		<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_before_credits' ) ) {
	add_action( 'hd_before_credits', '__hd_before_credits', 15 );

	/**
	 * @return void
	 */
	function __hd_before_credits(): void {
		if ( ! is_active_sidebar( 'footer-credits' ) ) {
			return;
		}
		?>
        <div class="footer-credits">
			<?php dynamic_sidebar( 'footer-credits' ); ?>
        </div>
		<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_credits' ) ) {
	add_action( 'hd_credits', '__hd_credits', 10 );

	/**
	 * Add the copyright to the footer
	 *
	 * @return void
	 */
	function __hd_credits(): void {
        echo '<div class="footer-copyright">';
		$copyright = sprintf(
			'<span class="copyright">&copy; %1$s %2$s</span><span class="hd">, %3$s <a class="_blank" title="%6$s" href="%4$s" %5$s>%6$s</a></span>',
			date( 'Y' ), // phpcs:ignore
			get_bloginfo( 'name' ),
			__( 'design by', TEXT_DOMAIN ),
			esc_url( 'https://webhd.vn' ),
			Helper::microdata( 'url' ),
			__( 'HD Agency', TEXT_DOMAIN )
		);

		echo apply_filters( 'hd_copyright', $copyright ); // phpcs:ignore
		echo '</div>';
	}
}

// -----------------------------------------------
