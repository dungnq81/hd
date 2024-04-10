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

			'main_nav' => __CLASS__ . '::main_nav',
			'menu_top' => __CLASS__ . '::menu_top',
		];

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
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
		$posts_per_page   = $atts['posts_per_page'] ? absint( $atts['posts_per_page'] ) : 12;
		$include_children = Helper::toBool( $atts['include_children'] );
		$orderby = [ 'date' => 'DESC' ];
		$strtotime_str    = $atts['limit_time'] ? Helper::toString( $atts['limit_time'] ) : false;

		$r = Helper::queryByTerms( $term_ids, $atts['post_type'], $atts['taxonomy'], $include_children, $posts_per_page, $orderby, [], $strtotime_str );
		if ( ! $r ) {
			return null;
		}

		// ok !
		$wrapper_open  = $atts['wrapper'] ? '<' . $atts['wrapper'] . ' class="' . $atts['wrapper_class'] . '">' : '';
		$wrapper_close = $atts['wrapper'] ? '</' . $atts['wrapper'] . '>' : '';

		$thumbnail_size = $atts['show']['thumbnail_size'] ?? 'medium';

		ob_start();

		$i = 0;

		// Load slides loop.
		while ( $r->have_posts() && $i < $posts_per_page ) :
			$r->the_post();

			global $post;

			$post_title     = get_the_title( $post->ID );
			$title          = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)', HD_TEXT_DOMAIN );
			$post_thumbnail = get_the_post_thumbnail( $post, $thumbnail_size );

			echo $wrapper_open . '<div class="cell">';
			echo '<article class="item">';

			// thumbnail
			if ( $atts['show']['thumbnail'] && $post_thumbnail ) :

				$scale_class = isset( $atts['show']['scale'] ) ? 'scale ' : '';

				$ratio_obj   = Helper::getAspectRatioClass( 'post', 'aspect_ratio__options' );
				$ratio_class = $ratio_obj->class ?? '';

				echo '<a class="d-block cover" href="' . get_permalink( $post->ID ) . '" aria-label="' . esc_attr( $title ) . '" tabindex="0">';
				echo '<span class="' . $scale_class . 'after-overlay res ' . $ratio_class . '">' . $post_thumbnail . '</span>';
				echo '</a>';

			endif;

			// post info
			echo '<div class="cover-content">';
			echo '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( $title ) . '"><h6>' . $title . '</h6></a>';

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
				echo '<a class="view-detail" href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( $title ) . '" data-glyph=""><span>' . __( 'Detail', HD_TEXT_DOMAIN ) . '</span></a>';
			}

			echo '</div>';

			echo '</article>';
			echo '</div>' . $wrapper_close;

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
				'id'       => esc_attr( uniqid( 'menu-' ) ),
				'depth'    => 4,
			],
			$atts,
			'vertical_menu'
		);

		$location = $atts['location'] ?: 'mobile-nav';
		$class    = $atts['class'] ?: 'mobile-menu';
		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-' ) );

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
				'id'       => esc_attr( uniqid( 'menu-' ) ),
				'depth'    => 4,
			],
			$atts,
			'horizontal_menu'
		);

		$location = $atts['location'] ?: 'main-nav';
		$class    = $atts['class'] ?: 'desktop-menu';
		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-' ) );

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
				'hide_if_desktop' => true,
                'class' => '',
			],
			$atts,
			'off_canvas_button'
		);

		$title = $atts['title'] ?: __( 'Menu', HD_TEXT_DOMAIN );
		$class = $atts['hide_if_desktop'] ? ' hide-for-large' : '';
		$class = $atts['class'] ? ' ' . $atts['class'] . $class : '';

		ob_start();

		?>
        <button class="menu-lines" type="button" data-open="offCanvasMenu" aria-label="button">
            <span class="menu-txt"><?= $title; ?></span>
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
				'id'    => esc_attr( uniqid( 'mail-' ) ),
			],
			$atts,
			'safe_mail'
		);

		$attributes['title'] = $atts['title'] ? esc_attr( $atts['title'] ) : esc_attr( $atts['email'] );
		$attributes['id']    = $atts['id'] ? esc_attr( $atts['id'] ) : esc_attr( uniqid( 'mail-' ) );

		if ( $atts['class'] ) {
			$attributes['class'] = esc_attr( $atts['class'] );
		}

		return Helper::safeMailTo( $atts['email'], $atts['title'], $attributes );
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
				'title' => '',
				'class' => '',
				'id'    => esc_attr( uniqid( 'search-' ) ),
			],
			$atts,
			'inline_search'
		);

		$title             = $atts['title'] ?: __( 'Search', HD_TEXT_DOMAIN );
		$title_for         = __( 'Search for', HD_TEXT_DOMAIN );
		$placeholder_title = esc_attr( __( 'Search ...', HD_TEXT_DOMAIN ) );
		$id                = $atts['id'] ?: esc_attr( uniqid( 'search-' ) );

		ob_start();

		?>
        <form role="search" action="<?= Helper::home(); ?>" class="frm-search" method="get" accept-charset="UTF-8" data-abide novalidate>
            <label for="<?= $id; ?>" class="screen-reader-text"><?= esc_attr( $title_for ); ?></label>
            <input id="<?= $id; ?>" required pattern="^(.*\S+.*)$" type="search" autocomplete="off" name="s" value="<?= get_search_query(); ?>" placeholder="<?= esc_attr( $placeholder_title ); ?>">
            <button type="submit" data-glyph="">
                <span><?= $title; ?></span>
            </button>
			<?php if ( class_exists( '\WooCommerce' ) ) : ?>
            <input type="hidden" name="post_type" value="product">
			<?php endif; ?>
        </form>
		<?php

		return '<div class="inline-search ' . $atts['class'] . '">' . ob_get_clean() . '</div>';
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
				'id'    => esc_attr( uniqid( 'search-' ) ),
			],
			$atts,
			'dropdown_search'
		);

		$title             = $atts['title'] ?: __( 'Search', HD_TEXT_DOMAIN );
		$title_for         = __( 'Search for', HD_TEXT_DOMAIN );
		$placeholder_title = __( 'Search ...', HD_TEXT_DOMAIN );
		$close_title       = __( 'Close', HD_TEXT_DOMAIN );
		$id                = $atts['id'] ?: esc_attr( uniqid( 'search-' ) );

		ob_start();

		?>
        <a class="trigger-s" title="<?= esc_attr( $title ); ?>" href="javascript:;" data-toggle="dropdown-<?= $id; ?>" data-glyph=""><span><?php echo $title; ?></span></a>
        <div role="search" class="dropdown-pane" id="dropdown-<?= $atts['id']; ?>" data-dropdown data-auto-focus="true">
            <form role="form" action="<?= Helper::home(); ?>" class="frm-search" method="get" accept-charset="UTF-8" data-abide novalidate>
                <div class="frm-container">
                    <label for="<?= $id; ?>" class="screen-reader-text"><?= esc_attr( $title_for ); ?></label>
                    <input id="<?= $id; ?>" required pattern="^(.*\S+.*)$" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr( $placeholder_title ); ?>">
                    <button class="btn-s" type="submit" data-glyph="">
                        <span><?php echo $title; ?></span>
                    </button>
                    <button class="trigger-s-close" type="button" data-glyph="">
                        <span><?php echo esc_attr( $close_title ); ?></span>
                    </button>
                </div>
				<?php if ( class_exists( '\WooCommerce' ) ) : ?>
                    <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
            </form>
        </div>
		<?php

		return '<div class="dropdown-search ' . $atts['class'] . '">' . ob_get_clean() . '</div>';
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function main_nav( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'location' => 'main-nav',
				'class'    => 'flex items-start lg:items-center lg:justify-center flex-col lg:flex-row gap-1 lg:gap-0',
				'id'       => esc_attr( uniqid( 'menu-' ) ),
				'depth'    => 4,
				'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'li_class'   => 'group items-center lg:items-center lg:px-4 py-2 lg:py-0 text-[var(--Gray-01)] hover:text-[var(--Primary-02)] relative first:pl-0 justify-between',
				'link_class'   => 'flex items-center gap-2 leading-[30px] justify-between',

				'li_class_depth' => 'border-b border-[var(--Gray-03)] text-[var(--Gray-01)] py-1 first:pt-0 last:border-none last:pb-0',
				'link_class_depth' => 'flex items-center gap-1 lg:p-2 hover:bg-[var(--Primary-04)] hover:text-[var(--Primary-02)] rounded-lg py-2 px-2',
			],
			$atts,
			'main_nav'
		);

		$location = $atts['location'] ?: 'main-nav';
		$class    = $atts['class'] ?: '';

		$li_class    = $atts['li_class'] ?: '';
		$link_class    = $atts['link_class'] ?: '';

		$li_class_depth    = $atts['li_class_depth'] ?: '';
		$link_class_depth    = $atts['link_class_depth'] ?: '';

		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-' ) );
		$items_wrap = $atts['items_wrap'] ?: '<ul id="%1$s" class="%2$s">%3$s</ul>';

		return Helper::horizontalNav( [
			'menu_id'        => $id,
			'menu_class'     => $class,
			'theme_location' => $location,
			'depth'          => $depth,
			'items_wrap' => $items_wrap,
			'li_class' => $li_class,
			'link_class' => $link_class,
			'li_class_depth' => $li_class_depth,
			'link_class_depth' => $link_class_depth,
			'echo'           => false,
		] );
	}

	// ------------------------------------------------------


	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function menu_top( array $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'location' => 'menu-top',
				'class'    => 'topnav flex items-center justify-start gap-4',
				'id'       => esc_attr( uniqid( 'menu-' ) ),
				'depth'    => 4,
				'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'link_class'   => 'flex items-center gap-1 text-white text-xs whitespace-nowrap capitalize hover:underline',
				'li_class' => '',
			],
			$atts,
			'menu_top'
		);

		$location = $atts['location'] ?: 'menu-top';
		$class    = $atts['class'] ?: '';
		$link_class    = $atts['link_class'] ?: '';
		$li_class    = $atts['li_class'] ?: '';
		$depth    = $atts['depth'] ? absint( $atts['depth'] ) : 1;
		$id       = $atts['id'] ?: esc_attr( uniqid( 'menu-' ) );
		$items_wrap = $atts['items_wrap'] ?: '<ul role="menubar" id="%1$s" class="%2$s">%3$s</ul>';

		return Helper::horizontalNav( [
			'menu_id'        => $id,
			'menu_class'     => $class,
			'theme_location' => $location,
			'depth'          => $depth,
			'items_wrap' => $items_wrap,
			'link_class' => $link_class,
			'li_class' => $li_class,
			'echo'           => false,
		] );
	}
}
