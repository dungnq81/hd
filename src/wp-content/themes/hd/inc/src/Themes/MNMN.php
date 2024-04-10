<?php

namespace Themes;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

final class MNMN {
	public function __construct() {

        // hide admin-bar default
        add_action( "user_register", [ &$this, 'user_register' ], 10, 1 );

        // filter post search only by title
        add_filter( 'posts_search', [ &$this, 'post_search_by_title' ], 500, 2 );

		// if not admin page
		if ( ! is_admin() ) {
			add_action('pre_get_posts', [&$this, '__set_posts_per_page']);
		}

        //////////////////////////////////////

        add_filter('nav_menu_css_class', function ( $classes, $menu_item, $args, $depth ) {

			// top level
	        if ($depth == 0) {
		        if (isset($args->li_class)) {
			        $classes[] = $args->li_class;
		        }
	        } else {
		        if (isset($args->li_class_depth)) {
			        $classes[] = $args->li_class_depth;
		        }
	        }

            return $classes;

        }, 1, 4);

        //////////////////////////////////////

        // add class to link in wp_nav_menu
        add_filter('nav_menu_link_attributes', function ( $atts, $menu_item, $args, $depth ) {

	        // top level
	        if ($depth == 0) {
		        if (property_exists($args, 'link_class')) {
			        $atts['class'] = $args->link_class;
		        }
	        } else {
		        $atts['class'] = $args->link_class_depth;
	        }

            return $atts;
        }, 1, 4);
	}

    /***************************************************/

	/**
	 * @param $query
	 */
	public function __set_posts_per_page( $query ): void {
		if ( ! is_admin() && !is_post_type_archive('lich-phat-song')) {

			// get default value
			$posts_per_page = get_option( 'posts_per_page' );

			$hd_posts_num_per_page_arr = apply_filters( 'hd_posts_num_per_page', [ 12, 24, 36 ] );
			if ( isset( $_GET['pagenum'] ) ) {

				$pagenum = esc_sql( $_GET['pagenum'] );
				if ( in_array( $pagenum, $hd_posts_num_per_page_arr ) ) {
					$posts_per_page = $pagenum;
				}

				if ( $pagenum > max( $hd_posts_num_per_page_arr ) ) {
					$posts_per_page = max( $hd_posts_num_per_page_arr );
				}
			}

			$query->set( 'posts_per_page', $posts_per_page );

		}
	}

    // ------------------------------------------------------

    /**
     * Search only in post title or excerpt
     *
     * @param $search
     * @param $wp_query
     *
     * @return mixed|string
     */
    public function post_search_by_title( $search, $wp_query ): mixed {
        global $wpdb;

        if ( empty( $search ) ) {
            return $search; // skip processing – no search term in query
        }

        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';

        $search = $search_and = '';

        $search_terms = Helper::toArray( $q['search_terms'] );
        foreach ( $search_terms as $term ) {
            $term       = esc_sql( $wpdb->esc_like( $term ) );
            $term = mb_strtolower( $term );

            $like = "LIKE CONCAT('{$n}', CONVERT('{$term}', BINARY), '{$n}')";
            $like_first = "LIKE CONCAT(CONVERT('{$term}', BINARY), '{$n}')";
            $like_last = "LIKE CONCAT('{$n}', CONVERT('{$term}', BINARY))";

            $search     .= "{$search_and}(LOWER($wpdb->posts.post_title) {$like} OR LOWER($wpdb->posts.post_title) {$like_first} OR LOWER($wpdb->posts.post_title) {$like_last} OR LOWER($wpdb->posts.post_excerpt) {$like} OR LOWER($wpdb->posts.post_excerpt) {$like_first} OR LOWER($wpdb->posts.post_excerpt) {$like_last})";
            $search_and = " AND ";
        }

        if ( ! empty( $search ) ) {
            $search = " AND ({$search}) ";
            if ( ! is_user_logged_in() ) {
                $search .= " AND ($wpdb->posts.post_password = '') ";
            }
        }

        return $search;
    }

    // ------------------------------------------------------

    /**
     * @param $user_id
     *
     * @return void
     */
    public function user_register( $user_id ): void {
        update_user_meta( $user_id, 'show_admin_bar_front', false );
        update_user_meta( $user_id, 'show_admin_bar_admin', false );
    }

	// ------------------------------------------------------
}
