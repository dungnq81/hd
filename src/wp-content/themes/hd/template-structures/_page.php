<?php
/**
 * Page hooks
 *
 * @author HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// page_before_section
// -----------------------------------------------

if ( ! function_exists( '__hd_page_title' ) ) {
	add_action( 'page_before_section', '__hd_page_title', 10, 1 );

	function __hd_page_title(): void {

		$args = [];
		the_page_title_theme( $args );
	}
}

// -----------------------------------------------
// page_content
// -----------------------------------------------

if ( ! function_exists( '__hd_page_header' ) ) {
	add_action( 'page_content', '__hd_page_header', 10 );

	function __hd_page_header(): void {
		global $post;
		$alternative_title = \get_field( 'alternative_title', $post->ID ) ?? '';

		?>
		<header class="text-center">
            <h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>

			<?php echo Helper::stripSpace( $post->post_excerpt ) ? '<div class="excerpt">' . Helper::nl2p( $post->post_excerpt ) . '</div>' : ''; ?>

        </header>
	<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_page_content' ) ) {
	add_action( 'home_content', '__hd_page_content', 12 );

	function __hd_page_content(): void {
		echo '<article ' . Helper::microdata( 'article' ) . '>';
		the_content();
		echo '</article>';
	}
}
