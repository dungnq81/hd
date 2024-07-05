<?php
/**
 * The template for displaying 'About Us'
 * Template Name: About Us
 * Template Post Type: page
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'about-us' );

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
$gallery           = $ACF->gallery ?? false;

$post_thumbnail = get_the_post_thumbnail( $post, 'medium' );

?>
<section class="section singular page page-about-us">
	<div class="container">
        <header>
            <h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>

            <?php echo Helper::stripSpace( $post->post_excerpt ) ? '<div class="excerpt">' . Helper::nl2p( $post->post_excerpt ) . '</div>' : ''; ?>
            <?php echo $post_thumbnail; ?>

        </header>
        <article <?=Helper::microdata( 'article' )?>>
            <?php the_content(); ?>

            <?php if ( $gallery ) : ?>
            <div class="swiper-container gallery-inner">
                <?php
                $swiper_class = ' auto-view pagination-bullets swiper-marquee';
                $_data = [
	                'loop'       => true,
	                'autoview'   => true,
	                'pagination' => 'bullets',
	                'autoplay'   => true,
	                'marquee'    => true,
	                'gap'        => true,
                ];

                try {
	                $swiper_data = json_encode( $_data, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );
                } catch ( JsonException $e ) {}

                if ( $swiper_data ) :

                ?>
                <div class="w-swiper swiper">
                    <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
                        <?php
                        $i = 0;
	                    foreach ( $gallery as $gal_id => $gal ) :

		                    $i++;
		                    if ( $i > 1 && Helper::Lighthouse() ) {
			                    break;
		                    }

		                    $attachment_meta = Helper::getAttachment( $gal );
		                    $_href = false;
		                    if ( '#' === $attachment_meta->description || filter_var( $attachment_meta->description, FILTER_VALIDATE_URL ) ) {
			                    $_href = $attachment_meta->description;
		                    }
                        ?>
                        <div class="swiper-slide">
                            <figure>
                                <?php

                                if ( $_href ) { echo '<a class="after-overlay" href="' . $_href . '" title="' . Helper::esc_attr_strip_tags( $attachment_meta->alt ) . '">'; }
                                echo wp_get_attachment_image( $gal, 'medium' );
                                if ( $_href ) { echo '</a>'; }

                                ?>
                            </figure>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </article>
	</div>
</section>
<?php

// footer
get_footer( 'about-us' );
