<?php

namespace APX\Themes;

use APX\Helpers\Arr;
use APX\Helpers\Cast;
use APX\Helpers\Str;
use APX\Helpers\Url;
use APX\Walkers\Horizontal_Nav_Walker;
use APX\Walkers\Vertical_Nav_Walker;
use WP_Error;
use WP_Query;
use WP_Term;

\defined( '\WPINC' ) || die;

/**
 * Global Functions Class
 *
 * @author   APX
 */

if ( ! class_exists( 'Func' ) ) {
    class Func {

        // -------------------------------------------------------------

        /**
         * @param $attachment_id
         * @param bool $return_object
         * @return array|object|null
         */
        public static function getAttachment( $attachment_id, bool $return_object = true ) {
            $attachment = get_post( $attachment_id );
            if (!$attachment) {
                return null;
            }

            $_return = [
                'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
                'caption'     => $attachment->post_excerpt,
                'description' => $attachment->post_content,
                'href'        => get_permalink( $attachment->ID ),
                'src'         => $attachment->guid,
                'title'       => $attachment->post_title
            ];

            if (true === $return_object) {
                $_return = Cast::toObject($_return);
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
        public static function lazyScriptTag( array $arr_parsed, string $tag, string $handle, string $src ) {
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
         * @param array $arr_styles [ $handle ]
         * @param string $html
         * @param string $handle
         *
         * @return array|string|string[]|null
         */
        public static function lazyStyleTag( array $arr_styles, string $html, string $handle ) {
            foreach ( $arr_styles as $style ) {
                if ( str_contains( $handle, $style ) ) {
                    return preg_replace( '/media=\'all\'/', 'media=\'print\' onload=\'this.media="all"\'', $html );
                }
            }

            return $html;
        }

        // -------------------------------------------------------------

        /**
         * @param $mod_name
         * @param $default
         *
         * @return mixed|string|string[]
         */
        public static function getThemeMod( $mod_name, $default = false ) {
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
                        $_is_loaded[0][ strtolower( $mod_name ) ] = str_replace( [ 'https://' ], 'http://', $_mod );
                    }
                }

                return $_is_loaded[0][ strtolower( $mod_name ) ];
            }

            return $default;
        }

        // -------------------------------------------------------------

        /**
         * @param object $term
         * @param string $post_type
         * @param bool $include_children
         *
         * @param int $posts_per_page
         * @param int|bool $paged
         * @param array $orderby
         * @return bool|WP_Query
         */
        public static function queryByTerm($term, string $post_type = 'any', bool $include_children = true, int $posts_per_page = 0, $paged = false, $orderby = [] ) {
            if (!$term || !$post_type) {
                return false;
            }

            $term = Cast::toObject($term);
            $tax_query = [];
            if (isset($term->taxonomy) && isset($term->term_id)) {
                $tax_query[] = [
                    'taxonomy' => $term->taxonomy,
                    'terms'    => [$term->term_id],
                    'include_children' => (bool) $include_children,
                ];
            }

            $_args = [
                'ignore_sticky_posts' => true,
                'no_found_rows' => true,
                'post_status' => 'publish',
                'tax_query' => $tax_query,
                'nopaging' => true,
            ];

            if (is_array($orderby)) {
                $orderby = Arr::removeEmptyValues($orderby);
            } else {
                $orderby = ['date' => 'DESC'];
            }

            $_args['orderby'] = $orderby;

            if ($post_type) {
                $_args['post_type'] = $post_type;
            }

            if ($posts_per_page) {
                $_args['posts_per_page'] = $posts_per_page;
            }

            if ($paged !== false && Cast::toInt($paged) >= 0) {
                $_args['paged'] = $paged;
                $_args['nopaging'] = false;
            }

            $_query = new \WP_Query($_args);
            if (!$_query->have_posts()) {
                return false;
            }
            return $_query;
        }

        // -------------------------------------------------------------

        /**
         * @param array $term_ids
         * @param string $taxonomy
         * @param string $post_type
         * @param bool $include_children
         * @param int $posts_per_page
         *
         * @return bool|WP_Query
         */
        public static function queryByTerms($term_ids = [], string $taxonomy = 'category', string $post_type = 'any', bool $include_children = true, int $posts_per_page = 10) {
            if (!$term_ids) {
                return false;
            }

            if (!is_array( $term_ids )) {
                $term_ids = Cast::toArray( $term_ids );
            }

            if (!$taxonomy) {
                $taxonomy = 'category';
            }

            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'terms' => $term_ids,
                'include_children' => (bool)$include_children,
            ];

            $_args = [
                'post_status' => 'publish',
                'orderby' => ['date' => 'DESC'],
                'tax_query' => $tax_query,
                'nopaging' => true,
                'no_found_rows' => true,
                'ignore_sticky_posts' => true,
            ];

            if ($post_type) {
                $_args['post_type'] = $post_type;
            }

            if ($posts_per_page) {
                $_args['posts_per_page'] = $posts_per_page;
            }

            // query
            $r = new \WP_Query($_args);
            if (!$r->have_posts())
                return false;

            return $r;
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
                    'menu_class'     => 'dropdown menu horizontal horizontal-menu',
                    'theme_location' => '',
                    'depth'          => 4,
                    'fallback_cb'    => false,
                    'walker'         => new Horizontal_Nav_Walker,
                    'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
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
                    'menu_class'     => 'vertical menu',
                    'theme_location' => '',
                    'depth'          => 4,
                    'fallback_cb'    => false,
                    'walker'         => new Vertical_Nav_Walker,
                    'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-accordion-menu data-submenu-toggle="true">%3$s</ul>',
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
         * @param string $location
         * @param string $menu_class
         * @param string $menu_id
         *
         * @return bool|string
         */
        public static function mainNav( string $location = 'main-nav', string $menu_class = 'desktop-menu', string $menu_id = 'main-menu' ) {
            return self::horizontalNav( [
                'menu_id'        => $menu_id,
                'menu_class'     => $menu_class . ' dropdown menu horizontal horizontal-menu',
                'theme_location' => $location,
                'echo'           => false,
            ] );
        }

        // -------------------------------------------------------------

        /**
         * @param string $location
         * @param string $menu_class
         * @param string $menu_id
         *
         * @return bool|string
         */
        public static function mobileNav( string $location = 'mobile-nav', string $menu_class = 'mobile-menu', string $menu_id = 'mobile-menu' ) {
            return self::verticalNav( [
                'menu_id'        => $menu_id,
                'menu_class'     => $menu_class . ' vertical menu',
                'theme_location' => $location,
                'echo'           => false,
            ] );
        }

        // -------------------------------------------------------------

        /**
         * @param string $location
         * @param string $menu_class
         *
         * @return bool|string
         */
        public static function termNav( string $location = 'policy-nav', string $menu_class = 'terms-menu' ) {
            return wp_nav_menu( [
                'container'      => false,
                'menu_class'     => $menu_class . ' menu horizontal horizontal-menu',
                'theme_location' => $location,
                'items_wrap'     => '<ul role="menubar" class="%2$s">%3$s</ul>',
                'depth'          => 1,
                'fallback_cb'    => false,
                'echo'           => false,
            ] );
        }

        // -------------------------------------------------------------

        /**
         * @param bool $echo
         *
         * @return string|null
         */
        public static function paginationLinks( bool $echo = true ) {
            global $wp_query;
            if ( $wp_query->max_num_pages > 1 ) {

                // This needs to be an unlikely integer
                $big = 999999999;

                // For more options and info view the docs for paginate_links()
                // http://codex.wordpress.org/Function_Reference/paginate_links
                $paginate_links = paginate_links(
                    apply_filters(
                        'wp_pagination_args',
                        [
                            'base'      => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
                            'current'   => max( 1, get_query_var( 'paged' ) ),
                            'total'     => $wp_query->max_num_pages,
                            'end_size'  => 3,
                            'mid_size'  => 3,
                            'prev_next' => true,
                            'prev_text' => '<i data-glyph="???"></i>',
                            'next_text' => '<i data-glyph="???"></i>',
                            'type'      => 'list',
                        ]
                    )
                );

                $paginate_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination">', $paginate_links );
                $paginate_links = str_replace( '<li><span class="page-numbers dots">&hellip;</span></li>', '<li class="ellipsis"></li>', $paginate_links );
                $paginate_links = str_replace( '<li><span aria-current="page" class="page-numbers current">', '<li class="current"><span aria-current="page" class="show-for-sr">You\'re on page </span>', $paginate_links );
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

        // -------------------------------------------------------------

        /**
         * @param bool $echo
         *
         * @return string|void
         */
        public static function siteTitleOrLogo( bool $echo = true ) {
            if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                $logo = get_custom_logo();
                $html = ( is_home() || is_front_page() ) ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
            } else {
                $tag  = is_home() ? 'h1' : 'div';
                $html = '<' . esc_attr( $tag ) . ' class="site-title"><a title href="' . Url::home() . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) . '>';
                if ( '' !== get_bloginfo( 'description' ) ) {
                    $html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
                }
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
         * @return string
         */
        function siteLogo(string $theme = 'default', ?string $class = '' ) {

            $html = '';
            $custom_logo_id = null;

            if ( 'default' !== $theme && $theme_logo = self::getThemeMod( $theme . '_logo' ) ) {
                $custom_logo_id = attachment_url_to_postid( $theme_logo );
            }
            else if ( has_custom_logo() ) {
                $custom_logo_id = self::getThemeMod( 'custom_logo' );
            }

            // We have a logo. Logo is go.
            if ( $custom_logo_id ) {
                $custom_logo_attr = array(
                    'class'   => $theme . '-logo',
                    'loading' => 'lazy',
                );

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
                if ($class) {
                    $html = '<div class="' . $class . '"><a class="after-overlay" title="' . $image_alt . '" href="' . Url::home() . '">' . $logo . '</a></div>';
                } else {
                    $html = '<a class="after-overlay" title="' . $image_alt . '" href="' . Url::home() . '">' . $logo . '</a>';
                }
            }

            return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        // -------------------------------------------------------------

        /**
         * @param null $post
         * @param string|null $class
         *
         * @return string|null
         */
        public static function loopExcerpt($post = null, ?string $class = 'excerpt' ) {
            $excerpt = get_the_excerpt( $post );
            if ( ! Str::stripSpace( $excerpt ) ) {
                return null;
            }

            $excerpt = strip_tags($excerpt);
            if ( ! $class ) {
                return $excerpt;
            }

            return "<p class=\"$class\">{$excerpt}</p>";
        }

        // -------------------------------------------------------------

        /**
         * @param $post
         * @param string|null $class
         * @param bool $glyph_icon
         * @return string|null
         */
        public static function postExcerpt($post = null, ?string $class = 'excerpt', bool $glyph_icon = false ) {
            $post = get_post($post);
            if ( ! Str::stripSpace( $post->post_excerpt ) ) {
                return null;
            }

            $open  = '';
            $close = '';
            $glyph = '';
            if ( true === $glyph_icon ) {
                $glyph = ' data-glyph="???"';
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
         * @param string|null $class
         *
         * @return string|null
         */
        public static function termExcerpt($term = 0, ?string $class = 'excerpt' ) {
            $description = term_description( $term );
            if ( ! Str::stripSpace( $description ) ) {
                return null;
            }

            if ( ! $class ) {
                return $description;
            }

            return "<div class=\"$class\">$description</div>";
        }

        // -------------------------------------------------------------

        /**
         * @param $post
         * @param string|null $taxonomy
         * @return array|false|mixed|WP_Error|WP_Term
         */
        public static function primaryTerm($post = null, ?string $taxonomy = '' ) {
            //$post = get_post( $post );
            //$ID   = $post->ID ?? null;

            // @todo not optimized
            if ( ! $taxonomy ) {
                $post_type  = get_post_type( $post );
                $taxonomies = get_object_taxonomies( $post_type );
                if ( isset( $taxonomies[0] ) ) {
                    if ( 'product_type' == $taxonomies[0] && isset( $taxonomies[2] ) ) {
                        $taxonomy = $taxonomies[2];
                    }
                }
            }

            if ( ! $taxonomy ) {
                $taxonomy = 'category';
            }

            // Rank Math SEO
            // https://vi.wordpress.org/plugins/seo-by-rank-math/
            $primary_term_id = get_post_meta( get_the_ID(), 'rank_math_primary_' . $taxonomy, true );
            if ( $primary_term_id ) {
                $term = get_term( $primary_term_id, $taxonomy );
                if ( $term ) {
                    return $term;
                }
            }

            // Yoast SEO
            // https://vi.wordpress.org/plugins/wordpress-seo/
            if ( class_exists( '\WPSEO_Primary_Term' ) ) {

                // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
                $wpseo_primary_term = new \WPSEO_Primary_Term($taxonomy, $post);
                $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                $term = get_term($wpseo_primary_term, $taxonomy);
                if ( $term ) {
                    return $term;
                }
            }

            // Default, first category
            $post_terms = get_the_terms( $post, $taxonomy );
            if ( is_array( $post_terms ) ) {
                return $post_terms[0];
            }

            return false;
        }

        // -------------------------------------------------------------

        /**
         * @param null $post
         * @param string|null $taxonomy
         * @param string|null $wrapper_open
         * @param string|null $wrapper_close
         *
         * @return string|null
         */
        public static function getPrimaryTerm($post = null, ?string $taxonomy = '', ?string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>' ) {
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
         * @param $post
         * @param string|null $taxonomy
         * @param string|null $wrapper_open
         * @param string|null $wrapper_close
         *
         * @return string|null
         */
        public static function postTerms($post, ?string $taxonomy = 'category', ?string $wrapper_open = '<div class="terms">', ?string $wrapper_close = '</div>' ) {
            if ( ! $taxonomy ) {
                $taxonomy = 'category';
            }

            $link       = '';
            $post_terms = get_the_terms( $post, $taxonomy );
            if ( empty( $post_terms ) ) {
                return false;
            }

            foreach ( $post_terms as $term ) {
                if ( $term->slug ) {
                    $link .= '<a href="' . esc_url( get_term_link($term) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
                }
            }

            if ( $wrapper_open && $wrapper_close ) {
                $link = $wrapper_open . $link . $wrapper_close;
            }

            return $link;
        }

        // -------------------------------------------------------------

        /**
         * @param string|null $taxonomy
         * @param int $id
         * @param string $sep
         *
         * @return void
         */
        public static function hashTags(?string $taxonomy = 'post_tag', int $id = 0, string $sep = '' ) {
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
                    '<div class="hashtag-links links">%1$s<span class="screen-reader-text">%2$s</span>%3$s</div>',
                    '<i data-glyph="#"></i>',
                    __( 'Tags', 'hd' ),
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
         * @deprecated use get_the_post_thumbnail_url( $post, $size )
         */
        public static function postImageSrc( $post = null, string $size = 'thumbnail' ) {
            return get_the_post_thumbnail_url( $post, $size );
        }

        // -------------------------------------------------------------

        /**
         *
         * @param $attachment_id
         * @param string $size
         *
         * @return string|null
         * @deprecated use wp_get_attachment_image_url( $attachment_id, $size )
         */
        public static function attachmentImageSrc( $attachment_id, string $size = 'thumbnail' ) {
            return wp_get_attachment_image_url( $attachment_id, $size );
        }

        // -------------------------------------------------------------

        /**
         * @param bool $img_wrap
         * @param bool $thumb
         * @return string
         */
        public static function placeholderSrc( bool $img_wrap = true, bool $thumb = true ) {
            $src = APX_THEME_URL . '/assets/img/placeholder.png';
            if ($thumb) {
                $src = APX_THEME_URL . '/assets/img/placeholder-320x320.png';
            }
            if ($img_wrap) {
                $src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder\">";
            }

            return $src;
        }

        // -------------------------------------------------------------

        /**
         * @param $term
         * @param null $acf_field_name
         * @param string $size
         * @param bool $img_wrap
         * @return string|null
         */
        public static function acfTermThumb( $term, $acf_field_name = null, string $size = "thumbnail", bool $img_wrap = false ) {
            if (is_numeric($term)) {
                $term = get_term( $term );
            }

            if ( class_exists('\ACF') && $attach_id = get_field( $acf_field_name, $term ) ) {
                $img_src = wp_get_attachment_image_url( $attach_id, $size );
                if ($img_wrap) {
                    $img_src = wp_get_attachment_image( $attach_id, $size );
                }

                return $img_src;
            }

            return null;
        }

        // -------------------------------------------------------------

        /**
         * @param $post
         * @param $from
         * @param $to
         * @param $_time
         * @return mixed|void
         */
        public static function humanizeTime( $post = null, $from = null, $to = null, $_time = null ) {
            $flag = false;
            $_ago = __( 'ago', 'apx' );
            if ( empty( $to ) ) {
                $to = current_time( 'timestamp' );
            }
            if ( empty( $from ) ) {
                $from = get_the_time( 'U', $post );
            }

            $diff = (int) abs( $to - $from );
            if ( $diff < HOUR_IN_SECONDS ) {
                $mins = round( $diff / MINUTE_IN_SECONDS );
                if ( $mins <= 1 ) {
                    $mins = 1;
                }
                /* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes */
                $since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
            } elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
                $hours = round( $diff / HOUR_IN_SECONDS );
                if ( $hours <= 1 ) {
                    $hours = 1;
                }
                /* translators: Time difference between two dates, in hours. %s: Number of hours */
                $since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
            } elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
                $days = round( $diff / DAY_IN_SECONDS );
                if ( $days <= 1 ) {
                    $days = 1;
                }
                /* translators: Time difference between two dates, in days. %s: Number of days */
                $since = sprintf( _n( '%s day', '%s days', $days, 'apx' ), $days );
            } else {
                $flag  = true;
                $since = ( $_time == null ) ? get_the_date( '', $post ) : sprintf( __( '%1$s at %2$s', 'apx' ), date( get_option( 'date_format' ), $from ), $_time );
            }
            if (!$flag) {
                $since = $since . ' <span class="ago">' . $_ago . '</span>';
            }

            return apply_filters( 'humanize_time', $since, $diff, $from, $to );
        }

        // -------------------------------------------------------------

        /**
         * Breadcrumbs
         * return void
         */
        public static function breadcrumbs() {
            global $post, $wp_query;

            $before = '<li class="current">';
            $after  = '</li>';

            if ( ! is_home() && ! is_front_page() ) {
                echo '<ul id="breadcrumbs" class="breadcrumbs" aria-label="breadcrumbs">';
                echo '<li><a class="home" href="' . Url::home() . '">' . __( 'Home', 'apx' ) . '</a></li>';

                //...
                if ( class_exists( '\WooCommerce' ) && is_shop() ) {
                    $shop_page_title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
                    echo $before . $shop_page_title . $after;
                }
                elseif ( $wp_query->is_posts_page ) {
                    $posts_page_title = get_the_title( get_option( 'page_for_posts', true ) );
                    echo $before . $posts_page_title . $after;
                }
                elseif ( $wp_query->is_post_type_archive ) {
                    $posts_page_title = post_type_archive_title( '', false );
                    echo $before . $posts_page_title . $after;
                }

                /** page, attachment */
                elseif ( is_page() || is_attachment() ) {

                    // parent page
                    if ( $post->post_parent ) {
                        $parent_id = $post->post_parent;
                        $breadcrumbs = [];

                        while ( $parent_id ) {
                            $page = get_post($parent_id);
                            $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                            $parent_id = $page->post_parent;
                        }

                        $breadcrumbs = array_reverse( $breadcrumbs );
                        foreach ( $breadcrumbs as $crumb ) {
                            echo $crumb;
                        }
                    }

                    echo $before . get_the_title() . $after;
                }

                /** single */
                elseif ( is_single() && ! is_attachment() ) {

                    if ( !in_array( get_post_type(), [ 'post', 'product', 'service', 'project' ])) {
                        $post_type = get_post_type_object( get_post_type() );
                        $slug      = $post_type->rewrite;
                        if ( ! is_bool( $slug ) ) {
                            echo '<li><a href="' . Url::home() . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
                        }
                    }
                    else {
                        $term = self::primaryTerm( $post );
                        if ($term) {
                            if ( $cat_code = get_term_parents_list( $term->term_id, $term->taxonomy, [ 'separator' => '' ] ) ) {
                                $cat_code = str_replace( '<a', '<li><a', $cat_code );
                                echo str_replace( '</a>', '</a></li>', $cat_code );
                            }
                        }
                    }

                    echo $before . get_the_title() . $after;
                }

                /** search page */
                elseif ( is_search() ) {
                    echo $before;
                    printf( __( 'Search Results for: %s', 'apx' ), get_search_query() );
                    echo $after;
                }

                /** tag */
                elseif ( is_tag() ) {
                    echo $before;
                    printf( __( 'Tag Archives: %s', 'apx' ), single_tag_title( '', false ) );
                    echo $after;
                }

                /** author */
                elseif ( is_author() ) {
                    global $author;

                    $userdata = get_userdata( $author );
                    echo $before;
                    echo $userdata->display_name;
                    echo $after;
                }

                /** day, month, year */
                elseif ( is_day() ) {
                    echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
                    echo '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a></li>';
                    echo $before . get_the_time( 'd' ) . $after;
                }
                elseif ( is_month() ) {
                    echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
                    echo $before . get_the_time( 'F' ) . $after;
                }
                elseif ( is_year() ) {
                    echo $before . get_the_time( 'Y' ) . $after;
                }

                /** category, tax */
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
                }

                /** 404 */
                elseif ( is_404() ) {
                    echo $before;
                    __( 'Not Found', 'apx' );
                    echo $after;
                }

                //...
                if ( get_query_var( 'paged' ) ) {
                    echo '<li class="paged">';
                    echo ' (';
                    echo __( 'page', 'apx' ) . ' ' . get_query_var( 'paged' );
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
         * @param mixed|null $id The ID, to load a single record;
         */
        public static function postComment(mixed $id = null ) {
            if ( !$id ) {
                if (get_post_type() === 'product') {
                    global $product;
                    $id = $product->get_id();
                }
                else {
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

            $wrapper_open = '<section id="comments-section" class="section comments-section comments-wrapper">';
            $wrapper_close = '</section>';

            //...
            $facebook_comment = false;
            $zalo_comment = false;

            if ( class_exists( '\ACF' ) ) {
                $facebook_comment = get_field('facebook_comment', $id);
                $zalo_comment = get_field('zalo_comment', $id);
            }

            if ( comments_open() || true === $facebook_comment || true === $zalo_comment ) {
                echo $wrapper_open;
                if ( comments_open() ) {
                    //if ( ( class_exists( '\WooCommerce' ) && 'product' != $post_type ) || ! class_exists( '\WooCommerce' ) ) {
                    comments_template();
                    //}
                }
                if ( true === $facebook_comment ) {
                    get_template_part( 'template-parts/comments/facebook' );
                }
                if ( true === $zalo_comment ) {
                    get_template_part( 'template-parts/comments/zalo' );
                }

                echo $wrapper_close;
            }
        }

        // -------------------------------------------------------------

        /**
         * A fallback when no navigation is selected by default.
         *
         * @param string|null $container
         * @return void
         */
        public static function menu_fallback(?string $container = 'grid-container' ) {
            echo '<div class="menu-fallback">';
            if ( $container ) {
                echo '<div class="' . $container . '">';
            }

            /* translators: %1$s: link to menus, %2$s: link to customize. */
            printf(
                __( 'Please assign a menu to the primary menu location under %1$s or %2$s the design.', 'apx' ),
                /* translators: %s: menu url */
                sprintf(
                    __( '<a class="_blank" href="%s">Menus</a>', 'apx' ),
                    get_admin_url( get_current_blog_id(), 'nav-menus.php' )
                ),
                /* translators: %s: customize url */
                sprintf(
                    __( '<a class="_blank" href="%s">Customize</a>', 'apx' ),
                    get_admin_url( get_current_blog_id(), 'customize.php' )
                )
            );
            if ( $container ) {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}