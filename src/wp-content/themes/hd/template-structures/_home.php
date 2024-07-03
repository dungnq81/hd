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

if ( ! function_exists( '__hd_page_title' ) ) {
	add_action( 'home_before_section', '__hd_page_title', 10 );

	/**
	 * @return void
	 */
	function __hd_page_title(): void {

		// template-parts/parts/page-title.php
		the_page_title_theme();
	}
}

// -----------------------------------------------
// home_page_content
// -----------------------------------------------

if ( ! function_exists( '__hd_home_page_header' ) ) {
	add_action( 'home_page_content', '__hd_home_page_header', 10 );

	/**
	 * @return void
	 */
	function __hd_home_page_header(): void {
		$post_page_id = (int) get_option( 'page_for_posts' );
		$post = get_post( $post_page_id );

		$alternative_title = \get_field( 'alternative_title', $post_page_id ) ?? '';
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

if ( ! function_exists( '__hd_home_page_content' ) ) {
	add_action( 'home_page_content', '__hd_home_page_content', 11 );

	/**
	 * @return void
	 */
	function __hd_home_page_content(): void {
		?>
		<div class="grid-posts">
	        <?php get_template_part( 'template-parts/posts/grid' ); ?>
        </div>
	<?php
	}
}
