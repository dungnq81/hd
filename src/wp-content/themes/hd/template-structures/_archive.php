<?php
/**
 * Archive hooks
 *
 * @author HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// -----------------------------------------------
// archive_before_section
// -----------------------------------------------

if ( ! function_exists( '__hd_archive_title' ) ) {
	add_action( 'archive_before_section', '__hd_archive_title', 10, 1 );

	function __hd_archive_title(): void {

		$args = [];
		the_archive_title_theme( $args );
	}
}

// -----------------------------------------------
// archive_content
// -----------------------------------------------

if ( ! function_exists( '__hd_archive_header' ) ) {
	add_action( 'archive_content', '__hd_archive_header', 10 );

	function __hd_archive_header(): void {
		$object = get_queried_object();

		$desc = '';
		if ( isset( $object->term_id ) ) {
			$term_id = (int) $object->term_id;
			$desc = Helper::termExcerpt( $term_id );
		}

		?>
		<header class="text-center">
            <h1 class="heading-title"><?= get_the_archive_title() ?></h1>
			<?php echo $desc; ?>
        </header>
	<?php
	}
}

// -----------------------------------------------

if ( ! function_exists( '__hd_archive_content' ) ) {
	add_action( 'archive_content', '__hd_archive_content', 11 );

	function __hd_archive_content(): void {

        echo '<div class="grid-posts">';
		get_template_part( 'template-parts/posts/grid', get_post_type(), [ 'sidebar' => true ] );
        echo '</div>';
	}
}
