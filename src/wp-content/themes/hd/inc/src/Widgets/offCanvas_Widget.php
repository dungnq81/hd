<?php

namespace Widgets;

use Cores\Abstract_Widget;
use Cores\Helper;

\defined( 'ABSPATH' ) || die;

class offCanvas_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display offCanvas Button', TEXT_DOMAIN );
		$this->widget_name        = __( '* OffCanvas Button', TEXT_DOMAIN );
		$this->settings           = [
			'hide_if_desktop' => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide if desktop devices', TEXT_DOMAIN ),
			],
		];

		parent::__construct();
	}

	/**
	 * Creating widget front-end
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

		$ACF             = $this->acfFields( 'widget_' . $args['widget_id'] );
		$hide_if_desktop = empty( $instance['hide_if_desktop'] ) ? 0 : 1;
		$css_class       = ! empty( $ACF->css_class ) ? Helper::esc_attr_strip_tags( $ACF->css_class ) : '';

		$shortcode_content = Helper::doShortcode(
			'off_canvas_button',
			apply_filters(
				'off_canvas_widget_shortcode_args',
				[
					'title'           => '',
					'hide_if_desktop' => $hide_if_desktop,
					'class'           => $css_class,
				]
			)
		);

		echo $this->cache_widget( $args, $shortcode_content ); // WPCS: XSS ok.
	}
}
