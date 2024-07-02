<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$acf_fc_layout = $args['acf_fc_layout'] ?? '';

$fc_subtitle    = $args['fc_subtitle'] ?? '';
$fc_title       = $args['fc_title'] ?? '';
$fc_product_cat = $args['fc_product_cat'] ?? false;
$fc_max_number  = $args['fc_max_number'] ?? 8;
$fc_button_link = $args['fc_button_link'] ?? '';
$fc_css_class   = ! empty( $args['fc_css_class'] ) ? ' ' . esc_attr_strip_tags( $args['fc_css_class'] ) : '';

wc_set_loop_prop( 'name', 'home_products_list' );

?>
<section class="section grid-section products-section<?= $fc_css_class ?>">
	<div class="container">
		<?php

		if ( $fc_subtitle ) { echo '<div class="subtitle">' . $fc_subtitle . '</div>'; }
		if ( $fc_title ) { echo '<h2 class="heading-title">' . $fc_title . '</h2>'; }

		$query_args = [
			'limit'   => $fc_max_number,
			'columns' => 4,
			'order'   => 'DESC',
            'orderby' => 'date',
			'title'   => wp_kses_post( $fc_title ),
		];

        if ( $fc_product_cat ) {
	        $category = [];
	        foreach ( $fc_product_cat as $cat ) {
		        $category[] = $cat->slug;
	        }

	        $query_args['category'] = implode( ',', $category );
        }

		$uniqid = Helper::esc_attr_strip_tags( uniqid( '-wc-list-', false ) );

		?>
        <div class="grid-products <?= $uniqid ?>" aria-label="<?php echo Helper::esc_attr_strip_tags( $fc_title ); ?>">
			<?php
			echo Helper::doShortcode(
				'products',
				$query_args
			);
			?>
        </div>

		<?php echo Helper::ACF_Link( $fc_button_link, 'button-link' ); ?>

	</div>
</section>
