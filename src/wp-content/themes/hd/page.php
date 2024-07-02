<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'page' );

if ( have_posts() ) {
	the_post();
}

if ( post_password_required() ) :
	echo get_the_password_form(); // WPCS: XSS ok.

	return;
endif;

// template-parts/parts/page-title.php
the_page_title_theme();

$ID = $post->ID ?? false;
try {
	$ACF = Helper::acfFields( $ID ) ?? '';
} catch ( JsonException $e ) {}

$alternative_title = $ACF->alternative_title ?? '';
$image_for_banner  = $ACF->image_for_banner ?? false;

?>
<section class="section singular page">
	<div class="container">
		<header>
			<h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>
			<?php echo Helper::stripSpace( $post->post_excerpt ) ? '<div class="excerpt">' . Helper::nl2p( $post->post_excerpt ) . '</div>' : ''; ?>
		</header>
		<article <?=Helper::microdata( 'article' )?>>

			<?php the_content(); ?>

		</article>
	</div>
</section>
<?php

// footer
get_footer( 'page' );
