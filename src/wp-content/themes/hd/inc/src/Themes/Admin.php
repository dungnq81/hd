<?php

namespace Themes;

use Addons\Base_Slug\Base_Slug;
use Addons\Custom_Order\Custom_Order;

use Cores\Helper;

use Libs\Optimizer\Bs_Cache;
use Libs\Optimizer\Gzip;
use Libs\Optimizer\Ssl;

use Libs\Security\Dir;
use Libs\Security\Headers;
use Libs\Security\Readme;
use Libs\Security\Xmlrpc;

/**
 * Options Class
 *
 * @author HD
 */

\defined( 'ABSPATH' ) || die;

final class Admin {

	// --------------------------------------------------

	public function __construct() {

		// editor-style.css
		add_action( 'enqueue_block_editor_assets', [ &$this, 'enqueue_block_editor_assets' ] );

		// admin.js, admin.css & codemirror_settings, v.v...
		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 9999, 1 );

		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
		add_action( 'admin_init', [ &$this, 'admin_init' ], 11 );

		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', [ &$this, 'options_reorder_submenu' ] );

		// ajax for settings
		add_action( 'wp_ajax_submit_settings', [ &$this, 'ajax_submit_settings' ] );
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function ajax_submit_settings(): void {
		check_ajax_referer( '_wpnonce_hd_settings_' . get_current_user_id() );
		$data = $_POST['_data'] ?? [];

		/** ---------------------------------------- */

		/** Aspect Ratio */
		$aspect_ratio_options   = [];
		$aspect_ratio_post_type = Helper::filter_setting_options( 'aspect_ratio_post_type', [] );

		foreach ( $aspect_ratio_post_type as $ar ) {
			$aspect_ratio_options[ 'ar-' . $ar . '-width' ]  = ! empty( $data[ $ar . '-width' ] ) ? sanitize_text_field( $data[ $ar . '-width' ] ) : 4;
			$aspect_ratio_options[ 'ar-' . $ar . '-height' ] = ! empty( $data[ $ar . '-height' ] ) ? sanitize_text_field( $data[ $ar . '-height' ] ) : 3;
		}

		Helper::updateOption( 'aspect_ratio__options', $aspect_ratio_options );

		/** ---------------------------------------- */

		/** SMTP Settings */
		if ( Helper::is_addons_active() && check_smtp_plugin_active() ) {

			$smtp_host     = ! empty( $data['smtp_host'] ) ? sanitize_text_field( $data['smtp_host'] ) : '';
			$smtp_auth     = ! empty( $data['smtp_auth'] ) ? sanitize_text_field( $data['smtp_auth'] ) : '';
			$smtp_username = ! empty( $data['smtp_username'] ) ? sanitize_text_field( $data['smtp_username'] ) : '';

			if ( ! empty( $data['smtp_password'] ) ) {

				// This removes slash (automatically added by WordPress) from the password when apostrophe is present
				$smtp_password = base64_encode( wp_unslash( sanitize_text_field( $data['smtp_password'] ) ) );
			}

			$smtp_encryption               = ! empty( $data['smtp_encryption'] ) ? sanitize_text_field( $data['smtp_encryption'] ) : '';
			$smtp_port                     = ! empty( $data['smtp_port'] ) ? sanitize_text_field( $data['smtp_port'] ) : '';
			$smtp_from_email               = ! empty( $data['smtp_from_email'] ) ? sanitize_email( $data['smtp_from_email'] ) : '';
			$smtp_from_name                = ! empty( $data['smtp_from_name'] ) ? sanitize_text_field( $data['smtp_from_name'] ) : '';
			$smtp_disable_ssl_verification = ! empty( $data['smtp_disable_ssl_verification'] ) ? sanitize_text_field( $data['smtp_disable_ssl_verification'] ) : '';

			$smtp_options = [
				'smtp_host'                     => $smtp_host,
				'smtp_auth'                     => $smtp_auth,
				'smtp_username'                 => $smtp_username,
				'smtp_encryption'               => $smtp_encryption,
				'smtp_port'                     => $smtp_port,
				'smtp_from_email'               => $smtp_from_email,
				'smtp_from_name'                => $smtp_from_name,
				'smtp_disable_ssl_verification' => $smtp_disable_ssl_verification,
			];

			if ( ! empty( $smtp_password ) ) {
				$smtp_options['smtp_password'] = $smtp_password;
			}

			Helper::updateOption( 'smtp__options', $smtp_options, true );
		}

		/** ---------------------------------------- */

		/** Emails list */
		if ( Helper::is_addons_active() ) {
			$email_options = [];
			$custom_emails = Helper::filter_setting_options( 'custom_emails', [] );
			if ( $custom_emails ) {
				foreach ( $custom_emails as $i => $ar ) {
					$email_options[ $i ] = ! empty( $data[ $i . '_email' ] ) ? sanitize_text_field( $data[ $i . '_email' ] ) : '';
				}

				Helper::updateOption( 'emails__options', $email_options );
			}
		}

		/** ---------------------------------------- */

		/** Contact info */
		if ( Helper::is_addons_active() ) {
			$contact_info_options = [
				'hotline' => ! empty( $data['contact_info_hotline'] ) ? sanitize_text_field( $data['contact_info_hotline'] ) : '',
				'address' => ! empty( $data['contact_info_address'] ) ? sanitize_text_field( $data['contact_info_address'] ) : '',
				'phones'  => ! empty( $data['contact_info_phones'] ) ? sanitize_text_field( $data['contact_info_phones'] ) : '',
				'emails'  => ! empty( $data['contact_info_emails'] ) ? sanitize_text_field( $data['contact_info_emails'] ) : '',
			];

			Helper::updateOption( 'contact_info__options', $contact_info_options, true );

			$html_contact_info_others = $data['contact_info_others'] ?? '';
			Helper::updateCustomPost( $html_contact_info_others, 'html_others', 'text/html' );
		}

		/** ---------------------------------------- */

		/** Contact Button */
		if ( Helper::is_addons_active() ) {
			$contact_btn_options = [
				'contact_title'        => ! empty( $data['contact_title'] ) ? sanitize_text_field( $data['contact_title'] ) : '',
				'contact_url'          => ! empty( $data['contact_url'] ) ? sanitize_text_field( $data['contact_url'] ) : '',
				'contact_window'       => ! empty( $data['contact_window'] ) ? sanitize_text_field( $data['contact_window'] ) : '',
				'contact_waiting_time' => ! empty( $data['contact_waiting_time'] ) ? sanitize_text_field( $data['contact_waiting_time'] ) : '',
				'contact_show_repeat'  => ! empty( $data['contact_show_repeat'] ) ? sanitize_text_field( $data['contact_show_repeat'] ) : '',
			];

			Helper::updateOption( 'contact_btn__options', $contact_btn_options, true );

			$html_contact_popup_content = $data['contact_popup_content'] ?? '';
			Helper::updateCustomPost( $html_contact_popup_content, 'html_contact', 'text/html' );
		}

		/** ---------------------------------------- */

		/** Block editor */
		if ( Helper::is_addons_active() ) {
			$block_editor_options = [
				'use_widgets_block_editor_off'           => ! empty( $data['use_widgets_block_editor_off'] ) ? sanitize_text_field( $data['use_widgets_block_editor_off'] ) : '',
				'gutenberg_use_widgets_block_editor_off' => ! empty( $data['gutenberg_use_widgets_block_editor_off'] ) ? sanitize_text_field( $data['gutenberg_use_widgets_block_editor_off'] ) : '',
				'use_block_editor_for_post_type_off'     => ! empty( $data['use_block_editor_for_post_type_off'] ) ? sanitize_text_field( $data['use_block_editor_for_post_type_off'] ) : '',
				'block_style_off'                        => ! empty( $data['block_style_off'] ) ? sanitize_text_field( $data['block_style_off'] ) : '',
			];

			Helper::updateOption( 'block_editor__options', $block_editor_options, true );
		}

		/** ---------------------------------------- */

		/** Optimizer */
		$optimizer_options_old = Helper::getOption( 'optimizer__options' );
		$https_enforce_old     = $optimizer_options_old['https_enforce'] ?? 0;

		$exclude_lazyload = ! empty( $data['exclude_lazyload'] ) ? Helper::explode_multi( [ ',', ' ', PHP_EOL ], $data['exclude_lazyload'] ) : [ 'no-lazy' ];
		$font_preload     = ! empty( $data['font_preload'] ) ? Helper::explode_multi( [ ',', ' ', PHP_EOL ], $data['font_preload'] ) : [];
		$dns_prefetch     = ! empty( $data['dns_prefetch'] ) ? Helper::explode_multi( [ ',', ' ', PHP_EOL ], $data['dns_prefetch'] ) : [];

		$exclude_lazyload = array_map( 'esc_textarea', $exclude_lazyload );
		$font_preload     = array_map( 'sanitize_url', $font_preload );
		$dns_prefetch     = array_map( 'sanitize_url', $dns_prefetch );

		$optimizer_options = [
			'https_enforce'    => ! empty( $data['https_enforce'] ) ? sanitize_text_field( $data['https_enforce'] ) : 0,
			'gzip'             => ! empty( $data['gzip'] ) ? sanitize_text_field( $data['gzip'] ) : 0,
			'bs_caching'       => ! empty( $data['bs_caching'] ) ? sanitize_text_field( $data['bs_caching'] ) : 0,
			'heartbeat'        => ! empty( $data['heartbeat'] ) ? sanitize_text_field( $data['heartbeat'] ) : 0,
			'minify_html'      => ! empty( $data['minify_html'] ) ? sanitize_text_field( $data['minify_html'] ) : 0,
			'svgs'             => ! empty( $data['svgs'] ) ? sanitize_text_field( $data['svgs'] ) : 'disable',
			'lazy_load'        => ! empty( $data['lazy_load'] ) ? sanitize_text_field( $data['lazy_load'] ) : 0,
			'lazy_load_mobile' => ! empty( $data['lazy_load_mobile'] ) ? sanitize_text_field( $data['lazy_load_mobile'] ) : 0,
			'exclude_lazyload' => $exclude_lazyload,
			'font_optimize'    => ! empty( $data['font_optimize'] ) ? sanitize_text_field( $data['font_optimize'] ) : 0,
			'font_preload'     => $font_preload,
			'dns_prefetch'     => $dns_prefetch,
		];

		Helper::updateOption( 'optimizer__options', $optimizer_options, true );

		// Ssl
		if ( $https_enforce_old !== $optimizer_options['https_enforce'] ) {
			( new Ssl() )->toggle_rules( $optimizer_options['https_enforce'] );
		}

		// Gzip + Caching
		( new Gzip() )->toggle_rules( $optimizer_options['gzip'] );
		( new Bs_Cache() )->toggle_rules( $optimizer_options['bs_caching'] );

		/** ---------------------------------------- */

		/** Security */
		$security_options = [
			'illegal_users'             => ! empty( $data['illegal_users'] ) ? sanitize_text_field( $data['illegal_users'] ) : '',
			'hide_wp_version'           => ! empty( $data['hide_wp_version'] ) ? sanitize_text_field( $data['hide_wp_version'] ) : '',
			'xml_rpc_off'               => ! empty( $data['xml_rpc_off'] ) ? sanitize_text_field( $data['xml_rpc_off'] ) : '',
			'remove_readme'             => ! empty( $data['remove_readme'] ) ? sanitize_text_field( $data['remove_readme'] ) : '',
			'rss_feed_off'              => ! empty( $data['rss_feed_off'] ) ? sanitize_text_field( $data['rss_feed_off'] ) : '',
			'lock_protect_system'       => ! empty( $data['lock_protect_system'] ) ? sanitize_text_field( $data['lock_protect_system'] ) : '',
			'advanced_xss_protection'   => ! empty( $data['advanced_xss_protection'] ) ? sanitize_text_field( $data['advanced_xss_protection'] ) : '',
			'limit_login_attempts'      => ! empty( $data['limit_login_attempts'] ) ? sanitize_text_field( $data['limit_login_attempts'] ) : '0',
			'two_factor_authentication' => ! empty( $data['two_factor_authentication'] ) ? sanitize_text_field( $data['two_factor_authentication'] ) : '',
		];

		Helper::updateOption( 'security__options', $security_options, true );

		// readme.html
		if ( $security_options['remove_readme'] ) {
			$readme = new Readme();
			$readme->delete_readme();
		}

		// toggle_rules
		( new Xmlrpc() )->toggle_rules( $security_options['xml_rpc_off'] );
		( new Dir() )->toggle_rules( $security_options['lock_protect_system'] );
		( new Headers() )->toggle_rules( $security_options['advanced_xss_protection'] );

		/** ---------------------------------------- */

		// Socials
		if ( Helper::is_addons_active() ) {
			$social_options       = [];
			$social_follows_links = Helper::filter_setting_options( 'social_follows_links', [] );

			foreach ( $social_follows_links as $i => $item ) {
				$social_options[ $i ] = [
					'url' => ! empty( $data[ $i . '-option' ] ) ? sanitize_url( $data[ $i . '-option' ] ) : '',
				];
			}

			Helper::updateOption( 'social__options', $social_options );
		}

		/** ---------------------------------------- */

		/** Woocommerce */
		if ( Helper::is_woocommerce_active() ) {
			global $wpdb;

			$woocommerce_options = [
				'remove_legacy_coupon'    => ! empty( $data['remove_legacy_coupon'] ) ? sanitize_text_field( $data['remove_legacy_coupon'] ) : '',
				'woocommerce_jsonld'      => ! empty( $data['woocommerce_jsonld'] ) ? sanitize_text_field( $data['woocommerce_jsonld'] ) : '',
				'woocommerce_default_css' => ! empty( $data['woocommerce_default_css'] ) ? sanitize_text_field( $data['woocommerce_default_css'] ) : '',
			];

			Helper::updateOption( 'woocommerce__options', $woocommerce_options, true );

			// fixed woo db
			if ( $woocommerce_options['remove_legacy_coupon'] ) {
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "wc_admin_notes SET status=%s WHERE name=%s", 'actioned', 'wc-admin-coupon-page-moved' ) );
				$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "wc_admin_note_actions SET status=%s WHERE name=%s", 'actioned', 'remove-legacy-coupon-menu' ) );
			}
		}

		/** ---------------------------------------- */

		/** Remove base slug */
		if ( Helper::is_addons_active() ) {
			$base_slug_reset = ! empty( $data['base_slug_reset'] ) ? sanitize_text_field( $data['base_slug_reset'] ) : '';

			if ( empty( $base_slug_reset ) ) {
				$custom_base_slug_options = [
					'base_slug_post_type' => ! empty( $data['base_slug_post_type'] ) ? array_map( 'sanitize_text_field', $data['base_slug_post_type'] ) : [],
					'base_slug_taxonomy'  => ! empty( $data['base_slug_taxonomy'] ) ? array_map( 'sanitize_text_field', $data['base_slug_taxonomy'] ) : [],
				];

				Helper::updateOption( 'custom_base_slug__options', $custom_base_slug_options );

				( new Base_Slug() )->flush_rules();

			} else {

				// reset order
				( new Base_Slug() )->reset_all();
			}
		}

		/** ---------------------------------------- */

		/** Custom Order */
		if ( Helper::is_addons_active() ) {
			$order_reset = ! empty( $data['order_reset'] ) ? sanitize_text_field( $data['order_reset'] ) : '';

			if ( empty( $order_reset ) ) {
				$custom_order_options = [
					'order_post_type' => ! empty( $data['order_post_type'] ) ? array_map( 'sanitize_text_field', $data['order_post_type'] ) : [],
					'order_taxonomy'  => ! empty( $data['order_taxonomy'] ) ? array_map( 'sanitize_text_field', $data['order_taxonomy'] ) : [],
				];

				Helper::updateOption( 'custom_order__options', $custom_order_options );

				( new Custom_Order() )->update_options();

			} else {
				( new Custom_Order() )->reset_all();
			}
		}

		/** ---------------------------------------- */

		/** reCAPTCHA */

		if ( Helper::is_addons_active() ) {
			$recaptcha_options = [
				'recaptcha_site_key'   => ! empty( $data['recaptcha_site_key'] ) ? sanitize_text_field( $data['recaptcha_site_key'] ) : '',
				'recaptcha_secret_key' => ! empty( $data['recaptcha_secret_key'] ) ? sanitize_text_field( $data['recaptcha_secret_key'] ) : '',
				'recaptcha_score'      => ! empty( $data['recaptcha_score'] ) ? sanitize_text_field( $data['recaptcha_score'] ) : '0.5',
				'recaptcha_global'     => ! empty( $data['recaptcha_global'] ) ? sanitize_text_field( $data['recaptcha_global'] ) : '',
			];

			Helper::updateOption( 'recaptcha__options', $recaptcha_options );
		}

		/** ---------------------------------------- */

		/** Comments */
//		$comment_options = [
//			'simple_antispam' => ! empty( $data['simple_antispam'] ) ? sanitize_text_field( $data['simple_antispam'] ) : '',
//		];
//
//		Helper::updateOption( 'comment__options', $comment_options, true );

		/** ---------------------------------------- */

		/** Custom Scripts */
		$html_header      = $data['html_header'] ?? '';
		$html_footer      = $data['html_footer'] ?? '';
		$html_body_top    = $data['html_body_top'] ?? '';
		$html_body_bottom = $data['html_body_bottom'] ?? '';

		Helper::updateCustomPost( $html_header, 'html_header', 'text/html', true );
		Helper::updateCustomPost( $html_footer, 'html_footer', 'text/html', true );
		Helper::updateCustomPost( $html_body_top, 'html_body_top', 'text/html', true );
		Helper::updateCustomPost( $html_body_bottom, 'html_body_bottom', 'text/html', true );

		/** ---------------------------------------- */

		/** Custom CSS */
		$html_custom_css = $data['html_custom_css'] ?? '';
		Helper::updateCustomCssPost( $html_custom_css, 'hd_css', false );

		/** ---------------------------------------- */

		Helper::clearAllCache();
		Helper::messageSuccess( __( 'Your settings have been saved.', ADDONS_TEXT_DOMAIN ), true );

		die();
	}

	// --------------------------------------------------

	/**
	 * Gutenberg editor
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets(): void {
		wp_enqueue_style( 'editor-style', THEME_URL . "assets/css/editor-style.css" );
	}

	// --------------------------------------------------

	/**
	 * @param $hook
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ): void {
		wp_enqueue_style( "admin-style", THEME_URL . "assets/css/admin.css", [], THEME_VERSION );

		wp_enqueue_script( 'pace-js', THEME_URL . 'assets/js/plugins/pace.min.js', [], false, true );
		wp_enqueue_script( "admin", THEME_URL . "assets/js/admin.js", [ "pace-js" ], THEME_VERSION, true );

		$pace_js_inline = 'paceOptions = {startOnPageLoad:!1}';
		wp_add_inline_script( 'pace-js', $pace_js_inline, 'before' );

		wp_enqueue_script( "fontawesome-kit", "https://kit.fontawesome.com/09f86c70cd.js", [], false, true );
		wp_script_add_data( "fontawesome-kit", "defer", true );

		// options_enqueue_assets
		$allowed_pages = 'toplevel_page_hd-settings';
		if ( $allowed_pages === $hook ) {
			$codemirror_settings = [
				'codemirror_css'  => wp_enqueue_code_editor( [ 'type' => 'text/css' ] ),
				'codemirror_html' => wp_enqueue_code_editor( [ 'type' => 'text/html' ] ),
			];

			wp_enqueue_style( 'wp-codemirror' );
			wp_localize_script( 'admin', 'codemirror_settings', $codemirror_settings );
		}
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function admin_menu(): void {

		//global $menu, $submenu;
		//dump($menu);
		//dump($submenu);

		$hide_menu = Helper::getThemeMod( 'remove_menu_setting' );
		if ( $hide_menu ) {
			foreach ( explode( "\n", $hide_menu ) as $menu_slug ) {
				if ( $menu_slug ) {
					remove_menu_page( $menu_slug );
				}
			}
		}

		// themes.php
//		if ( ! empty( $submenu['themes.php'] ) ) {
//            foreach ( $submenu['themes.php'] as $menu_key =>  $themes_menu ) {
//                if ( 'themes.php' == $themes_menu[2] ||
//                    'edit.php?post_type=wp_block' == $themes_menu[2]
//                ) {
//                    unset( $submenu['themes.php'][$menu_key] );
//                }
//            }
//		}

		/** ---------------------------------------- */

		add_menu_page(
			__( 'HD Settings', TEXT_DOMAIN ),
			__( 'HD', TEXT_DOMAIN ),
			'manage_options',
			'hd-settings',
			[ &$this, 'options_page' ],
			'dashicons-admin-settings',
			80
		);

		add_submenu_page( 'hd-settings', __( 'Advanced', TEXT_DOMAIN ), __( 'Advanced', TEXT_DOMAIN ), 'manage_options', 'customize.php' );
		add_submenu_page( 'hd-settings', __( 'Server Info', TEXT_DOMAIN ), __( 'Server Info', TEXT_DOMAIN ), 'manage_options', 'server-info', [
			&$this,
			'server_info'
		] );
	}

	// --------------------------------------------------

	/**
	 * Reorder the submenu pages.
	 *
	 * @param array $menu_order The WP menu order.
	 */
	public function options_reorder_submenu( array $menu_order ): array {

		// Load the global submenu.
		global $submenu;

		if ( empty( $submenu['hd-settings'] ) ) {
			return $menu_order;
		}

		// Change menu title
		$submenu['hd-settings'][0][0] = __( 'Settings', TEXT_DOMAIN );

		return $menu_order;
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function options_page(): void {

		?>
        <div class="wrap" id="hd_container">
            <form id="hd_form" method="post" enctype="multipart/form-data">

				<?php $nonce_field = wp_nonce_field( '_wpnonce_hd_settings_' . get_current_user_id() ); ?>

                <div id="main" class="filter-tabs clearfix">
                    <div id="hd_nav" class="tabs-nav">
                        <div class="logo-title">
                            <h3>
								<?php _e( 'HD Settings', TEXT_DOMAIN ); ?>
                                <span>Version: <?php echo THEME_VERSION; ?></span>
                            </h3>
                        </div>

                        <div class="save-bar">
                            <button type="submit" name="hd_submit_settings" class="button button-primary"><?php _e( 'Save Changes', TEXT_DOMAIN ); ?></button>
                        </div>

                        <ul class="ul-menu-list">
                            <li class="aspect-ratio-settings">
                                <a class="current" title="Aspect ratio" href="#aspect_ratio_settings"><?php _e( 'Aspect Ratio', TEXT_DOMAIN ); ?></a>
                            </li>

	                        <?php if ( Helper::is_addons_active() && check_smtp_plugin_active() ) : ?>
                                <li class="smtp-settings">
                                <a title="SMTP" href="#smtp_settings"><?php _e( 'SMTP', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php endif; ?>

	                        <?php if ( Helper::is_addons_active() ) : ?>
                                <li class="contact-info-settings">
                                <a title="Contact Info" href="#contact_info_settings"><?php _e( 'Contact Info', TEXT_DOMAIN ); ?></a>
                            </li>
                                <li class="contact-button-settings">
                                <a title="Contact Button" href="#contact_button_settings"><?php _e( 'Contact Button', TEXT_DOMAIN ); ?></a>
                            </li>
                                <li class="gutenberg-settings">
                                <a title="Editor" href="#block_editor_settings"><?php _e( 'Editor', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php endif; ?>

                            <li class="optimizer-settings">
                                <a title="Optimizer" href="#optimizer_settings"><?php _e( 'Optimizer', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="security-settings">
                                <a title="Security" href="#security_settings"><?php _e( 'Security', TEXT_DOMAIN ); ?></a>
                            </li>

	                        <?php if ( Helper::is_addons_active() ) : ?>
                                <li class="social-settings">
                                <a title="Social" href="#social_settings"><?php _e( 'Social', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php endif; ?>

	                        <?php if ( Helper::is_woocommerce_active() ) : ?>
                                <li class="woocommerce-settings">
                                <a title="WooCommerce" href="#woocommerce_settings"><?php _e( 'WooCommerce', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php endif; ?>

	                        <?php if ( Helper::is_addons_active() ) : ?>
                                <li class="base-slug-settings">
                                <a title="Remove base slug" href="#base_slug_settings"><?php _e( 'Remove Base Slug', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php
		                        $custom_emails = Helper::filter_setting_options( 'custom_emails', [] );
		                        if ( ! empty( $custom_emails ) ) :
			                        ?>
                                    <li class="email-settings">
                                <a title="EMAIL" href="#email_settings"><?php _e( 'Custom Email', TEXT_DOMAIN ); ?></a>
                            </li>
		                        <?php endif; ?>


                                <li class="order-settings">
                                <a title="Custom Order" href="#custom_order_settings"><?php _e( 'Custom Order', TEXT_DOMAIN ); ?></a>
                            </li>

                                <li class="recaptcha-settings">
                                <a title="reCAPTCHA" href="#recaptcha_settings"><?php _e( 'reCAPTCHA', TEXT_DOMAIN ); ?></a>
                            </li>
	                        <?php endif; ?>

                            <li class="comments-settings !hidden">
                                <a title="Comments" href="#comments_settings"><?php _e( 'Comments', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="custom-script-settings">
                                <a title="Custom Scripts" href="#custom_script_settings"><?php _e( 'Custom Scripts', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="custom-css-settings">
                                <a title="Custom CSS" href="#custom_css_settings"><?php _e( 'Custom CSS', TEXT_DOMAIN ); ?></a>
                            </li>
                        </ul>
                    </div>

                    <div id="hd_content" class="tabs-content">
                        <h2 class="hidden-text"></h2>

                        <div id="aspect_ratio_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/aspect_ratio.php'; ?>
                        </div>

	                    <?php if ( Helper::is_addons_active() && check_smtp_plugin_active() ) : ?>
                            <div id="smtp_settings" class="group tabs-panel">
							<?php include ADDONS_PATH . 'src/SMTP/options.php'; ?>
                        </div>
	                    <?php endif; ?>

	                    <?php if ( Helper::is_addons_active() ) : ?>
                            <div id="contact_info_settings" class="group tabs-panel">
							<?php include ADDONS_PATH . 'src/Contact_Info/options.php'; ?>
                        </div>
                            <div id="contact_button_settings" class="group tabs-panel">
							<?php include ADDONS_PATH . 'src/Contact_Button/options.php'; ?>
                        </div>
                            <div id="block_editor_settings" class="group tabs-panel">
							<?php include ADDONS_PATH . 'src/Editor/options.php'; ?>
                        </div>
	                    <?php endif; ?>

                        <div id="optimizer_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/optimizer.php'; ?>
                        </div>

                        <div id="security_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/security.php'; ?>
                        </div>

	                    <?php if ( Helper::is_addons_active() ) : ?>
                            <div id="social_settings" class="group tabs-panel">
							<?php include ADDONS_PATH . 'src/Social/options.php'; ?>
                        </div>
	                    <?php endif; ?>

	                    <?php if ( Helper::is_woocommerce_active() ) : ?>
                            <div id="woocommerce_settings" class="group tabs-panel">
                            <?php include INC_PATH . 'src/Plugins/WooCommerce/options.php'; ?>
                        </div>
	                    <?php endif; ?>

	                    <?php if ( Helper::is_addons_active() ) : ?>
                            <div id="base_slug_settings" class="group tabs-panel">
                            <?php include ADDONS_PATH . 'src/Base_Slug/options.php'; ?>
                        </div>

	                    <?php if ( ! empty( $custom_emails ) ) : ?>
                                <div id="email_settings" class="group tabs-panel">
                            <?php include ADDONS_PATH . 'src/Custom_Email/options.php'; ?>
                        </div>
		                    <?php endif; ?>


                            <div id="custom_order_settings" class="group tabs-panel">
		                    <?php include ADDONS_PATH . 'src/Custom_Order/options.php'; ?>
                        </div>

                            <div id="recaptcha_settings" class="group tabs-panel">
		                    <?php include ADDONS_PATH . 'src/reCAPTCHA/options.php'; ?>
                        </div>

	                    <?php endif; ?>

                        <div id="comments_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/comments.php'; ?>
                        </div>

                        <div id="custom_script_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/custom_script.php'; ?>
                        </div>

                        <div id="custom_css_settings" class="group tabs-panel">
							<?php include INC_PATH . 'admin/options/custom_css.php'; ?>
                        </div>

                        <div class="save-bar">
                            <button type="submit" name="hd_submit_settings" class="button button-primary"><?php _e( 'Save Changes', TEXT_DOMAIN ) ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
		<?php
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function server_info(): void {
		?>
        <div class="wrap">
            <div id="main">
                <h2 class="hide-text"></h2>
                <div class="server-info-body">
                    <h2><?php echo __( 'Server info', TEXT_DOMAIN ) ?></h2>
                    <p class="desc"><?php echo __( 'System configuration information', TEXT_DOMAIN ) ?></p>
                    <div class="server-info-inner code">
                        <ul>
                            <li><?php echo sprintf( '<span>Platform:</span> %s', php_uname() ); ?></li>

	                        <?php if ( $server_software = $_SERVER['SERVER_SOFTWARE'] ?? null ) : ?>
                                <li><?php echo sprintf( '<span>SERVER:</span> %s', $server_software ); ?></li>
	                        <?php endif; ?>

                            <li><?php echo sprintf( '<span>PHP version:</span> %s', PHP_VERSION ); ?></li>
                            <li><?php echo sprintf( '<span>WordPress version:</span> %s', get_bloginfo( 'version' ) ); ?></li>
                            <li><?php echo sprintf( '<span>WordPress multisite:</span> %s', ( is_multisite() ? 'Yes' : 'No' ) ); ?></li>
	                        <?php
	                        $openssl_status = 'Available';
	                        $openssl_text   = '';
	                        if ( ! defined( 'OPENSSL_ALGO_SHA1' ) &&
	                             ! extension_loaded( 'openssl' )
	                        ) {
		                        $openssl_status = 'Not available';
		                        $openssl_text   = ' (openssl extension is required in order to use any kind of encryption like TLS or SSL)';
	                        }
	                        ?>
                            <li><?php echo sprintf( '<span>openssl:</span> %s%s', $openssl_status, $openssl_text ); ?></li>
                            <li><?php echo sprintf( '<span>allow_url_fopen:</span> %s', ( ini_get( 'allow_url_fopen' ) ? 'Enabled' : 'Disabled' ) ); ?></li>
	                        <?php
	                        $stream_socket_client_status = 'Not Available';
	                        $fsockopen_status            = 'Not Available';
	                        $socket_enabled              = false;

	                        if ( function_exists( 'stream_socket_client' ) ) {
		                        $stream_socket_client_status = 'Available';
		                        $socket_enabled              = true;
	                        }
	                        if ( function_exists( 'fsockopen' ) ) {
		                        $fsockopen_status = 'Available';
		                        $socket_enabled   = true;
	                        }

	                        $socket_text = '';
	                        if ( ! $socket_enabled ) {
		                        $socket_text = ' (In order to make a SMTP connection your server needs to have either stream_socket_client or fsockopen)';
	                        }
	                        ?>
                            <li><?php echo sprintf( '<span>stream_socket_client:</span> %s', $stream_socket_client_status ); ?></li>
                            <li><?php echo sprintf( '<span>fsockopen:</span> %s%s', $fsockopen_status, $socket_text ); ?></li>

	                        <?php if ( $agent = $_SERVER['HTTP_USER_AGENT'] ?? null ) : ?>
                                <li><?php echo sprintf( '<span>User agent:</span> %s', $agent ); ?></li>
	                        <?php endif; ?>

                            <li><?php echo sprintf( '<span>IP:</span> %s', Helper::getIpAddress() ); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
	<?php }

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function admin_init(): void {

		// https://wordpress.stackexchange.com/questions/77532/how-to-add-the-category-id-to-admin-page
		$taxonomy_arr = [
			'category',
			'post_tag',
		];

		$taxonomy_arr = Helper::filter_setting_options( 'term_row_actions', $taxonomy_arr );

		foreach ( $taxonomy_arr as $term ) {
			add_filter( "{$term}_row_actions", [ &$this, 'term_action_links' ], 11, 2 );
		}

		// customize row_actions
		$post_type_arr = [
			'user',
			'post',
			'page',
		];

		$post_type_arr = Helper::filter_setting_options( 'post_row_actions', $post_type_arr );

		foreach ( $post_type_arr as $post_type ) {
			add_filter( "{$post_type}_row_actions", [ &$this, 'post_type_action_links' ], 11, 2 );
		}

		// customize post-page
		add_filter( 'manage_posts_columns', [ &$this, 'post_header' ], 11, 1 );
		add_filter( 'manage_posts_custom_column', [ &$this, 'post_column' ], 11, 2 );

		add_filter( 'manage_pages_columns', [ &$this, 'post_header' ], 5, 1 );
		add_filter( 'manage_pages_custom_column', [ &$this, 'post_column' ], 5, 2 );

		// exclude post columns
		$exclude_thumb_posts = [];
		$exclude_thumb_posts = Helper::filter_setting_options( 'post_type_exclude_thumb_columns', $exclude_thumb_posts );

		foreach ( $exclude_thumb_posts as $post ) {
			add_filter( "manage_{$post}_posts_columns", [ $this, 'post_exclude_header' ], 12, 1 );
		}

		// thumb terms
		$thumb_terms = [
			'category',
			'post_tag',
		];

		$thumb_terms = Helper::filter_setting_options( 'term_thumb_columns', $thumb_terms );

		foreach ( $thumb_terms as $term ) {
			add_filter( "manage_edit-{$term}_columns", [ &$this, 'term_header' ], 11, 1 );
			add_filter( "manage_{$term}_custom_column", [ &$this, 'term_column' ], 11, 3 );
		}
	}

	// --------------------------------------------------

	/**
	 * @param $columns
	 *
	 * @return array|mixed
	 */
	public function term_header( $columns ): mixed {
		if ( Helper::is_acf_active() ) {

			// thumb
			$thumb   = [
				"term_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", TEXT_DOMAIN ) ),
			];
			$columns = Helper::insertBefore( 'name', $columns, $thumb );

//			// order
//			$menu_order = [
//				'term_order' => sprintf( '<span class="term-order tips">%1$s</span>', __( "Order", TEXT_DOMAIN ) ),
//			];
//
//			$columns = array_merge( $columns, $menu_order );
		}

		return $columns;
	}

	// --------------------------------------------------

	/**
	 * @param $out
	 * @param $column
	 * @param $term_id
	 *
	 * @return int|mixed|string|null
	 */
	public function term_column( $out, $column, $term_id ): mixed {
		switch ( $column ) {
			case 'term_thumb':
				$term_thumb = Helper::acfTermThumb( $term_id, $column, "thumbnail", true );
				if ( ! $term_thumb ) {
					$term_thumb = Helper::placeholderSrc();
				}

				return $out = $term_thumb;
				break;

//			case 'term_order':
//				if ( class_exists( '\ACF' ) ) {
//					$term_order = \get_field( 'term_order', get_term( $term_id ) );
//
//					return $out = $term_order ?: 0;
//				}
//
//				return $out = 0;
//				break;

			default:
				return $out;
				break;
		}
	}

	// --------------------------------------------------

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function post_exclude_header( $columns ): mixed {
		unset( $columns['post_thumb'] );

		return $columns;
	}

	// --------------------------------------------------

	/**
	 * @param $columns
	 *
	 * @return array
	 */
	public function post_header( $columns ): array {
		$in = [
			"post_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", TEXT_DOMAIN ) ),
		];

		return Helper::insertBefore( 'title', $columns, $in );
	}

	// --------------------------------------------------

	/**
	 * @param $column_name
	 * @param $post_id
	 */
	public function post_column( $column_name, $post_id ): void {
		switch ( $column_name ) {
			case 'post_thumb':
				$post_type = get_post_type( $post_id );
				if ( ! in_array( $post_type, [ 'video', 'product' ] ) ) {
					if ( ! $thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' ) ) {
						$thumbnail = Helper::placeholderSrc();
					}
					echo $thumbnail;
				} else if ( 'video' === $post_type ) {
					if ( has_post_thumbnail( $post_id ) ) {
						echo get_the_post_thumbnail( $post_id, 'thumbnail' );
					} else if ( function_exists( 'get_field' ) && $url = \get_field( 'url', $post_id ) ) {
						$img_src = Helper::youtubeImage( esc_url( $url ), 3 );
						echo "<img loading=\"lazy\" alt=\"video\" src=\"" . $img_src . "\" />";
					}
				}

				break;

			default:
				break;
		}
	}

	// --------------------------------------------------

	/**
	 * @param $actions
	 * @param $_object
	 *
	 * @return mixed
	 */
	public function post_type_action_links( $actions, $_object ): mixed {
		if ( ! in_array( $_object->post_type, [ 'product', 'site-review' ] ) ) {
			Helper::prepend( $actions, 'Id:' . $_object->ID, 'action_id' );
		}

		return $actions;
	}

	// --------------------------------------------------

	/**
	 * @param $actions
	 * @param $_object
	 *
	 * @return mixed
	 */
	public function term_action_links( $actions, $_object ): mixed {
		Helper::prepend( $actions, 'Id: ' . $_object->term_id, 'action_id' );

		return $actions;
	}
}
