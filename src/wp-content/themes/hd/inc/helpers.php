<?php
/**
 * helpers functions
 *
 * @author WEBHD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// --------------------------------------------------

if ( ! function_exists( 'toggle_container' ) ) {
	/**
	 * @param bool $check
	 * @param string $css1
	 * @param string $css2
	 *
	 * @return void
	 */
	function toggle_container( bool $check, string $css1 = 'container', string $css2 = 'container fluid' ): void {
		$values = '';

		if ( $check && ! empty( $css1 ) ) {
			$values = '<div class="' . $css1 . '">';
		} else if ( ! $check && ! empty( $css2 ) ) {
			$values = '<div class="' . $css2 . '">';
		}

		echo $values;
	}
}

// --------------------------------------------------

if ( ! function_exists( 'check_smtp_plugin_active' ) ) {
	/**
	 * @return bool
	 */
	function check_smtp_plugin_active(): bool {
		$smtp_plugins_support = Helper::filter_setting_options( 'smtp_plugins_support', [] );

		$check = true;
		if ( ! empty( $smtp_plugins_support ) ) {
			foreach ( $smtp_plugins_support as $plugin_slug ) {
				if ( Helper::check_plugin_active( $plugin_slug ) ) {
					$check = false;
					break;
				}
			}
		}

		return $check;
	}
}

// --------------------------------------------------

if ( ! function_exists( 'in_array_checked' ) ) {
	/**
	 * @param array $checked_arr
	 * @param $current
	 * @param bool $display
	 * @param string $type
	 *
	 * @return string
	 */
	function in_array_checked( array $checked_arr, $current, bool $display = true, string $type = 'checked' ): string {
		if ( in_array( $current, $checked_arr, false ) ) {
			$result = " $type='$type'";
		} else {
			$result = '';
		}

		if ( $display ) {
			echo $result;
		}

		return $result;
	}
}

// --------------------------------------------------

if ( ! function_exists( 'sanitize_checkbox' ) ) {

	/**
	 * Sanitize checkbox values.
	 *
	 * @param $checked
	 *
	 * @return bool
	 */
	function sanitize_checkbox( $checked ): bool {
		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentionally loose.
		return isset( $checked ) && true === $checked;
	}
}

// --------------------------------------------------

if ( ! function_exists( 'sanitize_image' ) ) {

	/**
	 * @param $file
	 * @param $setting - WP_Customize_Image_Control
	 *
	 * @return mixed
	 */
	function sanitize_image( $file, $setting ): mixed {
		$mimes = [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'webp'         => 'image/webp',
			'tif|tiff'     => 'image/tiff',
			'ico'          => 'image/x-icon',
			'svg'          => 'image/svg+xml',
		];

		//check a file type from file name
		$file_ext = wp_check_filetype( $file, $mimes );

		// if a file has a valid mime type return it, otherwise return default
		return ( $file_ext['ext'] ? $file : $setting->default );
	}
}

// --------------------------------------------------

if ( ! function_exists( 'set_posts_per_page' ) ) {
	/**
	 * @param int $post_limit
	 *
	 * @return void
	 */
	function set_posts_per_page( int $post_limit = 12 ): void {
		if ( ! is_admin() && ! is_main_query() ) {
			$limit_default = Helper::getOption( 'posts_per_page' );

			if ( $post_limit > (int) $limit_default ) {
				add_action( 'pre_get_posts', function ( $query ) use ( $post_limit ) {
					$query->set( 'posts_per_page', $post_limit );
				}, 9999 );
			}
		}
	}
}

// --------------------------------------------------

if ( ! function_exists( 'the_paginate_links' ) ) {
	/**
	 * @param null $query
	 * @param bool $get
	 * @param bool $echo
	 *
	 * @return string|null
	 */
	function the_paginate_links( $query = null, bool $get = false, bool $echo = true ): ?string {
		if ( ! $query ) {
			global $wp_query;
		} else {
			$wp_query = $query;
		}

		if ( $wp_query->max_num_pages > 1 ) {

			// Setting up default values based on the current URL.
			$pagenum_link = html_entity_decode( get_pagenum_link() );
			$url_parts    = explode( '?', $pagenum_link, 2 );

			// Append the format placeholder to the base URL.
			$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

			$current = max( 1, get_query_var( 'paged' ) );
			$base    = $pagenum_link;

			if ( $get ) {
				$base = add_query_arg( 'page', '%#%' );
			}

			if ( ! empty( $_GET['page'] ) && $get ) {
				$current = (int) $_GET['page'];
			}

			// For more options and info view the docs for paginate_links()
			// http://codex.wordpress.org/Function_Reference/paginate_links
			$paginate_links = paginate_links(
				apply_filters(
					'wp_pagination_args',
					[
						'base'      => $base,
						'current'   => $current,
						'total'     => $wp_query->max_num_pages,
						'end_size'  => 1,
						'mid_size'  => 2,
						'prev_next' => true,
						'prev_text' => '<i data-glyph=""></i>',
						'next_text' => '<i data-glyph=""></i>',
						'type'      => 'list',
					]
				)
			);

			$paginate_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination">', $paginate_links );
			$paginate_links = str_replace( '<li><span class="page-numbers dots">&hellip;</span></li>', '<li class="ellipsis"></li>', $paginate_links );
			$paginate_links = str_replace( '<li><span aria-current="page" class="page-numbers current">', '<li class="current"><span aria-current="page" class="sr-only">You\'re on page </span>', $paginate_links );
			$paginate_links = str_replace( '</span></li>', '</li>', $paginate_links );
			$paginate_links = preg_replace( '/\s*page-numbers\s*/', '', $paginate_links );
			$paginate_links = preg_replace( '/\s*class=""/', '', $paginate_links );

			// Display the pagination if more than one page is found.
			if ( $paginate_links ) {
				$paginate_links = '<nav aria-label="Pagination">' . $paginate_links . '</nav>';
				if ( $echo ) {
					echo $paginate_links;
				} else {
					return $paginate_links;
				}
			}
		}

		return null;
	}
}

// --------------------------------------------------

if ( ! function_exists( 'the_post_comment' ) ) {

	/**
	 * @param mixed|null $id The ID, to load a single record;
	 */
	function the_post_comment( mixed $id = null ): void {
		if ( ! $id ) {
			if ( 'product' === get_post_type() ) {
				global $product;
				$id = $product->get_id();
			} else {
				$id = get_post()->ID;
			}
		}

		/*
		 * If the current post is protected by a password and
		 * the visitor has not yet entered the password we will
		 * return early without loading the comments.
		*/
		if ( post_password_required( $id ) ) {
			return;
		}

		$wrapper_open  = '<section id="comments-section" class="section comments-section comments-wrapper">';
		$wrapper_close = '</section>';

		//...
		$facebook_comment = false;
		$zalo_comment     = false;

		if ( Helper::is_acf_active() ) {
			$facebook_comment = \get_field( 'facebook_comment', $id ) ?? false;
			$zalo_comment     = \get_field( 'zalo_comment', $id ) ?? false;
		}

		if ( $facebook_comment || $zalo_comment || comments_open() ) {
			echo $wrapper_open;
			if ( comments_open() ) {
				//if ( ( class_exists( '\WooCommerce' ) && 'product' != $post_type ) || ! class_exists( '\WooCommerce' ) ) {
				comments_template();
				//}
			}
			if ( $facebook_comment ) {
				get_template_part( 'template-parts/comments/facebook' );
			}
			if ( $zalo_comment ) {
				get_template_part( 'template-parts/comments/zalo' );
			}

			echo $wrapper_close;
		}
	}
}
