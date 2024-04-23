<?php

namespace Cores\Traits;

use Cores\Helper;
use Libs\CSS;
use Libs\Horizontal_Nav_Walker;
use Libs\Vertical_Nav_Walker;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_Term;

\defined( 'ABSPATH' ) || die;

trait Wp {
	use Arr;
	use Base;
	use Cast;
	use File;
	use Str;
	use Url;

	// -------------------------------------------------------------

	/**
	 * @param mixed $action
	 * @param string $name
	 * @param bool $referer
	 * @param bool $display
	 *
	 * @return string
	 */
	public static function csrf_token( mixed $action = - 1, string $name = '_csrf_token', bool $referer = false, bool $display = true ): string {
		$name        = esc_attr( $name );
		$nonce_field = '<input type="hidden" id="' . Helper::random( 9 ) . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';

		if ( $referer ) {
			$nonce_field .= wp_referer_field( false );
		}

		if ( $display ) {
			echo $nonce_field;
		}

		return $nonce_field;
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_admin(): bool {
		return is_admin();
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_login(): bool {
		return in_array( $GLOBALS['pagenow'], [ 'wp-login.php', 'wp-register.php' ] );
	}

	// -------------------------------------------------------------

	/**
	 * @param array $args
	 *
	 * @return bool|false|string|void
	 */
	public static function verticalNav( array $args = [] ) {
		$args = wp_parse_args(
			(array) $args,
			[
				'container'      => false, // Remove nav container
				'menu_id'        => '',
				'menu_class'     => 'menu vertical',
				'theme_location' => '',
				'depth'          => 4,
				'fallback_cb'    => false,
				'walker'         => new Vertical_Nav_Walker(),
				'items_wrap'     => '<ul id="%1$s" class="%2$s" data-accordion-menu data-submenu-toggle="true">%3$s</ul>',
				'echo'           => false,
			]
		);

		if ( true === $args['echo'] ) {
			echo wp_nav_menu( $args );
		} else {
			return wp_nav_menu( $args );
		}
	}

	// -------------------------------------------------------------

	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param array $args
	 *
	 * @return bool|false|string|void
	 */
	public static function horizontalNav( array $args = [] ) {
		$args = wp_parse_args(
			(array) $args,
			[
				'container'      => false,
				'menu_id'        => '',
				'menu_class'     => 'dropdown menu horizontal',
				'theme_location' => '',
				'depth'          => 4,
				'fallback_cb'    => false,
				'walker'         => new Horizontal_Nav_Walker(),
				'items_wrap'     => '<ul id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
				'echo'           => false,
			]
		);

		if ( true === $args['echo'] ) {
			echo wp_nav_menu( $args );
		} else {
			return wp_nav_menu( $args );
		}
	}

	// -------------------------------------------------------------

	/**
	 * Call a shortcode function by tag name.
	 *
	 * @param string $tag The shortcode whose function to call.
	 * @param array $atts The attributes to pass to the shortcode function. Optional.
	 * @param array|null $content The shortcode's content. Default is null (none).
	 *
	 * @return false|mixed False on failure, the result of the shortcode on success.
	 */
	public static function doShortcode( string $tag, array $atts = [], $content = null ): mixed {
		global $shortcode_tags;
		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}

	// -------------------------------------------------------------

	/**
	 * Using `rawurlencode` on any variable used as part of the query string, either by using
	 * `add_query_arg()` or directly by string concatenation will prevent parameter hijacking.
	 *
	 * @param $url
	 * @param $args
	 *
	 * @return string
	 */
	public static function addQueryArg( $url, $args ): string {
		$args = array_map( 'rawurlencode', $args );

		return add_query_arg( $args, $url );
	}

	// -------------------------------------------------------------

	/**
	 * @param      $attachment_id
	 * @param bool $return_object
	 *
	 * @return array|object|null
	 */
	public static function getAttachment( $attachment_id, bool $return_object = true ): object|array|null {
		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) {
			return null;
		}

		$_return = [
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href'        => get_permalink( $attachment->ID ),
			'src'         => $attachment->guid,
			'title'       => $attachment->post_title,
		];

		if ( true === $return_object ) {
			return self::toObject( $_return );
		}

		return $_return;
	}

	// -------------------------------------------------------------

	/**
	 * @param array $arr_parsed [ $handle: $value ] -- $value[ 'defer', 'delay' ]
	 * @param string $tag
	 * @param string $handle
	 * @param string $src
	 *
	 * @return array|string|string[]|null
	 */
	public static function lazyScriptTag( array $arr_parsed, string $tag, string $handle, string $src ): array|string|null {
		foreach ( $arr_parsed as $str => $value ) {
			if ( str_contains( $handle, $str ) ) {
				if ( 'defer' === $value ) {
					$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );

					return preg_replace( '/\s+src=/', ' defer src=', $tag );
				} elseif ( 'delay' === $value ) {
					$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );

					return preg_replace( '/\s+src=/', ' defer data-type=\'lazy\' data-src=', $tag );
				}
			}
		}

		return $tag;
	}

	// -------------------------------------------------------------

	/**
	 * @param array $arr_styles
	 * @param string $html
	 * @param string $handle
	 *
	 * @return array|string|string[]|null
	 */
	public static function lazyStyleTag( array $arr_styles, string $html, string $handle ): array|string|null {
		foreach ( $arr_styles as $style ) {
			if ( str_contains( $handle, $style ) ) {
				return preg_replace( '/media=\'all\'/', 'media=\'print\' onload=\'this.media="all"\'', $html );
			}
		}

		return $html;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $option_name
	 * @param $new_options
	 * @param bool $merge_arr
	 *
	 * @return bool
	 */
	public static function updateOption( string $option_name, $new_options, bool $merge_arr = false ): bool {
		if ( true === $merge_arr ) {
			$options = self::getOption( $option_name );
			if ( is_array( $options ) && is_array( $new_options ) ) {
				$updated_options = array_merge( $options, $new_options );
			} else {
				$updated_options = $new_options;
			}
		} else {
			$updated_options = $new_options;
		}

		return false === is_multisite() ? update_option( $option_name, $updated_options ) : update_site_option( $option_name, $updated_options );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $option
	 * @param mixed $default
	 * @param bool $static_cache
	 *
	 * @return false|mixed
	 */
	public static function getOption( string $option, $default = false, bool $static_cache = false ): mixed {
		static $_is_option_loaded;
		if ( empty( $_is_option_loaded ) ) {

			// references cannot be directly assigned to static variables, so we use an array
			$_is_option_loaded[0] = [];
		}

		if ( $option ) {
			$_value = false === is_multisite() ? get_option( $option, $default ) : get_site_option( $option, $default );

			if ( true === $static_cache ) {
				if ( ! isset( $_is_option_loaded[0][ strtolower( $option ) ] ) ) {
					$_is_option_loaded[0][ strtolower( $option ) ] = $_value;
				}
			} else {
				$_is_option_loaded[0][ strtolower( $option ) ] = $_value;
			}

			return $_is_option_loaded[0][ strtolower( $option ) ];
		}

		return false;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $mod_name
	 * @param false|mixed $default
	 *
	 * @return false|mixed
	 */
	public static function getThemeMod( string $mod_name, $default = false ): mixed {
		static $_is_loaded;
		if ( empty( $_is_loaded ) ) {

			// references cannot be directly assigned to static variables, so we use an array
			$_is_loaded[0] = [];
		}

		if ( $mod_name ) {
			if ( ! isset( $_is_loaded[0][ strtolower( $mod_name ) ] ) ) {
				$_mod = get_theme_mod( $mod_name, $default );
				if ( is_ssl() ) {
					$_is_loaded[0][ strtolower( $mod_name ) ] = str_replace( [ 'http://' ], 'https://', $_mod );
				} else {
					$_is_loaded[0][ strtolower( $mod_name ) ] = $_mod;
				}
			}

			return $_is_loaded[0][ strtolower( $mod_name ) ];
		}

		return $default;
	}

	// -------------------------------------------------------------

	/**
	 * @param        $term_id
	 * @param string $taxonomy
	 *
	 * @return array|false|WP_Error|WP_Term|null
	 */
	public static function getTerm( $term_id, string $taxonomy = 'category' ) {
		//$term = false;
		if ( is_numeric( $term_id ) ) {
			$term_id = intval( $term_id );
			$term    = get_term( $term_id );
		} else {
			$term = get_term_by( 'slug', $term_id, $taxonomy );
			if ( ! $term ) {
				$term = get_term_by( 'name', $term_id, $taxonomy );
			}
		}

		return $term;
	}

	// -------------------------------------------------------------

	/**
	 * @param             $term
	 * @param string $post_type
	 * @param bool $include_children
	 *
	 * @param int $posts_per_page
	 * @param array $orderby
	 * @param array $meta_query
	 * @param bool|string $strtotime_recent - strtotime('last week');
	 *
	 * @return bool|WP_Query
	 */
	public static function queryByTerm(
		$term,
		string $post_type = 'post',
		bool $include_children = false,
		int $posts_per_page = 12,
		array $orderby = [ 'date' => 'DESC' ],
		array $meta_query = [],
		bool|string $strtotime_recent = false ): WP_Query|bool
	{
		if ( ! $term ) {
			return false;
		}

		$_args = [
			'post_type'              => $post_type ?: 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page ?: 10,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			//'update_post_meta_cache' => false,
			//'update_post_term_cache' => false,
			'tax_query'              => [ 'relation' => 'AND' ],
		];

		// term
		if ( ! is_object( $term ) ) {
			$term = self::toObject( $term );
		}

		if ( isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			$_args['tax_query'][] = [
				'taxonomy'         => $term->taxonomy,
				'terms'            => [ $term->term_id ],
				'include_children' => (bool) $include_children,
				'operator'         => 'IN',
			];
		}

		// 'orderby' => [ 'date' => 'DESC', 'menu_order' => 'DESC' ]
		if ( is_array( $orderby ) && ! empty( $orderby ) ) {
			$orderby = self::removeEmptyValues( $orderby );
		} else {
			$orderby = 'rand';
		}

		$_args['orderby'] = $orderby;

		// meta_query
		if ( ! empty( $meta_query ) ) {
			$_args = array_merge( $_args, $meta_query );
		}

		// date_query
		if ( $strtotime_recent ) {

			// constrain to just posts in $strtotime_recent
			$recent = strtotime( $strtotime_recent );
			if ( self::isInteger( $recent ) ) {
				$_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === self::getOption( 'woocommerce_hide_out_of_stock_items', false, true ) &&
		     self::is_woocommerce_active() &&
		     'product' == $post_type
		) {
			$product_visibility_term_ids = \wc_get_product_visibility_term_ids();
			$_args['tax_query'][] = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				],
			]; // WPCS: slow query ok.
		}

		$_query = new WP_Query( $_args );
		if ( ! $_query->have_posts() ) {
			return false;
		}

		return $_query;
	}

	// -------------------------------------------------------------

	/**
	 * @param array|string $term_ids
	 * @param string $post_type
	 * @param string $taxonomy
	 * @param bool $include_children
	 * @param int $posts_per_page
	 * @param array $orderby
	 * @param array $meta_query
	 * @param bool|string $strtotime_str
	 *
	 * @return false|WP_Query
	 */
	public static function queryByTerms(
		array|string $term_ids,
		string $post_type = 'post',
		string $taxonomy = 'category',
		bool $include_children = false,
		int $posts_per_page = 12,
		array $orderby = [ 'date' => 'DESC' ],
		array $meta_query = [],
		bool|string $strtotime_str = false ): WP_Query|false
	{
		$_args = [
			'post_type'              => $post_type ?: 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page ?: 12,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			//'update_post_meta_cache' => false,
			//'update_post_term_cache' => false,
			'tax_query'              => [ 'relation' => 'AND' ],
		];

		if ( ! $taxonomy ) {
			$taxonomy = 'category';
		}

		// terms
		$term_ids = self::removeEmptyValues( $term_ids );
		if ( count( $term_ids ) > 0 ) {
			$_args['tax_query'][] = [
				'taxonomy'         => $taxonomy,
				'terms'            => $term_ids,
				'field'            => 'term_id',
				'include_children' => (bool) $include_children,
				'operator'         => 'IN',
			];
		}

		// 'orderby' => [ 'date' => 'DESC', 'menu_order' => 'DESC' ]
		if ( is_array( $orderby ) && ! empty( $orderby ) ) {
			$orderby = self::removeEmptyValues( $orderby );
		} else {
			$orderby = 'rand';
		}

		$_args['orderby'] = $orderby;

		// meta_query
		if ( ! empty( $meta_query ) ) {
			$_args = array_merge( $_args, $meta_query );
		}

		// date_query
		if ( $strtotime_str ) {

			// constrain to just posts in $strtotime_str
			$recent = strtotime( $strtotime_str );
			if ( self::isInteger( $recent ) ) {
				$_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === self::getOption( 'woocommerce_hide_out_of_stock_items', false, true ) &&
		     self::is_woocommerce_active() &&
		     'product' == $post_type
		) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$_args['tax_query'][] = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				],
			]; // WPCS: slow query ok.
		}

		// query
		$r = new WP_Query( $_args );
		if ( ! $r->have_posts() ) {
			return false;
		}

		return $r;
	}

	// -------------------------------------------------------------

	/**
	 * @param bool $echo
	 * @param string $home_heading
	 * @param string $class
	 *
	 * @return string|void
	 */
	public static function siteTitleOrLogo( bool $echo = true, string $home_heading = 'h1', string $class = 'logo' ) {
		$is_home_or_front_page = is_home() || is_front_page();
		$tag                   = $is_home_or_front_page ? $home_heading : 'div';

		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$logo = get_custom_logo();
			$html = '<div id="logo" class="' . $class . '">' . $logo . '</div>';
		} else {
			$html = '<div class="' . $class . '"><a title href="' . self::home() . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></div>';
			if ( '' !== get_bloginfo( 'description' ) ) {
				$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
			}
		}

		$logo_heading = self::getThemeMod( 'logo_title_setting' );
		if ( $logo_heading && $is_home_or_front_page ) {
			$html .= '<' . esc_attr( $tag ) . ' class="sr-only">' . $logo_heading . '</' . esc_attr( $tag ) . '>';
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// -------------------------------------------------------------

	/**
	 * @param string $theme - default|light|dark
	 * @param string|null $class
	 *
	 * @return string
	 */
	public static function siteLogo( string $theme = 'default', ?string $class = '' ): string {
		$html           = '';
		$custom_logo_id = null;

		if ( 'default' !== $theme && $theme_logo = self::getThemeMod( $theme . '_logo' ) ) {
			$custom_logo_id = attachment_url_to_postid( $theme_logo );
		} else if ( has_custom_logo() ) {
			$custom_logo_id = self::getThemeMod( 'custom_logo' );
		}

		// We have a logo. Logo is go.
		if ( $custom_logo_id ) {
			$custom_logo_attr = [
				'class'   => $theme . '-logo',
				'loading' => 'lazy',
			];

			/**
			 * If the logo alt attribute is empty, get the site title and explicitly pass it
			 * to the attributes used by wp_get_attachment_image().
			 */
			$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$image_alt = get_bloginfo( 'name', 'display' );
			}

			$custom_logo_attr['alt'] = $image_alt;

			/**
			 * If the alt attribute is not empty, there's no need to explicitly pass it
			 * because wp_get_attachment_image() already adds the alt attribute.
			 */
			$logo = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );
			if ( $class ) {
				$html = '<div class="' . $class . '"><a title="' . $image_alt . '" href="' . self::home() . '">' . $logo . '</a></div>';
			} else {
				$html = '<a title="' . $image_alt . '" href="' . self::home() . '">' . $logo . '</a>';
			}
		}

		return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// -------------------------------------------------------------

	/**
	 * @param        $post
	 * @param string $class
	 *
	 * @return string|null
	 */
	public static function loopExcerpt( $post = null, string $class = 'excerpt' ): ?string {
		$excerpt = get_the_excerpt( $post );
		if ( ! self::stripSpace( $excerpt ) ) {
			return null;
		}

		$excerpt = strip_tags( $excerpt );
		if ( ! $class ) {
			return $excerpt;
		}

		return "<p class=\"$class\">{$excerpt}</p>";
	}

	// -------------------------------------------------------------

	/**
	 * @param null $post
	 * @param string $class
	 * @param bool $glyph_icon
	 *
	 * @return string|null
	 */
	public static function postExcerpt( $post = null, string $class = 'excerpt', bool $glyph_icon = false ): ?string {
		$post = get_post( $post );
		if ( ! self::stripSpace( $post->post_excerpt ) ) {
			return null;
		}

		$open  = '';
		$close = '';
		$glyph = '';
		if ( true === $glyph_icon ) {
			$glyph = ' data-glyph=""';
		}
		if ( $class ) {
			$open  = '<div class="' . $class . '"' . $glyph . '>';
			$close = '</div>';
		}

		return $open . '<div>' . $post->post_excerpt . '</div>' . $close;
	}

	// -------------------------------------------------------------

	/**
	 * @param int $term
	 * @param string $class
	 *
	 * @return string|null
	 */
	public static function termExcerpt( $term = 0, string $class = 'excerpt' ): ?string {
		$description = term_description( $term );
		if ( ! self::stripSpace( $description ) ) {
			return null;
		}

		if ( ! $class ) {
			return $description;
		}

		return "<div class=\"$class\">$description</div>";
	}

	// -------------------------------------------------------------

	/**
	 * @param             $post
	 * @param string $taxonomy
	 *
	 * @return array|false|mixed|WP_Error|WP_Term
	 */
	public static function primaryTerm( $post, string $taxonomy = '' ): mixed {
		//$post = get_post( $post );
		//$ID   = $post->ID ?? null;

		if ( ! $taxonomy ) {
			$post_type = get_post_type( $post );

			if ( 'post' == $post_type ) {
				$taxonomy = 'category';
			}

			$hd_post_type_terms_arr = apply_filters( 'hd_post_type_terms', [] );
			if ( ! empty( $hd_post_type_terms_arr ) ) {
				foreach ( $hd_post_type_terms_arr as $_post_type => $_taxonomy ) {
					if ( $_post_type == $post_type ) {
						$taxonomy = $_taxonomy;
					}
				}
			}
		}

		// get list terms
		$post_terms = get_the_terms( $post, $taxonomy );
		$term_ids   = wp_list_pluck( $post_terms, 'term_id' );

		// Rank Math SEO
		// https://vi.wordpress.org/plugins/seo-by-rank-math/
		$primary_term_id = get_post_meta( get_the_ID(), 'rank_math_primary_' . $taxonomy, true );
		if ( $primary_term_id && in_array( $primary_term_id, $term_ids ) ) {
			$term = get_term( $primary_term_id, $taxonomy );
			if ( $term ) {
				return $term;
			}
		}

		// Yoast SEO
		// https://vi.wordpress.org/plugins/wordpress-seo/
		if ( class_exists( '\WPSEO_Primary_Term' ) ) {

			// Show the post's 'Primary' category if this Yoast feature is available, & one is set
			$wpseo_primary_term = new \WPSEO_Primary_Term( $taxonomy, $post );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			$term               = get_term( $wpseo_primary_term, $taxonomy );
			if ( $term && in_array( $term->term_id, $term_ids ) ) {
				return $term;
			}
		}

		//...

		// Default, first category
		if ( is_array( $post_terms ) ) {
			return $post_terms[0];
		}

		return false;
	}

	// -------------------------------------------------------------

	/**
	 * @param null $post
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string|null $wrapper_close
	 *
	 * @return string|null
	 */
	public static function getPrimaryTerm( $post = null, string $taxonomy = '', string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>' ): ?string {
		$term = self::primaryTerm( $post, $taxonomy );
		if ( ! $term ) {
			return null;
		}

		$link = '<a href="' . esc_url( get_term_link( $term, $taxonomy ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}

		return $link;
	}

	// -------------------------------------------------------------

	/**
	 * @param             $post
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string|null $wrapper_close
	 *
	 * @return false|string|null
	 */
	public static function postTerms( $post, string $taxonomy = 'category', string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>' ): false|string|null {
		if ( ! $taxonomy ) {
			$post_type = get_post_type( $post );
			$taxonomy  = $post_type . '_cat';

			if ( 'post' == $post_type ) {
				$taxonomy = 'category';
			}

			$hd_post_type_terms_arr = apply_filters( 'hd_post_type_terms', [] );
			if ( ! empty( $hd_post_type_terms_arr ) ) {
				foreach ( $hd_post_type_terms_arr as $_post_type => $_taxonomy ) {
					if ( $_post_type == $post_type ) {
						$taxonomy = $_taxonomy;
					}
				}
			}
		}

		$link       = '';
		$post_terms = get_the_terms( $post, $taxonomy );
		if ( empty( $post_terms ) ) {
			return false;
		}

		foreach ( $post_terms as $term ) {
			if ( $term->slug ) {
				$link .= '<a href="' . esc_url( get_term_link( $term ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
			}
		}

		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}

		return $link;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $taxonomy
	 * @param int $id
	 * @param string $sep
	 *
	 * @return void
	 */
	public static function hashTags( string $taxonomy = 'post_tag', int $id = 0, string $sep = '' ): void {
		if ( ! $taxonomy ) {
			$taxonomy = 'post_tag';
		}

		// Get Tags for posts.
		$hashtag_list = get_the_term_list( $id, $taxonomy, '', $sep );

		// We don't want to output .entry-footer if it will be empty, so make sure its not.
		if ( $hashtag_list ) {
			echo '<div class="hashtags">';
			printf(
			/* translators: 1: SVG icon. 2: posted in label, only visible to screen readers. 3: list of tags. */
				'<div class="hashtag-links links">%1$s<span class="sr-only">%2$s</span>%3$s</div>',
				'<i data-glyph="#"></i>',
				__( 'Tags', TEXT_DOMAIN ),
				$hashtag_list
			); // WPCS: XSS OK.

			echo '</div>';
		}
	}

	// -------------------------------------------------------------

	/**
	 * @param null $post
	 * @param string $size
	 *
	 * @return string|null
	 */
	public static function postImageSrc( $post = null, string $size = 'thumbnail' ): ?string {
		return get_the_post_thumbnail_url( $post, $size );
	}

	// -------------------------------------------------------------

	/**
	 *
	 * @param        $attachment_id
	 * @param string $size
	 *
	 * @return string|null
	 */
	public static function attachmentImageSrc( $attachment_id, string $size = 'thumbnail' ): ?string {
		return wp_get_attachment_image_url( $attachment_id, $size );
	}

	// -------------------------------------------------------------

	/**
	 * @param $attachment_id
	 * @param string $size
	 * @param string $attr
	 *
	 * @return string
	 */
	public static function iconImage( $attachment_id, string $size = 'thumbnail', $attr = '' ): string {

		$html  = '';
		$image = wp_get_attachment_image_src( $attachment_id, $size, true );

		if ( $image ) {
			[ $src, $width, $height ] = $image;
			$hwstring   = image_hwstring( $width, $height );

			$default_attr = array(
				'src'   => $src,
				'alt'   => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
			);

			$context = apply_filters( 'wp_get_attachment_image_context', 'wp_get_attachment_image' );
			$attr    = wp_parse_args( $attr, $default_attr );

			$loading_attr              = $attr;
			$loading_attr['width']     = $width;
			$loading_attr['height']    = $height;
			$loading_optimization_attr = wp_get_loading_optimization_attributes(
				'img',
				$loading_attr,
				$context
			);

			// Add loading optimization attributes if not available.
			$attr = array_merge( $attr, $loading_optimization_attr );

			// Omit the `decoding` attribute if the value is invalid according to the spec.
			if ( empty( $attr['decoding'] ) || ! in_array( $attr['decoding'], array( 'async', 'sync', 'auto' ), true ) ) {
				unset( $attr['decoding'] );
			}

			/*
			 * If the default value of `lazy` for the `loading` attribute is overridden
			 * to omit the attribute for this image, ensure it is not included.
			 */
			if ( isset( $attr['loading'] ) && ! $attr['loading'] ) {
				unset( $attr['loading'] );
			}

			// If the `fetchpriority` attribute is overridden and set to false or an empty string.
			if ( isset( $attr['fetchpriority'] ) && ! $attr['fetchpriority'] ) {
				unset( $attr['fetchpriority'] );
			}

			$attr = array_map( 'esc_attr', $attr );
			$html = rtrim( "<img $hwstring" );

			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}

			$html .= ' />';
		}

		return $html;
	}

	// -------------------------------------------------------------

	/**
	 * @param        $term
	 * @param null $acf_field_name
	 * @param string $size
	 * @param bool $img_wrap
	 * @param string $attr
	 *
	 * @return string|null
	 */
	public static function acfTermThumb( $term, $acf_field_name = null, string $size = "thumbnail", bool $img_wrap = false, $attr = '' ): ?string {
		if ( is_numeric( $term ) ) {
			$term = get_term( $term );
		}

		if ( class_exists( '\ACF' ) ) {
			$attach_id = \get_field( $acf_field_name, $term ) ?? false;
			if ( $attach_id ) {
				$img_src = wp_get_attachment_image_url( $attach_id, $size );
				if ( $img_wrap ) {
					$img_src = wp_get_attachment_image( $attach_id, $size, false, $attr );
				}

				return $img_src;
			}
		}

		return null;
	}

	// -------------------------------------------------------------

	/**
	 * @param $post
	 * @param $from
	 * @param $to
	 *
	 * @return mixed|void
	 */
	public static function humanizeTime( $post = null, $from = null, $to = null ) {
		$_ago = __( 'ago', TEXT_DOMAIN );

		if ( empty( $to ) ) {
			$to = current_time( 'U' );
		}
		if ( empty( $from ) ) {
			$from = get_the_time( 'U', $post );
		}

		$diff = (int) abs( $to - $from );

		$since = human_time_diff( $from, $to );
		$since = $since . ' ' . $_ago;

		return apply_filters( 'humanize_time', $since, $diff, $from, $to );
	}

	// -------------------------------------------------------------

	/**
	 * @return void
	 */
	public static function breadcrumbs(): void {
		global $post, $wp_query;

		$before = '<li class="current">';
		$after  = '</li>';

		if ( ! is_front_page() ) {

			echo '<ul id="breadcrumbs" class="breadcrumbs" aria-label="Breadcrumbs">';
			echo '<li><a class="home" href="' . self::home() . '">' . __( 'Home', TEXT_DOMAIN ) . '</a></li>';

			//...
			if ( self::is_woocommerce_active() && @is_shop() ) {
				$shop_page_title = get_the_title( self::getOption( 'woocommerce_shop_page_id' ) );
				echo $before . $shop_page_title . $after;
			} elseif ( $wp_query->is_posts_page ) {
				$posts_page_title = get_the_title( self::getOption( 'page_for_posts', true ) );
				echo $before . $posts_page_title . $after;
			} elseif ( $wp_query->is_post_type_archive ) {
				$posts_page_title = post_type_archive_title( '', false );
				echo $before . $posts_page_title . $after;
			} /** page, attachment */
			elseif ( is_page() || is_attachment() ) {

				// parent page
				if ( $post->post_parent ) {
					$parent_id   = $post->post_parent;
					$breadcrumbs = [];

					while ( $parent_id ) {
						$page          = get_post( $parent_id );
						$breadcrumbs[] = '<li><a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a></li>';
						$parent_id     = $page->post_parent;
					}

					$breadcrumbs = array_reverse( $breadcrumbs );
					foreach ( $breadcrumbs as $crumb ) {
						echo $crumb;
					}
				}

				echo $before . get_the_title() . $after;
			} /** single */
			elseif ( is_single() && ! is_attachment() ) {

				if ( ! in_array( get_post_type(), [ 'post', 'product', 'service', 'project' ] ) ) {
					$post_type = get_post_type_object( get_post_type() );
					$slug      = $post_type->rewrite;
					if ( ! is_bool( $slug ) ) {
						echo '<li><a href="' . self::home() . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
					}
				} else {
					$term = self::primaryTerm( $post );
					if ( $term ) {
						if ( $cat_code = get_term_parents_list( $term->term_id, $term->taxonomy, [ 'separator' => '' ] ) ) {
							$cat_code = str_replace( '<a', '<li><a', $cat_code );
							echo str_replace( '</a>', '</a></li>', $cat_code );
						}
					}
				}

				echo $before . get_the_title() . $after;
			} /** search page */
			elseif ( is_search() ) {
				echo $before;
				printf( __( 'Search Results for: %s', TEXT_DOMAIN ), get_search_query() );
				echo $after;
			} /** tag */
			elseif ( is_tag() ) {
				echo $before;
				printf( __( 'Tag Archives: %s', TEXT_DOMAIN ), single_tag_title( '', false ) );
				echo $after;
			} /** author */
			elseif ( is_author() ) {
				global $author;

				$userdata = get_userdata( $author );
				echo $before;
				echo $userdata->display_name;
				echo $after;
			} /** day, month, year */
			elseif ( is_day() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a></li>';
				echo $before . get_the_time( 'd' ) . $after;
			} elseif ( is_month() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo $before . get_the_time( 'F' ) . $after;
			} elseif ( is_year() ) {
				echo $before . get_the_time( 'Y' ) . $after;
			} /** category, tax */
			elseif ( is_category() || is_tax() ) {

				$cat_obj = $wp_query->get_queried_object();
				$thisCat = get_term( $cat_obj->term_id );

				if ( isset( $thisCat->parent ) && 0 != $thisCat->parent ) {
					$parentCat = get_term( $thisCat->parent );
					if ( $cat_code = get_term_parents_list( $parentCat->term_id, $parentCat->taxonomy, [ 'separator' => '' ] ) ) {
						$cat_code = str_replace( '<a', '<li><a', $cat_code );
						echo str_replace( '</a>', '</a></li>', $cat_code );
					}
				}

				echo $before . single_cat_title( '', false ) . $after;
			} /** 404 */
			elseif ( is_404() ) {
				echo $before;
				__( 'Not Found', TEXT_DOMAIN );
				echo $after;
			}

			//...
			if ( get_query_var( 'paged' ) ) {
				echo '<li class="paged">';
				echo ' (';
				echo __( 'page', TEXT_DOMAIN ) . ' ' . get_query_var( 'paged' );
				echo ')';
				echo $after;
			}

			echo '</ul>';
		}

		// reset
		wp_reset_query();
	}

	// -------------------------------------------------------------

	/**
	 * Get lang code
	 *
	 * @return string
	 */
	public static function getLang(): string {
		return strtolower( substr( get_locale(), 0, 2 ) );
	}

	// -------------------------------------------------------------

	/**
	 * @param $user_id
	 *
	 * @return string
	 */
	public static function getUserLink( $user_id = null ): string {
		if ( ! $user_id ) {
			$user_id = get_the_author_meta( 'ID' );
		}

		return get_author_posts_url( $user_id );
	}

	// -------------------------------------------------------------

	/**
	 * @param mixed|null $obj
	 * @param mixed $fallback
	 *
	 * @return array|false|int|mixed|string|WP_Error|WP_Term|null
	 */
	public static function getPermalink( mixed $obj = null, $fallback = false ): mixed {
		if ( empty( $obj ) && ! empty( $fallback ) ) {
			return $fallback;
		}
		if ( is_numeric( $obj ) || empty( $obj ) ) {
			return get_permalink( $obj );
		}
		if ( is_string( $obj ) ) {
			return $obj;
		}

		if ( is_array( $obj ) ) {
			if ( isset( $obj['term_id'] ) ) {
				return get_term_link( $obj['term_id'] );
			}
			if ( isset( $obj['user_login'] ) && isset( $obj['ID'] ) ) {
				return self::getUserLink( $obj['ID'] );
			}
			if ( isset( $obj['ID'] ) ) {
				return get_permalink( $obj['ID'] );
			}
		}
		if ( is_object( $obj ) ) {
			$val_class = get_class( $obj );
			if ( $val_class == 'WP_Post' ) {
				return get_permalink( $obj->ID );
			}
			if ( $val_class == 'WP_Term' ) {
				return get_term_link( $obj->term_id );
			}
			if ( $val_class == 'WP_User' ) {
				return self::getUserLink( $obj->ID );
			}
		}

		return $fallback;
	}

	// -------------------------------------------------------------

	/**
	 * @param mixed|null $obj
	 * @param mixed $fallback
	 *
	 * @return false|int|mixed
	 */
	public static function getId( mixed $obj = null, $fallback = false ): mixed {
		if ( empty( $obj ) && $fallback ) {
			return get_the_ID();
		}
		if ( is_numeric( $obj ) ) {
			return intval( $obj );
		}
		if ( filter_var( $obj, FILTER_VALIDATE_URL ) ) {
			return url_to_postid( $obj );
		}
		if ( is_string( $obj ) ) {
			return intval( $obj );
		}
		if ( is_array( $obj ) ) {
			if ( isset( $obj['term_id'] ) ) {
				return $obj['term_id'];
			}
			if ( isset( $obj['ID'] ) ) {
				return $obj['ID'];
			}
		}
		if ( is_object( $obj ) ) {
			$val_class = get_class( $obj );
			if ( $val_class == 'WP_Post' ) {
				return $obj->ID;
			}
			if ( $val_class == 'WP_Term' ) {
				return $obj->term_id;
			}
			if ( $val_class == 'WP_User' ) {
				return $obj->ID;
			}
		}

		return \false;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $url
	 *
	 * @return int
	 */
	public static function getPostIdFromUrl( string $url = '' ): int {
		if ( ! $url ) {
			global $wp;
			$url = home_url( add_query_arg( [], $wp->request ) );
		}

		return url_to_postid( $url );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $post_type - max 20 characters
	 *
	 * @return array|WP_Post|null
	 */
	public static function getCustomPost( string $post_type = 'hd_css' ): array|WP_Post|null {
		if ( empty( $post_type ) ) {
			$post_type = 'hd_css';
		}

		$custom_query_vars = [
			'post_type'              => $post_type,
			'post_status'            => get_post_stati(),
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'cache_results'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'lazy_load_term_meta'    => false,
		];

		$post    = null;
		$post_id = self::getThemeMod( $post_type . '_option_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && - 1 !== $post_id ) {
			$query = new WP_Query( $custom_query_vars );
			$post  = $query->post;

			set_theme_mod( $post_type . '_option_id', $post ? $post->ID : - 1 );
		}

		return $post;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $post_type - max 20 characters
	 * @param bool $encode
	 *
	 * @return array|string
	 */
	public static function getCustomPostContent( string $post_type = 'hd_css', bool $encode = false ): array|string {
		$post = self::getCustomPost( $post_type );
		if ( isset( $post->post_content ) ) {
			$post_content = wp_unslash( $post->post_content );
			if ( $encode ) {
				$post_content = wp_unslash( base64_decode( $post->post_content ) );
			}

			return $post_content;
		}

		return '';
	}

	// -------------------------------------------------------------

	/**
	 * @param string $mixed
	 * @param string $post_type - max 20 characters
	 * @param string $code_type
	 * @param bool $encode
	 * @param string $preprocessed
	 *
	 * @return array|int|WP_Error|WP_Post|null
	 */
	public static function updateCustomPost( string $mixed = '', string $post_type = 'hd_css', string $code_type = 'css', bool $encode = false, string $preprocessed = '' ): WP_Error|array|int|WP_Post|null {
		$post_type = $post_type ?: 'hd_css';
		$code_type = $code_type ?: 'text/css';

		if ( in_array( $code_type, [ 'css', 'text/css' ] ) ) {
			$mixed = self::stripAllTags( $mixed, true, false );
		}

		if ( $encode ) {
			$mixed = base64_encode( $mixed );
		}

//		else if ( in_array( $code_type, [ 'html', 'text/html' ] ) ) {
//			$mixed = base64_encode( $mixed );
//		}

		$post_data = [
			'post_type'             => $post_type,
			'post_status'           => 'publish',
			'post_content'          => $mixed,
			'post_content_filtered' => $preprocessed,
		];

		// Update post if it already exists, otherwise create a new one.
		$post = self::getCustomPost( $post_type );
		if ( $post ) {
			$post_data['ID'] = $post->ID;
			$r               = wp_update_post( wp_slash( $post_data ), true );
		} else {
			$post_data['post_title'] = $post_type . '_post_title';
			$post_data['post_name']  = wp_generate_uuid4();
			$r                       = wp_insert_post( wp_slash( $post_data ), true );

			if ( ! is_wp_error( $r ) ) {
				set_theme_mod( $post_type . '_option_id', $r );

				// Trigger creation of a revision. This should be removed once #30854 is resolved.
				$revisions = wp_get_latest_revision_id_and_total_count( $r );
				if ( ! is_wp_error( $revisions ) && 0 === $revisions['count'] ) {
					wp_save_post_revision( $r );
				}
			}
		}

		if ( is_wp_error( $r ) ) {
			return $r;
		}

		return get_post( $r );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $css - CSS, stored in `post_content`.
	 * @param string $post_type - max 20 characters
	 * @param bool $encode
	 * @param string $preprocessed - Pre-processed CSS, stored in `post_content_filtered`. Normally empty string.
	 *
	 * @return array|int|WP_Error|WP_Post|null
	 */
	public static function updateCustomCssPost( string $css, string $post_type = 'hd_css', bool $encode = false, string $preprocessed = '' ): WP_Error|array|int|WP_Post|null {
		return self::updateCustomPost( $css, $post_type, 'text/css', $encode, $preprocessed );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $post_type
	 * @param string $option
	 *
	 * @return string|string[]
	 */
	public static function getAspectRatioOption( string $post_type = '', string $option = '' ): array|string {
		$post_type = $post_type ?: 'post';
		$option    = $option ?: 'aspect_ratio__options';

		$aspect_ratio_options = self::getOption( $option );
		$width                = $aspect_ratio_options[ 'ar-' . $post_type . '-width' ] ?? '';
		$height               = $aspect_ratio_options[ 'ar-' . $post_type . '-height' ] ?? '';

		return ( $width && $height ) ? [ $width, $height ] : '';
	}

	// -------------------------------------------------------------

	/**
	 * @param string $post_type
	 * @param string $option
	 * @param string $default
	 *
	 * @return object
	 */
	public static function getAspectRatioClass( string $post_type = '', string $option = '', string $default = 'ar-3-2' ): object {
		$ratio = self::getAspectRatioOption( $post_type, $option );

		$ratio_x = $ratio[0] ?? '';
		$ratio_y = $ratio[1] ?? '';

		$ratio_style = '';
		if ( ! $ratio_x || ! $ratio_y ) {
			$ratio_class = $default;
		} else {
			$ratio_class     = 'ar-' . $ratio_x . '-' . $ratio_y;
			$ar_default_list = apply_filters( 'hd_aspect_ratio_default_list', [] );

			if ( is_array( $ar_default_list ) && ! in_array( $ratio_x . '-' . $ratio_y, $ar_default_list ) ) {
				$css = new CSS();

				$css->set_selector( '.' . $ratio_class );
				$css->add_property( 'height', 0 );

				$pb = ( $ratio_y / $ratio_x ) * 100;
				$css->add_property( 'padding-bottom', $pb . '%' );
				$css->add_property( 'aspect-ratio', $ratio_x . '/' . $ratio_y );

				$ratio_style = $css->css_output();
			}
		}

		return (object) [
			'class' => $ratio_class,
			'style' => $ratio_style,
		];
	}

	// -------------------------------------------------------------

	/**
	 * Get any necessary microdata.
	 *
	 * @param string $context The element to target.
	 *
	 * @return string Our final attribute to add to the element.
	 *
	 * GeneratePress
	 */
	public static function microdata( string $context ): string {
		$data = false;

		if ( 'body' === $context ) {
			$type = 'WebPage';

			if ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) {
				$type = 'Blog';
			}

			if ( is_search() ) {
				$type = 'SearchResultsPage';
			}

			if ( function_exists( 'is_shop' ) && is_shop() ) {
				$type = 'Collection';
			}

			if ( function_exists( 'is_product_category' ) && is_product_category() ) {
				$type = 'Collection';
			}

			$data = sprintf( 'itemtype="https://schema.org/%s" itemscope', esc_html( $type ) );
		}

		if ( 'header' === $context ) {
			$data = 'itemtype="https://schema.org/WPHeader" itemscope';
		}

		if ( 'navigation' === $context ) {
			$data = 'itemtype="https://schema.org/SiteNavigationElement" itemscope';
		}

		if ( 'article' === $context ) {
			$type = apply_filters( 'hd_article_itemtype', 'CreativeWork' );
			$data = sprintf( 'itemtype="https://schema.org/%s" itemscope', esc_html( $type ) );
		}

		if ( 'post-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'comment-body' === $context ) {
			$data = 'itemtype="https://schema.org/Comment" itemscope';
		}

		if ( 'comment-author' === $context ) {
			$data = 'itemprop="author" itemtype="https://schema.org/Person" itemscope';
		}

		if ( 'sidebar' === $context ) {
			$data = 'itemtype="https://schema.org/WPSideBar" itemscope';
		}

		if ( 'footer' === $context ) {
			$data = 'itemtype="https://schema.org/WPFooter" itemscope';
		}

		if ( 'text' === $context ) {
			$data = 'itemprop="text"';
		}

		if ( 'url' === $context ) {
			$data = 'itemprop="url"';
		}

		return apply_filters( "hd_{$context}_microdata", $data );
	}

	// -------------------------------------------------------------

	/**
	 * @param $message
	 *
	 * @return void
	 */
	public static function messageSuccess( $message ): void {
		$message = $message ?: 'Values saved';
		$message = __( $message, TEXT_DOMAIN );

		$class = 'notice notice-success is-dismissible';
		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr( $class ), $message );
	}

	// -------------------------------------------------------------

	/**
	 * @param $message
	 *
	 * @return void
	 */
	public static function messageError( $message ): void {
		$message = $message ?: 'Values error';
		$message = __( $message, TEXT_DOMAIN );

		$class = 'notice notice-error is-dismissible';
		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr( $class ), $message );
	}

	// -------------------------------------------------------------

	/**
	 * A fallback when no navigation is selected by default.
	 *
	 * @param bool $container
	 *
	 * @return void
	 */
	public static function menuFallback( bool $container = false ): void {
		echo '<div class="menu-fallback">';
		if ( $container ) {
			echo '<div class="grid-container">';
		}

		/* translators: %1$s: link to menus, %2$s: link to customize. */
		printf(
			__( 'Please assign a menu to the primary menu location under %1$s or %2$s the design.', TEXT_DOMAIN ),
			/* translators: %s: menu url */
			sprintf(
				__( '<a class="_blank" href="%s">Menus</a>', TEXT_DOMAIN ),
				get_admin_url( get_current_blog_id(), 'nav-menus.php' )
			),
			/* translators: %s: customize url */
			sprintf(
				__( '<a class="_blank" href="%s">Customize</a>', TEXT_DOMAIN ),
				get_admin_url( get_current_blog_id(), 'customize.php' )
			)
		);

		if ( $container ) {
			echo '</div>';
		}
		echo '</div>';
	}

	// -------------------------------------------------------------

	/**
	 * Check if plugin is installed by getting all plugins from the plugins dir
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	public static function check_plugin_installed( $plugin_slug ): bool {

		// Check if needed functions exist - if not, require them
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();

		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
	}

	// -------------------------------------------------------------

	/**
	 * Check if the plugin is installed
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	public static function check_plugin_active( $plugin_slug ): bool {
		if ( self::check_plugin_installed( $plugin_slug ) ) {
			if ( is_plugin_active( $plugin_slug ) ) {
				return true;
			}
		}

		return false;
	}
}
