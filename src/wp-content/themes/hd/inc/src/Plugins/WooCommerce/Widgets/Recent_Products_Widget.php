<?php

namespace Plugins\WooCommerce\Widgets;

use Cores\Abstract_Widget;
use Cores\Helper;
use WP_Query;

\defined( 'ABSPATH' ) || die;

class Recent_Products_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( "Display a list of recent products from your store.", TEXT_DOMAIN );
		$this->widget_name        = __( '* Recent Products', TEXT_DOMAIN );
		$this->settings           = [
			'title'      => [
				'type'  => 'text',
				'std'   => __( 'Recent products', TEXT_DOMAIN ),
				'label' => __( 'Title' ),
			],
			'number'     => [
				'type'  => 'number',
				'min'   => 0,
				'max'   => 99,
				'std'   => 5,
				'class' => 'tiny-text',
				'label' => __( 'Number of products to show', TEXT_DOMAIN ),
			],
			'show'       => [
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Show', TEXT_DOMAIN ),
				'options' => [
					''         => __( 'All', TEXT_DOMAIN ),
					'featured' => __( 'Featured', TEXT_DOMAIN ),
					'on_sale'   => __( 'On-sale', TEXT_DOMAIN ),
				],
			],
			'orderby'    => [
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Order by', TEXT_DOMAIN ),
				'options' => [
					''      => __( 'Default', TEXT_DOMAIN ),
					'date'  => __( 'Date', TEXT_DOMAIN ),
					'price' => __( 'Price', TEXT_DOMAIN ),
					'rand'  => __( 'Random', TEXT_DOMAIN ),
					'sales' => __( 'Sales', TEXT_DOMAIN ),
				],
			],
			'order'      => [
				'type'    => 'select',
				'std'     => 'desc',
				'label'   => __( 'Sorting order', TEXT_DOMAIN ),
				'options' => [
					'asc'  => __( 'ASC', TEXT_DOMAIN ),
					'desc' => __( 'DESC', TEXT_DOMAIN ),
				],
			],
			'limit_time' => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Time limit', TEXT_DOMAIN ),
				'desc'  => __( 'Restrict to only posts within a specific time period.', TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * Query the products and return them.
	 *
	 * @param $number
	 * @param array $instance Widget instance.
	 *
	 * @return WP_Query
	 */
	public function get_products( $number, $instance ) {
		$show    = ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : $this->settings['show']['std'];
		$orderby = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
		$order   = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];

		$limit_time = $instance['limit_time'] ? trim( $instance['limit_time'] ) : $this->settings['limit_time']['std'];
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$query_args                  = [
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,

			'posts_per_page'      => $number,
			'post_status'         => 'publish',
			'post_type'           => 'product',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'order'               => $order,
			'tax_query'           => [ 'relation' => 'AND' ],
		]; // WPCS: slow query ok.

		// ...
		if ( $limit_time ) {

			// constrain to just posts in $limit_time
			$recent = strtotime( $limit_time );
			if ( Helper::isInteger( $recent ) ) {
				$query_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === Helper::getOption( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['tax_query'][] = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				],
			]; // WPCS: slow query ok.
		}

		// show
		switch ( $show ) {
			case 'featured':
				$query_args['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				];
				break;
			case 'on_sale':
				$product_ids_on_sale    = wc_get_product_ids_on_sale();
				$product_ids_on_sale[]  = 0;
				$query_args['post__in'] = $product_ids_on_sale;
				break;
		}

		// orderby
		switch ( $orderby ) {
			case 'price':
				$query_args['meta_key'] = '_price'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			case 'sales':
				$query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'date':
				$query_args['orderby'] = 'date';
				break;
		}

		set_posts_per_page( $number );

		return new WP_Query( apply_filters( 'recent_products_widget_query_args', $query_args ) );
	}

	/**
	 * Output widget.
	 *
	 * @param array $args Arguments.
	 * @param array $instance Widget instance.
	 *
	 * @see WP_Widget
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		// ACF
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$title = $this->get_instance_title( $instance );
		$number  = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		$products = $this->get_products( $number, $instance );
		if ( ! $products || ! $products->have_posts() ) {
			return;
		}

		$css_class = ! empty( $ACF->css_class ) ? ' ' . esc_attr_strip_tags( $ACF->css_class ) : '';
		$uniqid    = esc_attr_strip_tags( uniqid( $this->widget_classname . '-', true ) );

		// has products
		wc_set_loop_prop( 'name', 'recent_products_widget' );

		ob_start();

		?>
        <section class="section recent-products-section<?= $css_class ?>">

			<?php if ( $title ) {
				echo '<h2 class="heading-title">' . $title . '</h2>';
			} ?>

            <div class="<?= $uniqid ?>" aria-label="<?php echo esc_attr_strip_tags( $title ); ?>">
                <div class="grid-products">
					<?php
					$i = 0;

					$template_args = [
						'widget_id'   => $args['widget_id'] ?? $this->widget_id,
						'show_rating' => true,
					];

					// Load slides loop
					while ( $products->have_posts() && $i < $number ) : $products->the_post();
						global $product;

						if ( empty( $product ) ||
						     ! $product->is_visible() ||
						     false === wc_get_loop_product_visibility( $product->get_id() )
						) {
							continue;
						}

						echo '<div class="cell cell-' . $i . '">';
						wc_get_template( 'content-widget-product.php', $template_args );
						echo '</div>';

						++ $i;
					endwhile;
					wp_reset_postdata();

					?>
                </div>
            </div>
        </section>
		<?php

		echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
	}
}
