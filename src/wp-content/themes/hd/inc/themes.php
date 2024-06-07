<?php
/**
 * Themes functions
 *
 * @author WEBHD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// --------------------------------------------------

add_action( 'after_setup_theme', '__after_setup_theme_action', 11 );

/**
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
 *
 * @return void
 */
function __after_setup_theme_action(): void {
	register_nav_menus(
		[
			'main-nav'   => __( 'Primary Menu', TEXT_DOMAIN ),
			//'second-nav' => __( 'Secondary Menu', TEXT_DOMAIN ),
			//'mobile-nav' => __( 'Handheld Menu', TEXT_DOMAIN ),
			'social-nav' => __( 'Social menu', TEXT_DOMAIN ),
			//'policy-nav' => __( 'Term menu', TEXT_DOMAIN ),
		]
	);
}

// --------------------------------------------------

add_action( 'widgets_init', '__register_sidebars_action', 11 );

/**
 * Register widget area.
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function __register_sidebars_action(): void {

	//----------------------------------------------------------
	// Homepage
	//----------------------------------------------------------

	register_sidebar(
		[
			'container'     => false,
			'id'            => 'hd-home-sidebar',
			'name'          => __( 'Homepage', TEXT_DOMAIN ),
			'description'   => __( 'Widgets added here will appear in homepage.', TEXT_DOMAIN ),
			'before_widget' => '<div class="%2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<span>',
			'after_title'   => '</span>',
		]
	);

	//----------------------------------------------------------
	// Header
	//----------------------------------------------------------

	$top_header_cols    = (int) Helper::getThemeMod( 'top_header_setting' );
	$header_cols        = (int) Helper::getThemeMod( 'header_setting' );
	$bottom_header_cols = (int) Helper::getThemeMod( 'bottom_header_setting' );

	if ( $top_header_cols > 0 ) {
		for ( $i = 1; $i <= $top_header_cols; $i ++ ) {
			$_name = sprintf( __( 'Top-Header %d', TEXT_DOMAIN ), $i );
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'hd-top-header-' . $i . '-sidebar',
					'name'          => $_name,
					'description'   => __( 'Widgets added here will appear in top header.', TEXT_DOMAIN ),
					'before_widget' => '<div class="header-widgets %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);
		}
	}

	if ( $header_cols > 0 ) {
		for ( $i = 1; $i <= $header_cols; $i ++ ) {
			$_name = sprintf( __( 'Header %d', TEXT_DOMAIN ), $i );
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'hd-header-' . $i . '-sidebar',
					'name'          => $_name,
					'description'   => __( 'Widgets added here will appear in header.', TEXT_DOMAIN ),
					'before_widget' => '<div class="header-widgets %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);
		}
	}

	if ( $bottom_header_cols > 0 ) {
		for ( $i = 1; $i <= $bottom_header_cols; $i ++ ) {
			$_name = sprintf( __( 'Bottom-Header %d', TEXT_DOMAIN ), $i );
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'hd-bottom-header-' . $i . '-sidebar',
					'name'          => $_name,
					'description'   => __( 'Widgets added here will appear in bottom header.', TEXT_DOMAIN ),
					'before_widget' => '<div class="header-widgets %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);
		}
	}

	//----------------------------------------------------------
	// Footer
	//----------------------------------------------------------

	$footer_args = [];

	$rows    = (int) Helper::getThemeMod( 'footer_row_setting' );
	$regions = (int) Helper::getThemeMod( 'footer_col_setting' );

	for ( $row = 1; $row <= $rows; $row ++ ) {
		for ( $region = 1; $region <= $regions; $region ++ ) {

			$footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
			$footer   = sprintf( 'footer_%d', $footer_n );

			if ( 1 === $rows ) {
				$footer_region_name        = sprintf( __( 'Footer-Column %1$d', TEXT_DOMAIN ), $region );
				$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', TEXT_DOMAIN ), $region );
			} else {
				$footer_region_name        = sprintf( __( 'Footer-Row %1$d - Column %2$d', TEXT_DOMAIN ), $row, $region );
				$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', TEXT_DOMAIN ), $region, $row );
			}

			$footer_args[ $footer ] = [
				'name'        => $footer_region_name,
				'id'          => sprintf( 'hd-footer-%d', $footer_n ),
				'description' => $footer_region_description,
			];
		}
	}

	foreach ( $footer_args as $args ) {
		$footer_tags = [
			'container'     => false,
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<p class="widget-title h6">',
			'after_title'   => '</p>',
		];

		register_sidebar( $args + $footer_tags );
	}

	//----------------------------------------------------------
	// Other ...
	//----------------------------------------------------------
}

// --------------------------------------------------

add_action( 'wp_default_scripts', '__wp_default_scripts_action' );

/**
 * @param $scripts
 *
 * @return void
 */
function __wp_default_scripts_action( $scripts ): void {
	if ( isset( $scripts->registered['jquery'] ) && ! is_admin() ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {

			// Check whether the script has any dependencies

			// remove jquery-migrate
			$script->deps = array_diff( $script->deps, [ 'jquery-migrate' ] );
		}
	}
}

// --------------------------------------------------

add_filter( 'body_class', '__body_classes_filter', 11, 1 );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes
 *
 * @return array
 */
function __body_classes_filter( array $classes ): array {

	// Check whether we're in the customizer preview.
	if ( is_customize_preview() ) {
		$classes[] = 'customizer-preview';
	}

	foreach ( $classes as $class ) {
		if (
			str_contains( $class, 'wp-custom-logo' )
			|| str_contains( $class, 'page-template-templates' )
			|| str_contains( $class, 'page-template-default' )
			|| str_contains( $class, 'no-customize-support' )
			|| str_contains( $class, 'page-id-' )
		) {
			$classes = array_diff( $classes, [ $class ] );
		}
	}

	$is_home_or_front_page = is_home() || is_front_page();
	if ( $is_home_or_front_page && Helper::is_woocommerce_active() ) {
		$classes[] = 'woocommerce';
	}

	// ...
	$classes[] = 'default-mode';

	return $classes;
}

// --------------------------------------------------

add_filter( 'post_class', '__post_classes_filter', 11, 1 );

/**
 * Adds custom classes to the array of post-classes.
 *
 * @param array $classes Classes for the post-element.
 *
 * @return array
 */
function __post_classes_filter( array $classes ): array {

	// remove_sticky_class
	if ( in_array( 'sticky', $classes, false ) ) {
		$classes   = array_diff( $classes, [ "sticky" ] );
		$classes[] = 'wp-sticky';
	}

	// remove 'tag-', 'category-' classes
	foreach ( $classes as $class ) {
		if ( str_contains( $class, 'tag-' )
		     || str_contains( $class, 'category-' )
		) {
			$classes = array_diff( $classes, [ $class ] );
		}
	}

	return $classes;
}

// --------------------------------------------------

add_filter( 'nav_menu_css_class', '__nav_menu_css_classes_filter', 11, 4 );

/**
 * add class to li in wp_nav_menu
 *
 * @param $classes
 * @param $menu_item
 * @param $args
 * @param $depth
 *
 * @return array
 */
function __nav_menu_css_classes_filter( $classes, $menu_item, $args, $depth ): array {
	if ( ! is_array( $classes ) ) {
		$classes = [];
	}

	// Remove 'menu-item-type-', 'menu-item-object-' classes
	foreach ( $classes as $class ) {
		if ( str_contains( $class, 'menu-item-type-' )
		     || str_contains( $class, 'menu-item-object-' )
		) {
			$classes = array_diff( $classes, [ $class ] );
		}
	}

	if ( 1 === $menu_item->current
	     || $menu_item->current_item_ancestor
	     || $menu_item->current_item_parent
	) {
		$classes[] = 'active';
	}

	// li_class
	// li_depth_class

	if ( $depth === 0 ) {
		if ( isset( $args->li_class ) ) {
			$classes[] = $args->li_class;
		}

		return $classes;
	}

	if ( isset( $args->li_depth_class ) ) {
		$classes[] = $args->li_depth_class;
	}

	return $classes;
}

// --------------------------------------------------

add_filter( 'nav_menu_link_attributes', '__nav_menu_link_attributes_filter', 11, 4 );

/**
 * add class to link in wp_nav_menu
 *
 * @param $atts
 * @param $menu_item
 * @param $args
 * @param $depth
 *
 * @return array
 */
function __nav_menu_link_attributes_filter( $atts, $menu_item, $args, $depth ): array {
	// link_class
	// link_depth_class

	if ( $depth === 0 ) {
		if ( property_exists( $args, 'link_class' ) ) {
			$atts['class'] = $args->link_class;
		}

		return $atts;
	}

	if ( property_exists( $args, 'link_depth_class' ) ) {
		$atts['class'] = $args->link_depth_class;
	}

	return $atts;
}

// --------------------------------------------------

add_filter( 'wp_insert_post_data', '__wp_insert_post_data_filter', 99 );

/**
 * Comment off default
 *
 * @param $data
 *
 * @return mixed
 */
function __wp_insert_post_data_filter( $data ): mixed {
	if ( $data['post_status'] === 'auto-draft' ) {
		// $data['comment_status'] = 0;
		$data['ping_status'] = 0;
	}

	return $data;
}

// --------------------------------------------------

add_filter( 'widget_tag_cloud_args', '__widget_tag_cloud_args_filter', 99 );

/**
 * Tags cloud font sizes
 *
 * @param $args
 *
 * @return array
 */
function __widget_tag_cloud_args_filter( $args ): array {
	$args['smallest'] = '10';
	$args['largest']  = '19';
	$args['unit']     = 'px';
	$args['number']   = 12;

	return $args;
}

// --------------------------------------------------
// custom filter
// --------------------------------------------------

add_filter( 'hd_theme_setting_options', '__hd_theme_setting_options', 99 );

/**
 * @param array $arr
 *
 * @return array
 */
function __hd_theme_setting_options( array $arr ): array {
	$arr_new = [

		// defer, delay script - default 5s
		'defer_script'                    => [

			// defer
			'contact-form-7'       => 'defer',
			'swv'                  => 'defer',
			'hoverintent-js'       => 'defer',
			'wc-single-product'    => 'defer',
			'sourcebuster-js'      => 'defer',
			'wc-order-attribution' => 'defer',

			// delay
			'comment-reply'        => 'delay',
			'wp-embed'             => 'delay',
			'admin-bar'            => 'delay',
			'back-to-top'          => 'delay',
			'social-share'         => 'delay',
		],

		// defer style
		'defer_style'                     => [
			'dashicons',
			'admin-bar',
			'contact-form-7',
		],

		// Aspect Ratio default
		'aspect_ratio_css'                => [
			'1-1',
			'3-2',
			'4-3',
			'16-9',
		],

		// Aspect Ratio - custom post-type
		'aspect_ratio_post_type'          => [
			'post',
		],

		// Add ID to admin category page
		'term_row_actions'                => [
			'category',
			'post_tag',
		],

		// Add ID to admin post-page
		'post_row_actions'                => [
			'user',
			'post',
			'page',
		],

		// Terms thumbnail (term_thumb)
		'term_thumb_columns'              => [
			'category',
			'post_tag',
		],

		// Exclude thumb post_type columns
		'post_type_exclude_thumb_columns' => [],

		// ACF attributes in menu locations
		'acf_menu_items_locations'        => [
			'main-nav',
		],

		// ACF attributes 'mega menu' locations
		'acf_mega_menu_locations'         => [
			'main-nav',
		],

		// Custom post_per_page
		'posts_num_per_page'              => [],

		// Custom post-type & taxonomy
		'post_type_terms'                 => [],

		// smtp_plugins_support
		'smtp_plugins_support'            => [
			'wp_mail_smtp'     => 'wp-mail-smtp/wp_mail_smtp.php',
			'wp_mail_smtp_pro' => 'wp-mail-smtp-pro/wp_mail_smtp.php',
			'smtp_mailer'      => 'smtp-mailer/main.php',
			'gmail_smtp'       => 'gmail-smtp/main.php',
		],

		// Custom Email list
		'custom_emails'                   => [],

		// lazy_load_exclude
		'lazy_load_exclude'               => [
			'no-lazy',
			'skip-lazy',
		],

		// The urls where a lazy load is excluded.
		'lazy_load_exclude_urls'          => [],

		// reCAPTCHA forms
		'recaptcha_custom_forms'          => [],

		// reCAPTCHA exclude users
		'recaptcha_exclude_user'          => [],

		// Links socials
		'social_follows_links'            => [
			'facebook'  => [
				'name'  => 'Facebook',
				'icon'  => '<svg viewBox="0 0 36 36" style="color:#0866FF" fill="currentColor" height="40" width="40"><path d="M20.181 35.87C29.094 34.791 36 27.202 36 18c0-9.941-8.059-18-18-18S0 8.059 0 18c0 8.442 5.811 15.526 13.652 17.471L14 34h5.5l.681 1.87Z"></path><path style="fill:#ffffff" d="M13.651 35.471v-11.97H9.936V18h3.715v-2.37c0-6.127 2.772-8.964 8.784-8.964 1.138 0 3.103.223 3.91.446v4.983c-.425-.043-1.167-.065-2.081-.065-2.952 0-4.09 1.116-4.09 4.025V18h5.883l-1.008 5.5h-4.867v12.37a18.183 18.183 0 0 1-6.53-.399Z"></path></svg>',
				'color' => '#0866FF',
				'url'   => 'https://www.facebook.com',
			],
			'instagram' => [
				'name'  => 'Instagram',
				'icon'  => 'fa-brands fa-instagram',
				'color' => 'rgb(224, 241, 255)',
				'url'   => 'https://www.instagram.com',
			],
			'youtube'   => [
				'name'  => 'Youtube',
				'icon'  => 'fa-brands fa-youtube',
				'color' => 'rgb(255, 0, 0)',
				'url'   => 'https://www.youtube.com',
			],
			'twitter'   => [
				'name'  => 'X (Twitter)',
				'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><g><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></g></svg>',
				'color' => 'rgb(239, 243, 244)',
				'url'   => 'https://twitter.com',
			],
			'tiktok'    => [
				'name'  => 'Tiktok',
				'icon'  => 'fa-brands fa-tiktok',
				'color' => 'rgba(255, 255, 255, 0.9)',
				'url'   => 'https://www.tiktok.com',
			],
			'telegram'  => [
				'name'  => 'Telegram',
				'icon'  => 'fa-brands fa-telegram',
				'color' => '#2BA0E5',
				'url'   => 'https://telegram.org',
			],
			'zalo'      => [
				'name'  => 'Zalo',
				'icon'  => THEME_URL . 'storage/img/zlogo.png',
				'color' => '#0068FF',
				'url'   => 'https://chat.zalo.me/?phone=xxx',
			],
			'skype'     => [
				'name'  => 'Skype',
				'icon'  => 'fa-brands fa-skype',
				'color' => '#0092E0',
				'url'   => 'https://www.skype.com',
			],
			'hotline'   => [
				'name'  => 'Hotline',
				'icon'  => 'fa-solid fa-phone',
				'color' => '',
				'url'   => '',
			],
			'email'     => [
				'name'  => 'Email',
				'icon'  => 'fa-solid fa-envelope',
				'color' => '',
				'url'   => '',
			],
		],

		//----------------------------------------------------------
		// Custom ...
		//----------------------------------------------------------
	];

	// --------------------------------------------------

	if ( Helper::is_woocommerce_active() ) {
		$arr_new['aspect_ratio_post_type'][]          = 'product';
		$arr_new['term_row_actions'][]                = 'product_cat';
		$arr_new['post_type_exclude_thumb_columns'][] = 'product';
		$arr_new['post_type_terms'][]                 = [ 'product' => 'product_cat' ];
	}

	if ( Helper::is_contact_form_7_active() ) {
		$arr_new['post_type_exclude_thumb_columns'][] = 'wpcf7_contact_form';
	}

	return array_merge( $arr, $arr_new );
}
