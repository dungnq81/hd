<?php
/**
 * The Template for displaying all single posts.
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header();

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
<section class="section singular post">
	<div class="container">

		<?php get_template_part( 'template-parts/parts/sharing' ); ?>

		<div class="content-col">
			<header>
                <h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>

                <?php echo Helper::postExcerpt( $post, 'excerpt', true );?>

			</header>
            <article <?=Helper::microdata( 'article' )?>>

				<?php the_content(); ?>

            </article>
		</div>
	</div>
</section>
<?php

// footer
get_footer();
