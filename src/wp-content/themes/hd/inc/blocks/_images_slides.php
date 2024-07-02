<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$acf_fc_layout = $args['acf_fc_layout'] ?? '';

$banner_cat = $args['fc_banner_cat'] ?? [];
$navigation = $args['fc_navigation'] ?? false;
$pagination = $args['fc_pagination'] ?? false;
$autoplay   = $args['fc_autoplay'] ?? false;
$max_number = $args['fc_max_number'] ?? -1;
$css_class  = ! empty( $args['fc_css_class'] ) ? ' ' . esc_attr_strip_tags( $args['fc_css_class'] ) : '';

$slides_query = Helper::queryByTerms( $banner_cat, 'banner', 'banner_cat', false, $max_number );
if ( ! $slides_query ) {
	return;
}

ob_start();

?>
<section class="section images-carousel media-carousel<?= $css_class ?>">
    <div class="swiper-container">
        <?php
        $swiper_class = ' auto-view';
        $_data = [
	        'loop'     => true,
	        'autoview' => true,
        ];

        if ( $navigation ) {
	        $_data['navigation'] = Helper::toBool( $navigation );
        }

        if ( $pagination ) {
	        $_data['pagination'] = 'bullets';
	        $swiper_class        .= ' pagination-bullets';
        }

        if ( $autoplay ) {
	        $_data['autoplay'] = Helper::toBool( $autoplay );
        }

        // _data
        try {
	        $swiper_data = json_encode( $_data, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );
        } catch ( JsonException $e ) {}

        if ( $swiper_data ) :

        ?>
        <div class="w-swiper swiper">
            <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
                <?php
                $i = 0;

                // Load slides loop.
                while ( $slides_query->have_posts() ) : $slides_query->the_post();
                    $post = get_post();

	                $i++;
	                if ( $i > 1 && Helper::Lighthouse() ) {
		                break;
	                }

                    if ( has_post_thumbnail() ) :

	                    $ACF_banner = Helper::acfFields( $post->ID );
	                    $banner_url = $ACF_banner->banner_url ?? '';
	                    $responsive_image = $ACF_banner->responsive_image ?? '';

	                    $_video_class = '';
	                    $video_url = $ACF_banner->video_url ?? '';
	                    if ( $video_url ) :
		                    $_video_class = ' has-video';
	                    endif;

	                    $_bg_class = '';
	                    $bg_color = $ACF_banner->bg_color ?? '';
	                    if ( $bg_color ) :
		                    $_bg_class = ' style="background-color: ' . $bg_color . '"';
	                    endif;

                ?>
                <div class="swiper-slide<?= $_bg_class ?>">
                    <div class="item">
                        <div class="overlay">
	                        <?php if ( ! Helper::Lighthouse() ) : ?>
                            <picture class="<?=$_video_class?>">
                                <?php if ( $responsive_image ) : ?>
                                <source srcset="<?= Helper::attachmentImageSrc( $responsive_image, 'thumbnail' ) ?>" media="(max-width: 576px)">
                                <source srcset="<?= Helper::attachmentImageSrc( $responsive_image, 'medium' ) ?>" media="(max-width: 768px)">
                                <?php else : ?>
                                <source srcset="<?= Helper::postImageSrc( $post->ID, 'thumbnail' ) ?>" media="(max-width: 576px)">
                                <source srcset="<?= Helper::postImageSrc( $post->ID, 'medium' ) ?>" media="(max-width: 768px)">
                                <?php endif; ?>
                                <source srcset="<?= Helper::postImageSrc( $post->ID, 'large' ) ?>" media="(max-width: 1024px)">
                                <source srcset="<?= Helper::postImageSrc( $post->ID, 'post-thumbnail' ) ?>" media="(max-width: 1280px)">
	                            <?php echo get_the_post_thumbnail( $post->ID, 'widescreen', [ 'alt' => esc_attr( get_the_title() ) ] ); ?>
                            </picture>
                            <?php
                            elseif ( Helper::Lighthouse() ) :
		                        echo '<picture class="' . $_video_class . '">';
	                            if ( $responsive_image ) { echo '<source srcset="' . Helper::attachmentImageSrc( $responsive_image, 'small-thumbnail' ) . '" media="(max-width: 768px)">'; }
                                else { echo '<source srcset="' . Helper::postImageSrc( $post->ID, 'small-thumbnail' ) . '" media="(max-width: 768px)">'; }
	                            echo get_the_post_thumbnail( $post->ID, 'medium', [ 'alt' => esc_attr( get_the_title() ) ] );
	                            echo '</picture>';
                            endif;

	                        if ( $banner_url ) { echo Helper::ACF_Link_Wrap( '', $banner_url, 'link-overlay', 'banner', '' ); }
	                        if ( $video_url ) { echo Helper::ACF_Link_Wrap( '', $video_url, 'link-overlay fcy-video', 'video', '' ); }
                            ?>
                        </div>
                        <?php

                        $html_title  = $ACF_banner->html_title ?? '';
                        $description = $ACF_banner->description ?? '';
                        $button_link = $ACF_banner->button_link ?? '';

	                    if ( Helper::stripSpace( $html_title ) ) : ?>
                        <div class="overlay-content">
                            <div class="inner">
                                <h2 class="html-title"><?= $html_title ?></h2>
	                            <?php if ( Helper::stripSpace( $description ) ) : ?>
                                <div class="html-desc"><?= $description ?></div>
	                            <?php endif;

	                            echo Helper::ACF_Link( $button_link, 'button-link' );

                                ?>
                            </div>
                        </div>
	                    <?php endif; ?>
                    </div>
                </div>
                <?php
                    endif;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php
    echo ob_get_clean(); // WPCS: XSS ok.
