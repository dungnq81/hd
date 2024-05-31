<?php

namespace Widgets;

use Cores\Abstract_Widget;
use Cores\Helper;

\defined( 'ABSPATH' ) || die;

class Posts_Carousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s Posts Carousels.' );
		$this->widget_name        = __( '* Posts Carousels', TEXT_DOMAIN );
		$this->settings           = [
			'title'                 => [
				'type'  => 'text',
				'std'   => __( 'Posts slideshow', TEXT_DOMAIN ),
				'label' => __( 'Title', TEXT_DOMAIN ),
			],
			'container'            => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Container layout', TEXT_DOMAIN ),
			],
			'include_children'      => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Include children', TEXT_DOMAIN ),
			],
			'show_cat'              => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post categories', TEXT_DOMAIN ),
			],
			'show_thumbnail'        => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post thumbnails', TEXT_DOMAIN ),
			],
			'show_date'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post date', TEXT_DOMAIN ),
			],
			'show_desc'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display post description', TEXT_DOMAIN ),
			],
			'show_detail_button'             => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Display detail button', TEXT_DOMAIN ),
			],
			'number'                => [
				'type'  => 'number',
				'min'   => 0,
				'max'   => 99,
				'std'   => 12,
				'class' => 'tiny-text',
				'label' => __( 'Maximum number of posts', TEXT_DOMAIN ),
			],
			'limit_time'            => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Time limit', TEXT_DOMAIN ),
				'desc'  => __( 'Restrict to only posts within a specific time period.', TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function styles_and_scripts() {
		wp_enqueue_style( "swiper-style", ASSETS_URL . "css/plugins/swiper.css", [], THEME_VERSION );
		wp_enqueue_script( "swiper", ASSETS_URL . "js/plugins/swiper.js", [], THEME_VERSION, true );
		wp_script_add_data( "swiper", "defer", true );
	}

	/**
	 * Outputs the content for the posts widget instance.
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @throws \JsonException
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$title = $this->get_instance_title( $instance );

		$container          = ! empty( $instance['container'] );
		$number             = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$show_cat           = ! empty( $instance['show_cat'] );
		$show_thumbnail     = ! empty( $instance['show_thumbnail'] );
		$show_date          = ! empty( $instance['show_date'] );
		$show_desc          = ! empty( $instance['show_desc'] );
		$show_detail_button = ! empty( $instance['show_detail_button'] );

		$include_children = ! empty( $instance['include_children'] );
		$limit_time       = $instance['limit_time'] ? trim( $instance['limit_time'] ) : $this->settings['limit_time']['std'];

		// ACF fields
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$heading_tag   = ! empty( $ACF->title_tag ) ? Helper::esc_attr_strip_tags( $ACF->title_tag ) : 'span';
		$heading_class = ! empty( $ACF->title_classes ) ? Helper::esc_attr_strip_tags( $ACF->title_classes ) : 'heading-title';

		$term_ids = $ACF->post_category_ids ?? [];

		$show_view_more_button = $ACF->show_view_more_button ?? false;
		$view_more_link        = $ACF->view_more_link ?? '';
		$view_more_link        = Helper::ACF_Link( $view_more_link );

		$query_args = [
			'term_ids'         => $term_ids,
			'include_children' => $include_children,
			'posts_per_page'   => $number,
			'limit_time'       => $limit_time,
			'wrapper'          => 'div',
			'wrapper_class'    => 'swiper-slide',
			'show'             => [
				'thumbnail' => Helper::toBool( $show_thumbnail ),
				//'thumbnail_size' => 'medium',
				//'scale' => true,
				'time'      => Helper::toBool( $show_date ),
				'term'      => Helper::toBool( $show_cat ),
				'desc'      => Helper::toBool( $show_desc ),
				'more'      => Helper::toBool( $show_detail_button ),
			],
		];

		$css_class = ! empty( $ACF->css_class ) ? ' ' . Helper::esc_attr_strip_tags( $ACF->css_class ) : '';
		$uniqid    = esc_attr( uniqid( $this->widget_classname . '-', false ) );

		ob_start();

		?>
        <section class="section carousel-section posts-carousel-section posts-section<?= $css_class ?>">
			<?php
			toggle_container( $container, 'container', '' );

			if ( $title ) {
				$args['before_title'] = '<' . $heading_tag . ' class="' . $heading_class . '">';
				$args['after_title'] = '</' . $heading_tag . '>';

				echo $args['before_title'] . $title . $args['after_title'];
			}

			?>
            <div class="<?= $uniqid ?>" aria-label="<?php echo Helper::esc_attr_strip_tags( $title ); ?>">
                <div class="swiper-section carousel-posts grid-posts">
					<?php
					$_data = $this->swiper_acf_options( $instance, $ACF );

					$swiper_class = $_data['class'] ?? '';
					$swiper_data  = $_data['data'] ?? json_encode( [], JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT );

					?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $swiper_data ?>'>
							<?php
							echo Helper::doShortcode(
								'posts',
								$query_args
							);
							?>
                        </div>
                    </div>
                </div>
            </div>
	        <?php

	        if ( $show_view_more_button ) {echo $view_more_link;}
	        if ( $container ) {echo '</div>';}

	        ?>
        </section>
		<?php
        echo $this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
	}
}
