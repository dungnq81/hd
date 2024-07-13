<?php
/**
 * Home hooks
 *
 * @author HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// home_before_section
// -----------------------------------------------

if ( ! function_exists( '__hd_home_title' ) ) {
	add_action( 'home_before_section', '__hd_home_title', 10, 1 );

	function __hd_home_title(): void {

		$args = [];
		the_page_title_theme( $args );
	}
}

// -----------------------------------------------
// home_content
// -----------------------------------------------

if ( ! function_exists( '__hd_home_header' ) ) {
	add_action( 'home_content', '__hd_home_header', 10 );

	function __hd_home_header(): void {
		$post_page_id = (int) get_option( 'page_for_posts' );
		$post = get_post( $post_page_id );

		$alternative_title = Helper::get_field( 'alternative_title', $post_page_id );
		$desc              = Helper::postExcerpt( $post );

		?>
		<header class="text-center">
            <h1 class="heading-title"><?= $alternative_title ?: get_the_title( $post ) ?></h1>
			<?php echo Helper::stripSpace( $desc ) ? $desc : ''; ?>
        </header>
	<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_home_content' ) ) {
	add_action( 'home_content', '__hd_home_content', 12 );

	function __hd_home_content(): void {

        echo '<div class="grid-posts">';
		get_template_part( 'template-parts/posts/grid' );
        echo '</div>';
	}
}
