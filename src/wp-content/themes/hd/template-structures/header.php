<?php
/**
 * Header elements
 *
 * @author HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// wp_head hook
// -----------------------------------------------

if ( ! function_exists( '__wp_head' ) ) {
	add_action( 'wp_head', '__wp_head', 1 );

	/**
	 * @return void
	 */
	function __wp_head() : void {

		// Add viewport to wp_head
        $meta_viewport = '<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0" />';
		echo apply_filters( 'hd_meta_viewport', $meta_viewport );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Add a ping-back url auto-discovery header for singularly identifiable articles.
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s" />', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}

		// Theme color
		$theme_color = Helper::getThemeMod( 'theme_color_setting' );
		if ( $theme_color ) {
			echo '<meta name="theme-color" content="' . $theme_color . '" />';
		}

        // Fb
		$fb_appid = Helper::getThemeMod( 'social_fb_setting' );
		if ( $fb_appid ) {
			echo '<meta property="fb:app_id" content="' . $fb_appid . '" />';
		}
	}
}

// -----------------------------------------------
// hd_before_header hook
// -----------------------------------------------

if ( ! function_exists( '__hd_skip_to_content_link' ) ) {
	add_action( 'hd_before_header', '__hd_skip_to_content_link', 2 );

	/**
	 * Add skip to a content link before the header.
	 *
	 * @return void
	 */
	function __hd_skip_to_content_link(): void {
		printf(
			'<a class="screen-reader-text skip-link" href="#site-content" title="%1$s">%2$s</a>',
			esc_attr__( 'Skip to content', TEXT_DOMAIN ),
			esc_html__( 'Skip to content', TEXT_DOMAIN )
		);
	}
}

// -----------------------------------------------

if ( ! function_exists( '__off_canvas_menu' ) ) {
	add_action( 'hd_before_header', '__off_canvas_menu', 10 );

	/**
	 * Position canvas menu
	 *
	 * @return void
	 */
	function __off_canvas_menu(): void {

		$position = Helper::getThemeMod( 'offcanvas_menu_setting' );
		if ( ! in_array( $position, [ 'left', 'right', 'top', 'bottom' ] ) ) {
			$position = 'right';
		}

		// Check if OffCanvas_Widget active
		if ( is_active_widget( false, false, 'w-offcanvas', true ) ) {
			get_template_part( 'template-parts/header/off-canvas/' . $position );
		}
	}
}

// -----------------------------------------------
// hd_header hook
// -----------------------------------------------

if ( ! function_exists( '__hd_construct_header' ) ) {
	add_action( 'hd_header', '__hd_construct_header', 10 );

	/**
	 * @return void
	 */
	function __hd_construct_header(): void {
		?>
		<header id="masthead" class="site-header" <?php echo Helper::microdata( 'header' ); ?>>
            <?php

            /**
             * @see __top_header - 10
             * @see __header - 11
             * @see __bottom_header - 12
             */
            do_action( 'masthead' );

            ?>
		</header>
		<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__top_header' ) ) {
	add_action( 'masthead', '__top_header', 10 );

	/**
	 * @return void
	 */
	function __top_header(): void {
		$top_header_cols      = (int) Helper::getThemeMod( 'top_header_setting' );
		$top_header_container = Helper::getThemeMod( 'top_header_container_setting' );

		if ( $top_header_cols > 0 ) :

        ?>
        <div class="top-header" id="top-header">
	        <?php
	        if ( $top_header_container ) echo '<div class="grid-container">';
	        else echo '<div class="grid-container fluid">';

		    for ( $i = 1; $i <= $top_header_cols; $i++ ) :
			    if ( is_active_sidebar( 'hd-top-header-' . $i . '-sidebar' )) :
				    echo '<div class="cell cell-' . $i . '">';
				    dynamic_sidebar( 'hd-top-header-' . $i . '-sidebar' );
				    echo '</div>';
			    endif;
            endfor;

            echo '</div>';
            ?>
        </div>
    <?php endif;
	}
}

// -----------------------------------------------

if ( ! function_exists( '__header' ) ) {
	add_action( 'masthead', '__header', 11 );

	/**
	 * @return void
	 */
	function __header(): void {
		$header_cols = (int) Helper::getThemeMod( 'header_setting' );
		$header_container = Helper::getThemeMod( 'header_container_setting' );

		if ( $header_cols > 0 ) :

        ?>
        <div class="inside-header" id="inside-header">
	        <?php
	        if ( $header_container ) echo '<div class="grid-container">';
	        else echo '<div class="grid-container fluid">';

	        for ( $i = 1; $i <= $header_cols; $i++ ) :
		        if ( is_active_sidebar( 'hd-header-' . $i . '-sidebar' )) :
			        echo '<div class="cell cell-' . $i . '">';
			        dynamic_sidebar( 'hd-header-' . $i . '-sidebar' );
			        echo '</div>';
		        endif;
	        endfor;

            echo '</div>';
            ?>
        </div>
    <?php endif;
	}
}

// -----------------------------------------------

if ( ! function_exists( '__bottom_header' ) ) {
	add_action( 'masthead', '__bottom_header', 12 );

	/**
	 * @return void
	 */
	function __bottom_header(): void {
		$bottom_header_cols      = (int) Helper::getThemeMod( 'bottom_header_setting' );
		$bottom_header_container = Helper::getThemeMod( 'bottom_header_container_setting' );

		if ( $bottom_header_cols > 0 ) :

        ?>
        <div class="bottom-header header-content" id="bottom-header">
            <?php
            if ( $bottom_header_container ) echo '<div class="grid-container">';
            else echo '<div class="grid-container fluid">';

            for ( $i = 1; $i <= $bottom_header_cols; $i++ ) :
	            if ( is_active_sidebar( 'hd-bottom-header-' . $i . '-sidebar' )) :
		            echo '<div class="cell cell-' . $i . '">';
		            dynamic_sidebar( 'hd-bottom-header-' . $i . '-sidebar' );
		            echo '</div>';
	            endif;
            endfor;

            echo '</div>';
            ?>
        </div>
    <?php endif;
	}
}