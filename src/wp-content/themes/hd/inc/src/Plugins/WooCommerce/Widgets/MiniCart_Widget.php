<?php

namespace Plugins\WooCommerce\Widgets;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

class MiniCart_Widget extends \WC_Widget_Cart {
	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( apply_filters( 'woocommerce_widget_cart_is_hidden', is_cart() || is_checkout() ) ) {
			return;
		}

		$args['before_widget'] = '<div class="mini_widget_shopping_cart">';
		$args['after_widget'] = '</div>';

		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		$title = $this->get_instance_title( $instance );
		if ( $title ) {
			echo '<span class="cart-title">' . $title . '</span>';
		}

		$class = 'menu-item';
		if ( is_cart() || is_checkout() ) {
			$class .= ' current-menu-item';
		}
		?>
		<ul id="shopping-cart" class="shopping-cart menu">
			<li class="<?php echo Helper::esc_attr_strip_tags( $class ); ?>">
				<?php _wc_cart_link(); ?>
			</li>
			<li class="widget-menu-item menu-item">
				<?php the_widget('WC_Widget_Cart', 'title='); ?>
			</li>
		</ul>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}
