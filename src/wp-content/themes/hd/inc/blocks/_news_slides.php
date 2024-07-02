<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$acf_fc_layout = $args['acf_fc_layout'] ?? '';

$fc_subtitle    = $args['fc_subtitle'] ?? '';
$fc_title       = $args['fc_title'] ?? '';
$fc_post_cat    = $args['fc_post_cat'] ?? false;
$fc_max_number  = $args['fc_max_number'] ?? '1';
$fc_navigation  = $args['fc_navigation'] ?? true;
$fc_pagination  = $args['fc_pagination'] ?? true;
$fc_autoplay    = $args['fc_autoplay'] ?? true;
$fc_button_link = $args['fc_button_link'] ?? '';
$fc_css_class   = ! empty( $args['fc_css_class'] ) ? ' ' . esc_attr_strip_tags( $args['fc_css_class'] ) : '';

$_args          = [
	'post_type'              => 'post',
	'post_status'            => 'publish',
	'posts_per_page'         => $fc_max_number,
	'no_found_rows'          => true,
	'ignore_sticky_posts'    => true,
];

if ( ! empty( $fc_post_cat ) ) {
    $_args['tax_query'] = [ 'relation' => 'AND' ];

	$term_ids = Helper::removeEmptyValues( $fc_post_cat );
	if ( count( $term_ids ) > 0 ) {
		$_args['tax_query'][] = [
			'taxonomy'         => 'category',
			'terms'            => $term_ids,
			'field'            => 'term_id',
			'include_children' => false,
			'operator'         => 'IN',
		];
	}
}

// set custom posts_per_page
set_posts_per_page( $fc_max_number );

// query
$_query = new WP_Query( $_args );
if ( ! $_query->have_posts() ) {
	return;
}

ob_start();

?>
<section class="section carousel-section posts-section<?= $fc_css_class ?>">
	<div class="container">
		<?php

		if ( $fc_subtitle ) { echo '<div class="subtitle">' . $fc_subtitle . '</div>'; }
		if ( $fc_title ) { echo '<h2 class="heading-title">' . $fc_title . '</h2>'; }

		?>
        <div class="swiper-container carousel-posts grid-posts">
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
	                'slidesPerView'  => 3,
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

                    // Load slides loop.
                    while ( $_query->have_posts() ) : $_query->the_post();
                        $post = get_post();

                        $i++;
                        if ( $i > 1 && Helper::Lighthouse() ) {
                            break;
                        }
                    ?>
                    <div class="swiper-slide">

                        <?php get_template_part( 'template-parts/posts/loop', null, [ 'title-tag' => 'h3' ] ); ?>

                    </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php endif; ?>
		</div>

        <?php echo Helper::ACF_Link( $fc_button_link, 'button-link' ); ?>

	</div>
</section>
<?php
echo ob_get_clean(); // WPCS: XSS ok.

