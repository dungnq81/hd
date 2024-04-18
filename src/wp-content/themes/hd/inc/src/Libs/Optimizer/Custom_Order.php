<?php

namespace Libs\Optimizer;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Order Items (Posts, Pages, and Custom Post Types) using a Drag-and-Drop Sortable JavaScript.
 *
 * @author Colorlib
 * Modified by HD Team
 *
 * @copyright 2013-2017 Sameer Humagain im@hsameer.com.np
 * @copyright 2017-2023 Colorlib support@colorlib.com
 */

final class Custom_Order {

	private mixed $order_post_type;
	private mixed $order_taxonomy;

	public function __construct() {
		$this->_init();

		// Check custom order
		$custom_order_options  = Helper::getOption( 'custom_order__options', [] );
		$this->order_post_type = $custom_order_options['order_post_type'] ?? [];
		$this->order_taxonomy  = $custom_order_options['order_taxonomy'] ?? [];

		if ( ! empty( $this->order_post_type ) || ! empty( $this->order_taxonomy ) ) {
			$this->_init_run();
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

	// ------------------------------------------------------

	private function _init_run(): void {
		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 33, 1 );

		// posts
		add_action( 'pre_get_posts', [ &$this, 'custom_order_pre_get_posts' ] );

		// Dynamic hook get_(adjacent)_post_sort
		add_filter( 'get_previous_post_sort', [ &$this, 'custom_order_previous_post_sort' ] );
		add_filter( 'get_next_post_sort', [ &$this, 'custom_order_next_post_sort' ] );

		// Dynamic hook get_(adjacent)_post_where
		add_filter( 'get_previous_post_where', [ &$this, 'custom_order_previous_post_where' ] );
		add_filter( 'get_next_post_where', [ &$this, 'custom_order_next_post_where' ] );

		// terms
		add_filter( 'get_terms_orderby', [ &$this, 'custom_order_get_terms_orderby' ], 10, 2 );
		add_filter( 'wp_get_object_terms', [ &$this, 'custom_order_get_object_terms' ] );
		add_filter( 'get_terms', [ &$this, 'custom_order_get_object_terms' ] );
	}

	// ------------------------------------------------------

	/**
	 * @param $where
	 *
	 * @return array|mixed|string|string[]|null
	 */
	public function custom_order_previous_post_where( $where ): mixed {
		global $post;

		$objects = $this->order_post_type;
		if ( empty( $objects ) ) {
			return $where;
		}

		if ( isset( $post->post_type ) && in_array( $post->post_type, $objects ) ) {
			$where = preg_replace( "/p.post_date < \'[0-9\-\s\:]+\'/i", "p.menu_order > '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	// ------------------------------------------------------

	/**
	 * @param $where
	 *
	 * @return array|mixed|string|string[]|null
	 */
	public function custom_order_next_post_where( $where ): mixed {
		global $post;

		$objects = $this->order_post_type;
		if ( empty( $objects ) ) {
			return $where;
		}

		if ( isset( $post->post_type ) && in_array( $post->post_type, $objects ) ) {
			$where = preg_replace( "/p.post_date > \'[0-9\-\s\:]+\'/i", "p.menu_order < '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	// ------------------------------------------------------

	/**
	 * @param $orderby
	 *
	 * @return mixed|string
	 */
	public function custom_order_previous_post_sort( $orderby ): mixed {
		global $post;

		$objects = $this->order_post_type;
		if ( empty( $objects ) ) {
			return $orderby;
		}

		if ( isset( $post->post_type ) && in_array( $post->post_type, $objects ) ) {
			$orderby = 'ORDER BY p.menu_order ASC LIMIT 1';
		}

		return $orderby;
	}

	// ------------------------------------------------------

	/**
	 * @param $orderby
	 *
	 * @return mixed|string
	 */
	public function custom_order_next_post_sort( $orderby ): mixed {
		global $post;

		$objects = $this->order_post_type;
		if ( empty( $objects ) ) {
			return $orderby;
		}

		if ( isset( $post->post_type ) && in_array( $post->post_type, $objects ) ) {
			$orderby = 'ORDER BY p.menu_order DESC LIMIT 1';
		}

		return $orderby;
	}

	// ------------------------------------------------------

	/**
	 * @param $hook_suffix - The current admin page.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ): void {
		if ( $this->_check_custom_order_script() ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}

	// ------------------------------------------------------

	/**
	 * @param $wp_query
	 *
	 * @return false|void
	 */
	public function custom_order_pre_get_posts( $wp_query ) {
		$objects = $this->order_post_type;

		if ( empty( $objects ) ) {
			return false;
		}

		if ( is_admin() && ! wp_doing_ajax() ) {

			if ( isset( $wp_query->query['post_type'] ) && ! isset( $_GET['orderby'] ) ) {
				if ( in_array( $wp_query->query['post_type'], $objects ) ) {
					if ( ! $wp_query->get( 'orderby' ) ) {
						$wp_query->set( 'orderby', 'menu_order' );
					}
					if ( ! $wp_query->get( 'order' ) ) {
						$wp_query->set( 'order', 'ASC' );
					}
				}
			}

		} else {

			$active = false;

			if ( isset( $wp_query->query['post_type'] ) ) {
				if ( ! is_array( $wp_query->query['post_type'] ) ) {
					if ( in_array( $wp_query->query['post_type'], $objects ) ) {
						$active = true;
					}
				}
			} else {
				if ( in_array( 'post', $objects ) ) {
					$active = true;
				}
			}

			if ( ! $active ) {
				return false;
			}

			if ( isset( $wp_query->query['suppress_filters'] ) ) {
				if ( $wp_query->get( 'orderby' ) == 'date' ) {
					$wp_query->set( 'orderby', 'menu_order' );
				}
				if ( $wp_query->get( 'order' ) == 'DESC' ) {
					$wp_query->set( 'order', 'ASC' );
				}
			} else {
				if ( ! $wp_query->get( 'orderby' ) ) {
					$wp_query->set( 'orderby', 'menu_order' );
				}
				if ( ! $wp_query->get( 'order' ) ) {
					$wp_query->set( 'order', 'ASC' );
				}
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @param $orderby
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public function custom_order_get_terms_orderby( $orderby, $args ): mixed {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $orderby;
		}

		if ( ! isset( $args['taxonomy'] ) ) {
			return $orderby;
		}

		$tags = $this->order_taxonomy;

		if ( is_array( $args['taxonomy'] ) ) {
			$taxonomy = $args['taxonomy'][0] ?? false;
		} else {
			$taxonomy = $args['taxonomy'];
		}

		if ( ! in_array( $taxonomy, $tags ) ) {
			return $orderby;
		}

		return 't.term_order';
	}

	// ------------------------------------------------------

	/**
	 * @param $terms
	 *
	 * @return mixed|void
	 */
	public function custom_order_get_object_terms( $terms ) {
		if ( is_admin() && ! wp_doing_ajax() && isset( $_GET['orderby'] ) ) {
			return $terms;
		}

		$tags = $this->order_taxonomy;

		foreach ( $terms as $key => $term ) {
			if ( is_object( $term ) && isset( $term->taxonomy ) ) {
				$taxonomy = $term->taxonomy;
				if ( ! in_array( $taxonomy, $tags, true ) ) {
					return $terms;
				}
			} else {
				return $terms;
			}
		}

		if ( is_array( $terms ) ) {
			usort( $terms, [ &$this, 'taxonomy_cmp' ] );
		}

		return $terms;
	}

	// ------------------------------------------------------

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function taxonomy_cmp( $a, $b ): int {
		if ( $a->term_order == $b->term_order ) {
			return 0;
		}

		return ( $a->term_order < $b->term_order ) ? - 1 : 1;
	}

	// ------------------------------------------------------

	/**
	 * @return bool
	 */
	private function _check_custom_order_script(): bool {
		$active = false;

		if ( empty( $this->order_post_type ) && empty( $this->order_taxonomy ) ) {
			return false;
		}

		if ( isset( $_GET['orderby'] ) || strstr( $_SERVER['REQUEST_URI'], 'action=edit' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) ) {
			return false;
		}

		if ( ! empty( $this->order_post_type ) ) {
			if ( isset( $_GET['post_type'] ) && ! isset( $_GET['taxonomy'] ) && in_array( $_GET['post_type'], $this->order_post_type ) ) {
				$active = true;
			}
			if ( ! isset( $_GET['post_type'] ) && strstr( $_SERVER['REQUEST_URI'], 'wp-admin/edit.php' ) && in_array( 'post', $this->order_post_type ) ) {
				$active = true;
			}
		}

		if ( ! empty( $this->order_taxonomy ) ) {
			if ( isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], $this->order_taxonomy ) ) {
				$active = true;
			}
		}

		return $active;
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function reset_all(): void {
		global $wpdb;

		// posts
		if ( ! empty( $this->order_post_type ) ) {

			$in_list     = implode( ',', array_map( fn( $value ) => $wpdb->prepare( '%s', $value ), $this->order_post_type ) );
			$status_cond = sprintf( '`post_type` IN (%s)', $in_list );

			$wpdb->query( "UPDATE {$wpdb->posts} SET `menu_order` = 0 WHERE {$status_cond}" );
		}

		// taxonomy
		if ( ! empty( $this->order_taxonomy ) ) {
			$prep_taxonomy_query = $wpdb->prepare( "UPDATE {$wpdb->terms} SET `term_order` = 0" );
			$wpdb->query( $prep_taxonomy_query );
		}

		// reset
		$custom_order_options = [
			'order_post_type' => [],
			'order_taxonomy'  => [],
		];

		Helper::updateOption( 'custom_order__options', $custom_order_options );
	}
}
