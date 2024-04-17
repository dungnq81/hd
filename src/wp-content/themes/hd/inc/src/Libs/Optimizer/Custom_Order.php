<?php

namespace Libs\Optimizer;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

final class Custom_Order {

	public function __construct() {
		$this->_init();

		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 33, 1 );
	}

	// ------------------------------------------------------

	/**
	 * @param $hook
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ): void {
		if ( $this->_check_custom_order_script() ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _init(): void {
		$_custom_order_ = Helper::getThemeMod( '_custom_order_' );
		if ( ! $_custom_order_ ) {
			global $wpdb;

			$result = $wpdb->query( "DESCRIBE {$wpdb->terms} `term_order`" );
			if ( ! $result ) {
				$query = "ALTER TABLE {$wpdb->terms} ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
				$wpdb->query( $query );
			}

			set_theme_mod( '_custom_order_', 1 );
		}


	}

	/** ---------------------------------------- */

	/**
	 * @return bool
	 */
	private function _check_custom_order_script(): bool {

		// Check custom order
		$custom_order_options = Helper::getOption( 'custom_order__options', [], true );
		$order_post_type      = $custom_order_options['order_post_type'] ?? [];
		$order_taxonomy       = $custom_order_options['order_taxonomy'] ?? [];

		$active = false;

		if ( empty( $order_post_type ) && empty( $order_taxonomy ) ) {
			return false;
		}

		if ( isset( $_GET['orderby'] ) || strstr( $_SERVER['REQUEST_URI'], 'action=edit' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) ) {
			return false;
		}

		if ( ! empty( $order_post_type ) ) {
			if ( isset( $_GET['post_type'] ) && ! isset( $_GET['taxonomy'] ) && in_array( $_GET['post_type'], $order_post_type ) ) {
				$active = true;
			}
			if ( ! isset( $_GET['post_type'] ) && strstr( $_SERVER['REQUEST_URI'], 'wp-admin/edit.php' ) && in_array( 'post', $order_post_type ) ) {
				$active = true;
			}
		}

		if ( ! empty( $order_taxonomy ) ) {
			if ( isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], $order_taxonomy ) ) {
				$active = true;
			}
		}

		return $active;
	}
}
