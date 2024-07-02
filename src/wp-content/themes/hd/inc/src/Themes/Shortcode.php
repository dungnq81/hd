<?php

namespace Themes;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Shortcode Class
 *
 * @author WEBHD
 */
final class Shortcode {

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public static function init(): void {
		$shortcodes = [
			'safe_mail'         => __CLASS__ . '::safe_mail',
			'site_logo'         => __CLASS__ . '::site_logo',
			'inline_search'     => __CLASS__ . '::inline_search',
			'dropdown_search'   => __CLASS__ . '::dropdown_search',
			'off_canvas_button' => __CLASS__ . '::off_canvas_button',

			'horizontal_menu' => __CLASS__ . '::horizontal_menu',
			'vertical_menu'   => __CLASS__ . '::vertical_menu',

			'posts' => __CLASS__ . '::posts',

			'menu_logo'         => __CLASS__ . '::menu_logo',
			'social_menu'         => __CLASS__ . '::social_menu',
		];

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	// ------------------------------------------------------

    public static function social_menu( array $atts = [] ): string {
	    $atts = shortcode_atts(
		    [
			    'class'    => 'social-menu',
		    ],
		    $atts,
		    'social_menu'
	    );

	    $class = $atts['class'] ? ' ' . Helper::esc_attr_strip_tags( $atts['class'] ) : ' social-menu';

	    ob_start();

	    $social_options       = get_option( 'social__options' );
	    $social_follows_links = filter_setting_options( 'social_follows_links', [] );

        if ( $social_options ) {
            foreach ( $social_options as $key => $social_option ) {
                $data = [
                    'url' => $social_option['url'],
                    'name' => $key,
                    //'color' => $social_follows_links[$key]['color'],
                    'icon' => $social_follows_links[$key]['icon'],
                ];

                $thumb = '';
	            if ( filter_var( $data['icon'], FILTER_VALIDATE_URL ) || str_starts_with( $data['icon'], 'data:' ) ) :
		            $thumb = '<img src="' . $data['icon'] . '" alt="' . esc_attr( $data['name'] ) . '">';
                elseif ( str_starts_with( $data['icon'], '<svg' ) ) :
	                $thumb = $data['icon'];
                elseif ( is_string( $data['icon'] ) ) :
	                $thumb = '<i class="' . $data['icon'] . '"></i>';
	            endif;

                if ( ! empty( $social_option['url'] ) ) {
        ?>
        <li>
            <a href="<?=$data['url']?>" title="<?=esc_attr_strip_tags( $data['name'] )?>" target="_blank">
                <?=$thumb?>
                <span class="social-txt"><?=$data['name']?></span>
            </a>
        </li>
        <?php } } }
	    return '<ul class="menu' . $class . '">' . ob_get_clean() . '</ul>';
    }

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return false|string|null
	 */
	public static function posts( array $atts = [] ): false|string|null {
		$default_atts = [
			'post_type'        => 'post',
			'term_ids'         => '',
			'taxonomy'         => 'category',
			'include_children' => false,
			'posts_per_page'   => 12,

			'limit_time'    => '',
			'wrapper'       => '',
			'wrapper_class' => '',
			'title_tag'     => 'h3',

			'show' => [
				'thumbnail'      => true,
				'thumbnail_size' => 'medium',
				'scale'          => true,
				'time'           => true,
				'term'           => true,
				'desc'           => true,
				'more'           => true,
			],
		];

		$atts = shortcode_atts(
			$default_atts,
			$atts,
			'posts'
		);

		//...
		$term_ids         = $atts['term_ids'] ?: [];
		$posts_per_page   = $atts['posts_per_page'] ? absint( $atts['posts_per_page'] ) : get_option( 'posts_per_page' );
		$include_children = Helper::toBool( $atts['include_children'] );
		$orderby          = [ 'date' => 'DESC' ];
		$strtotime_str    = $atts['limit_time'] ? Helper::toString( $atts['limit_time'] ) : false;

		$r = Helper::queryByTerms( $term_ids, $atts['post_type'], $atts['taxonomy'], $include_children, $posts_per_page, $orderby, [], $strtotime_str );
		if ( ! $r ) {
			return null;
		}

		// ok !
		$wrapper_open  = $atts['wrapper'] ? '<' . $atts['wrapper'] . ' class="' . $atts['wrapper_class'] . '">' : '';
		$wrapper_close = $atts['wrapper'] ? '</' . $atts['wrapper'] . '>' : '';

		$thumbnail_size = $atts['show']['thumbnail_size'] ?? 'medium';
		$title_tag      = $atts['title_tag'] ?? 'p';

		ob_start();

		$i = 0;

		// Load slides loop.
		while ( $r->have_posts() && $i < $posts_per_page ) :
			$r->the_post();

			global $post;

			$post_title     = get_the_title( $post->ID );
			$post_title     = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)', TEXT_DOMAIN );

            $attr_post_title = Helper::esc_attr_strip_tags( $post_title );
			$post_thumbnail = get_the_post_thumbnail( $post, $thumbnail_size, [ 'alt' => $attr_post_title ] );

			if ( empty( $post_thumbnail ) ) {
				$post_thumbnail = Helper::placeholderSrc();
			}

			//echo $wrapper_open . '<div class="cell">';
			echo $wrapper_open;
			echo '<div class="item">';

			// thumbnail
			if ( $atts['show']['thumbnail'] ) :

				$scale_class = isset( $atts['show']['scale'] ) ? 'scale ' : '';
				$ratio_class = Helper::aspectRatioClass();

				echo '<a class="block cover" href="' . get_permalink( $post->ID ) . '" aria-label="' . $attr_post_title . '">';
				echo '<span class="' . $scale_class . 'after-overlay res ' . $ratio_class . '">' . $post_thumbnail . '</span>';
				echo '</a>';

			endif;

			// post info
			echo '<div class="cover-content">';
			echo '<a href="' . get_permalink( $post->ID ) . '" title="' . $attr_post_title . '"><' . $title_tag . '>' . $post_title . '</' . $title_tag . '></a>';

			if ( $atts['show']['time'] || $atts['show']['term'] ) :
				echo '<div class="meta">';

				if ( $atts['show']['time'] ) {
					echo '<span class="post-date">' . Helper::humanizeTime( $post ) . '</span>';
				}
				if ( $atts['show']['term'] ) {
					echo Helper::getPrimaryTerm( $post );
				}

				echo '</div>';
			endif;

			if ( $atts['show']['desc'] ) {
				echo Helper::loopExcerpt( $post );
			}
			if ( $atts['show']['more'] ) {
				echo '<a class="view-detail" href="' . get_permalink( $post->ID ) . '" title="' . $attr_post_title . '" data-glyph=""><span>' . __( 'Detail', TEXT_DOMAIN ) . '</span></a>';
			}

			echo '</div>';

			echo '</div>';
			//echo '</div>' . $wrapper_close;
			echo $wrapper_close;

			++ $i;
		endwhile;
		wp_reset_postdata();

		return ob_get_clean();
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function vertical_menu( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'location' => 'mobile-nav',
				'class'    => 'mobile-menu',
				'id'       => esc_attr( uniqid( 'menu-', false ) ),
				'depth'    => 4,
			],
			$atts,
			'vertical_menu'
		);

		$location = $atts['location'] ?: 'mobile-nav';
		$class    = $atts['class'] ? Helper::esc_attr_strip_tags( $atts['class'] ) : 'mobile-menu';
		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-', false ) );

		return Helper::verticalNav( [
			'menu_id'        => $id,
			'menu_class'     => 'menu vertical vertical-menu ' . $class,
			'theme_location' => $location,
			'depth'          => $depth,
			'echo'           => false,
		] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function horizontal_menu( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'location' => 'main-nav',
				'class'    => 'desktop-menu',
				'id'       => esc_attr( uniqid( 'menu-', false ) ),
				'depth'    => 4,
			],
			$atts,
			'horizontal_menu'
		);

		$location = $atts['location'] ?: 'main-nav';
		$class    = $atts['class'] ? Helper::esc_attr_strip_tags( $atts['class'] ) : 'desktop-menu';
		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-', false ) );

		return Helper::horizontalNav( [
			'menu_id'        => $id,
			'menu_class'     => 'dropdown menu horizontal horizontal-menu ' . $class,
			'theme_location' => $location,
			'depth'          => $depth,
			'echo'           => false,
		] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function off_canvas_button( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'title'           => '',
				'hide_if_desktop' => 1,
				'class'           => '',
			],
			$atts,
			'off_canvas_button'
		);

		$title = $atts['title'] ?: __( 'Menu', TEXT_DOMAIN );
		$class = ! empty( $atts['hide_if_desktop'] ) ? ' !lg:hidden' : '';
		$class .= $atts['class'] ? ' ' . Helper::esc_attr_strip_tags( $atts['class'] ) . $class : '';

		ob_start();

		?>
        <button class="menu-lines" type="button" data-open="offCanvasMenu" aria-label="button">
            <span class="menu-txt"><?= $title ?></span>
        </button>
		<?php

		return '<div class="off-canvas-content' . $class . '" data-off-canvas-content>' . ob_get_clean() . '</div>';
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function safe_mail( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'title' => '',
				'email' => '',
				'class' => '',
				'id'    => esc_attr( uniqid( 'mail-', false ) ),
			],
			$atts,
			'safe_mail'
		);

		$attributes['title'] = $atts['title'] ? Helper::esc_attr_strip_tags( $atts['title'] ) : esc_attr_strip_tags( $atts['email'] );
		$attributes['id']    = $atts['id'] ? Helper::esc_attr_strip_tags( $atts['id'] ) : esc_attr( uniqid( 'mail-', false ) );

		if ( $atts['class'] ) {
			$attributes['class'] = Helper::esc_attr_strip_tags( $atts['class'] );
		}

		return Helper::safeMailTo( $atts['email'], $atts['title'], $attributes );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function menu_logo( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'heading' => 'h1',
				'class'   => 'logo',
			],
			$atts,
			'menu_logo'
		);

		return Helper::siteTitleOrLogo( false, $atts['heading'], $atts['class'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function site_logo( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'theme' => 'default',
				'class' => 'site-logo',
			],
			$atts,
			'site_logo'
		);

		return Helper::siteLogo( $atts['theme'], $atts['class'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function inline_search( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'title'       => '',
				'placeholder' => '',
				'class'       => '',
				'id'          => esc_attr( uniqid( 'search-', false ) ),
			],
			$atts,
			'inline_search'
		);

		$title             = $atts['title'] ?: '';
		$title_for         = __( 'Search', TEXT_DOMAIN );
		$placeholder_title = $atts['placeholder'] ?: __( 'Search...', TEXT_DOMAIN );
		$id                = $atts['id'] ? Helper::esc_attr_strip_tags( $atts['id'] ) : esc_attr( uniqid( 'search-', false ) );
		$class             = $atts['class'] ? ' ' . Helper::esc_attr_strip_tags( $atts['class'] ) : '';

		ob_start();

		?>
        <form action="<?= Helper::home(); ?>" class="frm-search" method="get" accept-charset="UTF-8" data-abide novalidate>
            <label for="<?= $id ?>" class="screen-reader-text"><?= $title_for ?></label>
            <input id="<?= $id ?>" required pattern="^(.*\S+.*)$" type="search" autocomplete="off" name="s" value="<?= get_search_query() ?>" placeholder="<?= $placeholder_title; ?>">
            <button type="submit" data-glyph="">
                <span><?= $title ?></span>
            </button>
			<?php if ( class_exists( \WooCommerce::class ) ) : ?>
            <input type="hidden" name="post_type" value="product">
			<?php endif; ?>
        </form>
		<?php

		return '<div class="inline-search' . $class . '">' . ob_get_clean() . '</div>';
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function dropdown_search( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'title' => '',
				'class' => '',
				'id'    => esc_attr( uniqid( 'search-', false ) ),
			],
			$atts,
			'dropdown_search'
		);

		$title             = $atts['title'] ?: __( 'Search', TEXT_DOMAIN );
		$title_for         = __( 'Search for', TEXT_DOMAIN );
		$placeholder_title = esc_attr( __( 'Search ...', TEXT_DOMAIN ) );
		$close_title       = __( 'Close', TEXT_DOMAIN );
		$id                = $atts['id'] ? Helper::esc_attr_strip_tags( $atts['id'] ) : esc_attr( uniqid( 'search-', false ) );
		$class             = $atts['class'] ? ' ' . Helper::esc_attr_strip_tags( $atts['class'] ) : '';

		ob_start();

		?>
        <a class="trigger-s" title="<?= Helper::esc_attr_strip_tags( $title ) ?>" href="javascript:;" data-toggle="dropdown-<?= $id ?>" data-glyph=""><span><?= $title ?></span></a>
        <div role="search" class="dropdown-pane" id="dropdown-<?= $id ?>" data-dropdown data-auto-focus="true">
            <form action="<?= Helper::home() ?>" class="frm-search" method="get" accept-charset="UTF-8" data-abide novalidate>
                <div class="frm-container">
                    <label for="<?= $id ?>" class="screen-reader-text"><?= $title_for ?></label>
                    <input id="<?= $id ?>" required pattern="^(.*\S+.*)$" type="search" name="s" value="<?= get_search_query() ?>" placeholder="<?= $placeholder_title ?>">
                    <button class="btn-s" type="submit" data-glyph="">
                        <span><?= $title ?></span>
                    </button>
                    <button class="trigger-s-close" type="button" data-glyph="">
                        <span><?= $close_title ?></span>
                    </button>
                </div>
				<?php if ( class_exists( \WooCommerce::class ) ) : ?>
                <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
            </form>
        </div>
		<?php

		return '<div class="dropdown-search' . $class . '">' . ob_get_clean() . '</div>';
	}

	// ------------------------------------------------------
}
