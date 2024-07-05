<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$acf_fc_layout = $args['acf_fc_layout'] ?? '';

$fc_subtitle    = $args['fc_subtitle'] ?? '';
$fc_title       = $args['fc_title'] ?? '';
$fc_product_cat = $args['fc_product_cat'] ?? false;
$fc_navigation  = $args['fc_navigation'] ?? true;
$fc_pagination  = $args['fc_pagination'] ?? true;
$fc_autoplay    = $args['fc_autoplay'] ?? true;
$fc_css_class   = ! empty( $args['fc_css_class'] ) ? ' ' . Helper::esc_attr_strip_tags( $args['fc_css_class'] ) : '';

ob_start();

?>
<section class="section carousel-section product-cat-section<?= $fc_css_class ?>">
	<div class="container">
		<?php

		if ( $fc_subtitle ) { echo '<div class="subtitle">' . $fc_subtitle . '</div>'; }
		if ( $fc_title ) { echo '<h2 class="heading-title">' . $fc_title . '</h2>'; }

        if ( $fc_product_cat ) :

		?>
        <div class="swiper-container carousel-product-cat grid-product-cat">
	        <?php

	        $swiper_class = '';
	        $_data = [
		        'loop' => true,
		        'mobile' => [
			        'spaceBetween'   => 20,
			        'slidesPerView'  => 2,
			        'slidesPerGroup' => 1,
		        ],
		        'tablet' => [
			        'spaceBetween'   => 20,
			        'slidesPerView'  => 2,
			        'slidesPerGroup' => 1,
		        ],
		        'desktop' => [
			        'spaceBetween'   => 30,
			        'slidesPerView'  => 4,
			        'slidesPerGroup' => 1,
		        ]
	        ];

	        if ( $fc_navigation ) {
		        $_data['navigation'] = Helper::toBool( $fc_navigation );
	        }

	        if ( $fc_pagination ) {
		        $_data['pagination'] = 'bullets';
		        $swiper_class        .= ' pagination-bullets';
	        }

	        if ( $fc_autoplay ) {
		        $_data['autoplay'] = Helper::toBool( $fc_autoplay );
	        }

            // swiper_data
	        try {
		        $swiper_data = json_encode( $_data, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );
	        } catch ( JsonException $e ) {}

            if ( $swiper_data ) :

	        ?>
            <div class="w-swiper swiper">
                <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
                    <?php
                    $i = 0;

	                foreach ( $fc_product_cat as $key => $term_id ) :
		                $i++;
		                if ( $i > 1 && Helper::Lighthouse() ) {
			                break;
		                }

		                $term = get_term( $term_id );
		                $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );

		                $scale_class = 'scale';
		                $ratio_class = Helper::aspectRatioClass( 'product_cat', 'ar[16-9]');
                    ?>
                    <div class="swiper-slide">
                        <a <?php wc_product_cat_class( 'block item' ); ?> href="<?php echo get_term_link( $term_id, 'product_cat' ); ?>" title="<?php echo Helper::esc_attr_strip_tags( $term->name ); ?>">
                            <div class="cover">
                                <?php

                                echo '<span class="' . $scale_class . ' after-overlay res ' . $ratio_class . '">';
                                echo wp_get_attachment_image( $thumbnail_id, 'medium' );
                                echo '</span>';

                                ?>
                            </div>
                            <div class="cover-content">
                                <h3><?php echo $term->name; ?></h3>
		                        <?php if ( $term->description ) : ?>
                                <div class="desc"><?php echo Helper::excerpt( $term->description, 15 ) ?></div>
		                        <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
	</div>
</section>
<?php
echo ob_get_clean(); // WPCS: XSS ok.
