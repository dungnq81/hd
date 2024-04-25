<?php

namespace Widgets;

use Cores\Abstract_Widget;
use Cores\Helper;

\defined( 'ABSPATH' ) || die;

class Dropdown_Search_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the dropdown search form for your site', TEXT_DOMAIN );
		$this->widget_name        = __( '* Dropdown Search', TEXT_DOMAIN );
		$this->settings = [
			'title'         => [
				'type'  => 'text',
				'std'   => __( 'Search', TEXT_DOMAIN ),
				'label' => __( 'Title', TEXT_DOMAIN ),
			],
			'popup_overlay' => [
				'type'  => 'checkbox',
				'std'   => '',
				'class' => 'checkbox',
				'label' => __( 'Overlay', TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ): void {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$title = $this->get_instance_title( $instance );
		$css_class = ! empty( $ACF->css_class ) ? esc_attr_strip_tags( $ACF->css_class ) : '';
		$popup_overlay = ! empty( $instance['popup_overlay'] );

		if ( $popup_overlay ) {
			$css_class = 'popup-overlay ' . $css_class;
		}

		$shortcode_content = Helper::doShortcode(
			'dropdown_search',
			apply_filters(
				'dropdown_search_widget_shortcode_args',
				[
					'title' => $title,
					'class' => $css_class,
					'id'    => '',
				]
			)
		);

		echo $this->cache_widget( $args, $shortcode_content ); // WPCS: XSS ok.
	}
}
