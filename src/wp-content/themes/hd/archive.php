<?php
/**
 * The template for displaying Archive pages.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'archive' );

$object = get_queried_object();

$desc = '';
if ( isset( $object->term_id ) ) {
	$desc = Helper::termExcerpt( $object->term_id );
}

// template-parts/parts/archive-title.php
the_archive_title_theme();

?>
<section class="section archive">
    <div class="container">
        <header class="text-center">
            <h1 class="heading-title"><?= get_the_archive_title() ?></h1>
		    <?php echo Helper::stripSpace( $desc ) ? $desc : ''; ?>
        </header>
        <div class="grid-posts">

		    <?php get_template_part( 'template-parts/posts/grid', null, [ 'sidebar' => true ] ); ?>

        </div>
    </div>
</section>
<?php

// footer
get_footer( 'archive' );
