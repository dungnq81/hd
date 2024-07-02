<?php
/**
 * The template for displaying Home (Blog) pages.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'home' );

$post_page_id = (int) get_option( 'page_for_posts' );
$object = get_queried_object();

if ( $post_page_id !== (int) $object->ID ) {
    Helper::redirect( Helper::home(), 301 );
}

$desc = Helper::postExcerpt( $object );

// template-parts/parts/page-title.php
the_page_title_theme();

try {
	$ACF = Helper::acfFields( $post_page_id ) ?? '';
} catch ( JsonException $e ) {}

$alternative_title = $ACF->alternative_title ?? '';
$image_for_banner  = $ACF->image_for_banner ?? false;

?>
<section class="section archive blog-page">
    <div class="container">
        <header class="text-center">
            <h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>
		    <?php echo Helper::stripSpace( $desc ) ? $desc : ''; ?>
        </header>

        <div class="grid-posts">

	        <?php get_template_part( 'template-parts/posts/grid' ); ?>

        </div>
    </div>
</section>
<?php

// footer
get_footer( 'home' );
