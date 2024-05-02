<?php

namespace Addons\Base_Slug;

\defined( 'ABSPATH' ) || die;

class Rewrite_PostType {
	private mixed $base_slug_post_type;

	public function __construct() {
		$custom_base_slug_options  = get_option( 'custom_base_slug__options', [] );
		$this->base_slug_post_type = $custom_base_slug_options['base_slug_post_type'] ?? [];
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function run(): void {
		if ( ! empty( $this->base_slug_post_type ) ) {

			add_filter( 'post_type_link', [ &$this, 'post_type_link' ], 10, 2 ); // remove base slug from URLs

			if ( ! is_admin() ) {
				add_action( 'wp', [ &$this, 'redirect' ] ); // auto redirect old URLs to non-base versions
				add_action( 'request', [ &$this, 'request' ], 11, 1 ); // Permalink Manager.
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @param $permalink
	 * @param $post
	 *
	 * @return array|string|string[]|void
	 */
	public function post_type_link( $permalink, $post ) {
		global $wp_post_types;

		foreach ( $wp_post_types as $type => $custom_post ) {
			if ( $type !== $post->post_type || ! get_option( 'permalink_structure' ) ) {
				continue;
			}

			if ( $custom_post->_builtin === false &&
			     $custom_post->public === true &&
			     $custom_post->show_ui === true &&
			     in_array( $custom_post->name, $this->base_slug_post_type )
			) {
				// woocommerce
				if ( 'product' === $post->post_type &&
				     check_plugin_active( 'woocommerce/woocommerce.php' )
				) {
					return str_replace( $this->_get_product_base(), '/', $permalink );
				}

				return str_replace( '/' . trim( $custom_post->rewrite['slug'], '/' ) . '/', '/', $permalink );
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function redirect(): void {
		global $post;

		if ( ! is_preview() &&
		     ! is_admin() &&
		     is_single() &&
		     is_object( $post ) &&
		     in_array( $post->post_type, $this->base_slug_post_type )
		) {
			$new_url  = get_permalink();
			$real_url = get_current_url();

			if ( substr_count( $new_url, '/' ) != substr_count( $real_url, '/' ) &&
			     ! strstr( $real_url, $new_url )
			) {
				remove_filter( 'post_type_link', [ &$this, 'post_type_link' ], 10 );
				$old_url = get_permalink();

				add_filter( 'post_type_link', [ &$this, 'post_type_link' ], 10, 3 );
				$fixed_url = str_replace( $old_url, $new_url, $real_url );

				wp_safe_redirect( $fixed_url, 301 );
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @param $request
	 *
	 * @return array|mixed
	 */
	public function request( $request ): mixed {
		global $wp;
		$url_request = $wp->request;

		if ( empty( $url_request ) || is_admin() ) {
			return $request;
		}

		$replace   = [];
		$url_parts = explode( '/', $url_request );
		$slug      = end( $url_parts );

		if ( 'feed' === $slug || 'amp' === $slug ) {
			$replace[ $slug ] = $slug;
		}

		if ( str_starts_with( $slug, 'comment-page-' ) ) {
			$replace['cpage'] = substr( $slug, strlen( 'comment-page-' ) );
		}

		if ( str_starts_with( $slug, 'schema-preview' ) ) {
			$replace['schema-preview'] = '';
		}

		// test for posts and pages
		$post_data = get_page_by_path( $url_request, OBJECT, 'post' );
		if ( ! ( $post_data instanceof \WP_Post ) &&
		     ! is_object( get_page_by_path( $url_request ) )
		) {
			$post_data = get_page_by_path( $url_request, OBJECT, $this->base_slug_post_type );
			if ( is_object( $post_data ) ) {
				$post_name = $post_data->post_name;
				$ancestors = get_post_ancestors( $post_data->ID );

				foreach ( $ancestors as $ancestor ) {
					if ( ! empty( $ancestor ) ) {
						$post_name = get_post_field( 'post_name', $ancestor ) . '/' . $post_name;
					}
				}

				$replace['page']                  = '';
				$replace['name']                  = $post_name;
				$replace['post_type']             = $post_data->post_type;
				$replace[ $post_data->post_type ] = $post_name;

				return $replace;

			} else {
				// extra
				//...
			}
		}

		return $request;
	}

	// ------------------------------------------------------

	/**
	 * Get product base.
	 *
	 * @return string
	 */
	private function _get_product_base(): string {

		$permalink_structure = wc_get_permalink_structure();
		$product_base        = $permalink_structure['product_rewrite_slug'];

		if ( str_contains( $product_base, '%product_cat%' ) ) {
			$product_base = str_replace( '%product_cat%', '', $product_base );
		}

		return '/' . trim( $product_base, '/' ) . '/';
	}
}
