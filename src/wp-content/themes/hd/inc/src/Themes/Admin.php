<?php

namespace Themes;

use Addons\Custom_Order\Custom_Order;

use Cores\Helper;

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
	public function __construct() {

        // editor-style.css
		add_action( 'enqueue_block_editor_assets', [ &$this, 'enqueue_block_editor_assets' ] );

        // admin.js, admin.css & codemirror_settings, v.v...
		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 9999, 1 );

		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
		add_action( 'admin_init', [ &$this, 'admin_init' ], 11 );

		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', [ &$this, 'options_reorder_submenu' ] );
	}

	/** ---------------------------------------- */

	/**
	 * Gutenberg editor
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets(): void {
		wp_enqueue_style( 'editor-style', THEME_URL . "assets/css/editor-style.css" );
	}

	/** ---------------------------------------- */

	/**
	 * @param $hook
	 * 
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ): void {
		wp_enqueue_style( "admin-style", THEME_URL . "assets/css/admin.css", [], THEME_VERSION );
		wp_enqueue_script( "admin", THEME_URL . "assets/js/admin.js", [ "jquery" ], THEME_VERSION, true );

		// options_enqueue_assets
		$allowed_pages = [
			'toplevel_page_hd-settings',
		];

		if ( in_array( $hook, $allowed_pages ) ) {
			$codemirror_settings = [
				'codemirror_css'  => wp_enqueue_code_editor( [ 'type' => 'text/css' ] ),
				'codemirror_html' => wp_enqueue_code_editor( [ 'type' => 'text/html' ] ),
			];

			wp_enqueue_style( 'wp-codemirror' );
			wp_localize_script( 'admin', 'codemirror_settings', $codemirror_settings );
		}
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_menu(): void {

		global $menu, $submenu;
		//dump($menu);
		//dump($submenu);

		// Hide menu
		$hide_menu = Helper::getThemeMod( 'remove_menu_setting' );
		if ( $hide_menu ) {
			$array_hide_menu = explode( "\n", $hide_menu );
			foreach ( $array_hide_menu as $menu_slug ) {
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
		/** ---------------------------------------- */

		// menu page
		add_menu_page(
			__( 'HD Settings', TEXT_DOMAIN ),
			__( 'HD', TEXT_DOMAIN ),
			'manage_options',
			'hd-settings',
			[ &$this, 'options_page' ],
			'dashicons-admin-settings',
			80
		);

		// submenu page
		add_submenu_page( 'hd-settings', __( 'Advanced', TEXT_DOMAIN ), __( 'Advanced', TEXT_DOMAIN ), 'manage_options', 'customize.php' );
		add_submenu_page( 'hd-settings', __( 'Server Info', TEXT_DOMAIN ), __( 'Server Info', TEXT_DOMAIN ), 'manage_options', 'server-info', [
			&$this,
			'server_info'
		] );
	}

	/** ---------------------------------------- */

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

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function options_page(): void {
		global $wpdb;

		if ( isset( $_POST['hd_submit_settings'] ) ) {

			check_admin_referer( '_wpnonce_hd_settings' );

			// ------------------------------------------------------

			/** Aspect Ratio */

			$aspect_ratio_options = [];
			$ar_post_type_list    = apply_filters( 'hd_aspect_ratio_post_type', [] );
			foreach ( $ar_post_type_list as $i => $ar ) {
				$aspect_ratio_options[ 'ar-' . $ar . '-width' ]  = ! empty( $_POST[ $ar . '-width' ] ) ? sanitize_text_field( $_POST[ $ar . '-width' ] ) : 4;
				$aspect_ratio_options[ 'ar-' . $ar . '-height' ] = ! empty( $_POST[ $ar . '-height' ] ) ? sanitize_text_field( $_POST[ $ar . '-height' ] ) : 3;
			}

			Helper::updateOption( 'aspect_ratio__options', $aspect_ratio_options );

			// ------------------------------------------------------

			/** SMTP Settings */

            if ( Helper::is_addons_active() && check_smtp_plugin_active() ) {

				$smtp_host     = ! empty( $_POST['smtp_host'] ) ? sanitize_text_field( $_POST['smtp_host'] ) : '';
				$smtp_auth     = ! empty( $_POST['smtp_auth'] ) ? sanitize_text_field( $_POST['smtp_auth'] ) : '';
				$smtp_username = ! empty( $_POST['smtp_username'] ) ? sanitize_text_field( $_POST['smtp_username'] ) : '';

				if ( ! empty( $_POST['smtp_password'] ) ) {
					$smtp_password = sanitize_text_field( $_POST['smtp_password'] );
					$smtp_password = wp_unslash( $smtp_password ); // This removes slash (automatically added by WordPress) from the password when apostrophe is present
					$smtp_password = base64_encode( $smtp_password );
				}

				$smtp_encryption               = ! empty( $_POST['smtp_encryption'] ) ? sanitize_text_field( $_POST['smtp_encryption'] ) : '';
				$smtp_port                     = ! empty( $_POST['smtp_port'] ) ? sanitize_text_field( $_POST['smtp_port'] ) : '';
				$smtp_from_email               = ! empty( $_POST['smtp_from_email'] ) ? sanitize_email( $_POST['smtp_from_email'] ) : '';
				$smtp_from_name                = ! empty( $_POST['smtp_from_name'] ) ? sanitize_text_field( $_POST['smtp_from_name'] ) : '';
				$smtp_disable_ssl_verification = ! empty( $_POST['smtp_disable_ssl_verification'] ) ? sanitize_text_field( $_POST['smtp_disable_ssl_verification'] ) : '';

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

			// ------------------------------------------------------

			/** Emails list */

			$email_options = [];
			$hd_email_list = apply_filters( 'hd_email_list', [] );

            if ( $hd_email_list ) {
	            foreach ( $hd_email_list as $i => $ar ) {
		            $email_options[ $i ] = ! empty( $_POST[ $i . '_email' ] ) ? sanitize_text_field( $_POST[ $i . '_email' ] ) : '';
	            }

	            Helper::updateOption( 'emails__options', $email_options );
            }

			// ------------------------------------------------------

			/** Contact info */

			$contact_info_options = [
				'hotline' => ! empty( $_POST['contact_info_hotline'] ) ? sanitize_text_field( $_POST['contact_info_hotline'] ) : '',
				'address' => ! empty( $_POST['contact_info_address'] ) ? sanitize_text_field( $_POST['contact_info_address'] ) : '',
				'phones'  => ! empty( $_POST['contact_info_phones'] ) ? sanitize_text_field( $_POST['contact_info_phones'] ) : '',
				'emails'  => ! empty( $_POST['contact_info_emails'] ) ? sanitize_text_field( $_POST['contact_info_emails'] ) : '',
			];

			Helper::updateOption( 'contact_info__options', $contact_info_options, true );

			$html_contact_info_others = $_POST['contact_info_others'] ?? '';
			Helper::updateCustomPost( $html_contact_info_others, 'html_others', 'text/html' );

			// ------------------------------------------------------

			/** Custom Order */

            if ( Helper::is_addons_active() ) {

	            $order_reset = ! empty( $_POST['order_reset'] ) ? sanitize_text_field( $_POST['order_reset'] ) : '';

	            if ( empty( $order_reset ) ) {
		            $custom_order_options = [
			            'order_post_type' => ! empty( $_POST['order_post_type'] ) ? array_map( 'sanitize_text_field', $_POST['order_post_type'] ) : [],
			            'order_taxonomy'  => ! empty( $_POST['order_taxonomy'] ) ? array_map( 'sanitize_text_field', $_POST['order_taxonomy'] ) : [],
		            ];

		            Helper::updateOption( 'custom_order__options', $custom_order_options );

		            // update options
		            ( new Custom_Order() )->update_options();

	            } else {

		            // reset order
		            ( new Custom_Order() )->reset_all();
	            }
            }

			// ------------------------------------------------------

			/** Contact Button */

			$contact_btn_options = [
				'contact_title'        => ! empty( $_POST['contact_title'] ) ? sanitize_text_field( $_POST['contact_title'] ) : '',
				'contact_url'          => ! empty( $_POST['contact_url'] ) ? sanitize_text_field( $_POST['contact_url'] ) : '',
				'contact_window'       => ! empty( $_POST['contact_window'] ) ? sanitize_text_field( $_POST['contact_window'] ) : '',
				'contact_waiting_time' => ! empty( $_POST['contact_waiting_time'] ) ? sanitize_text_field( $_POST['contact_waiting_time'] ) : '',
				'contact_show_repeat'  => ! empty( $_POST['contact_show_repeat'] ) ? sanitize_text_field( $_POST['contact_show_repeat'] ) : '',
			];

			Helper::updateOption( 'contact_btn__options', $contact_btn_options, true );

			$html_contact_popup_content = $_POST['contact_popup_content'] ?? '';
			Helper::updateCustomPost( $html_contact_popup_content, 'html_contact', 'text/html' );

			// ------------------------------------------------------

			/** Block editor */

			$block_editor_options = [
				'use_widgets_block_editor_off'           => ! empty( $_POST['use_widgets_block_editor_off'] ) ? sanitize_text_field( $_POST['use_widgets_block_editor_off'] ) : '',
				'gutenberg_use_widgets_block_editor_off' => ! empty( $_POST['gutenberg_use_widgets_block_editor_off'] ) ? sanitize_text_field( $_POST['gutenberg_use_widgets_block_editor_off'] ) : '',
				'use_block_editor_for_post_type_off'     => ! empty( $_POST['use_block_editor_for_post_type_off'] ) ? sanitize_text_field( $_POST['use_block_editor_for_post_type_off'] ) : '',
				'block_style_off'                        => ! empty( $_POST['block_style_off'] ) ? sanitize_text_field( $_POST['block_style_off'] ) : '',
			];

			Helper::updateOption( 'block_editor__options', $block_editor_options, true );

			// ------------------------------------------------------

			/** Optimizer */

			$optimizer_options_old = Helper::getOption( 'optimizer__options', false, false );
			$https_enforce_old     = $optimizer_options_old['https_enforce'] ?? 0;

			$optimizer_options = [
				'https_enforce' => ! empty( $_POST['https_enforce'] ) ? sanitize_text_field( $_POST['https_enforce'] ) : 0,
				'svgs'          => ! empty( $_POST['svgs'] ) ? sanitize_text_field( $_POST['svgs'] ) : 'disable',
			];

			Helper::updateOption( 'optimizer__options', $optimizer_options, true );

			// Ssl
			if ( $https_enforce_old != $optimizer_options['https_enforce'] ) {
				$ssl = new Ssl();
				$ssl->toggle_rules( $optimizer_options['https_enforce'] );
			}

			// ------------------------------------------------------

			/** Security */

			$security_options = [
				'illegal_users'             => ! empty( $_POST['illegal_users'] ) ? sanitize_text_field( $_POST['illegal_users'] ) : '',
				'hide_wp_version'           => ! empty( $_POST['hide_wp_version'] ) ? sanitize_text_field( $_POST['hide_wp_version'] ) : '',
				'xml_rpc_off'               => ! empty( $_POST['xml_rpc_off'] ) ? sanitize_text_field( $_POST['xml_rpc_off'] ) : '',
				'remove_readme'             => ! empty( $_POST['remove_readme'] ) ? sanitize_text_field( $_POST['remove_readme'] ) : '',
				'rss_feed_off'              => ! empty( $_POST['rss_feed_off'] ) ? sanitize_text_field( $_POST['rss_feed_off'] ) : '',
				'lock_protect_system'       => ! empty( $_POST['lock_protect_system'] ) ? sanitize_text_field( $_POST['lock_protect_system'] ) : '',
				'advanced_xss_protection'   => ! empty( $_POST['advanced_xss_protection'] ) ? sanitize_text_field( $_POST['advanced_xss_protection'] ) : '',
				'limit_login_attempts'      => ! empty( $_POST['limit_login_attempts'] ) ? sanitize_text_field( $_POST['limit_login_attempts'] ) : '0',
				'two_factor_authentication' => ! empty( $_POST['two_factor_authentication'] ) ? sanitize_text_field( $_POST['two_factor_authentication'] ) : '',
			];

			Helper::updateOption( 'security__options', $security_options, true );

			// readme.html
			if ( $security_options['remove_readme'] ) {
				$readme = new Readme();
				$readme->delete_readme();
			}

			// xml-rpc
			$xml_rpc = new Xmlrpc();
			$xml_rpc->toggle_rules( $security_options['xml_rpc_off'] );

			// system protect
			$protect_system = new Dir();
			$protect_system->toggle_rules( $security_options['lock_protect_system'] );

			// xss protection
			$xss_protection = new Headers();
			$xss_protection->toggle_rules( $security_options['advanced_xss_protection'] );

			// ------------------------------------------------------

			/** Woocommerce */

			if ( Helper::is_woocommerce_active() ) {

				$woocommerce_options = [
					'remove_legacy_coupon' => ! empty( $_POST['remove_legacy_coupon'] ) ? sanitize_text_field( $_POST['remove_legacy_coupon'] ) : '',
					'woocommerce_jsonld'   => ! empty( $_POST['woocommerce_jsonld'] ) ? sanitize_text_field( $_POST['woocommerce_jsonld'] ) : '',
				];

				Helper::updateOption( 'woocommerce__options', $woocommerce_options, true );

				// fixed woo db
				if ( $woocommerce_options['remove_legacy_coupon'] ) {
					$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "wc_admin_notes SET status=%s WHERE name=%s", 'actioned', 'wc-admin-coupon-page-moved' ) );
					$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "wc_admin_note_actions SET status=%s WHERE name=%s", 'actioned', 'remove-legacy-coupon-menu' ) );
				}
			}

			// ------------------------------------------------------

			/** Comments */

			$comment_options = [
				'simple_antispam' => ! empty( $_POST['simple_antispam'] ) ? sanitize_text_field( $_POST['simple_antispam'] ) : '',
			];

			Helper::updateOption( 'comment__options', $comment_options, true );

			// ------------------------------------------------------

			/** Custom Scripts */

			$html_header      = $_POST['html_header'] ?? '';
			$html_footer      = $_POST['html_footer'] ?? '';
			$html_body_top    = $_POST['html_body_top'] ?? '';
			$html_body_bottom = $_POST['html_body_bottom'] ?? '';

			Helper::updateCustomPost( $html_header, 'html_header', 'text/html', true );
			Helper::updateCustomPost( $html_footer, 'html_footer', 'text/html', true );
			Helper::updateCustomPost( $html_body_top, 'html_body_top', 'text/html', true );
			Helper::updateCustomPost( $html_body_bottom, 'html_body_bottom', 'text/html', true );

			// ------------------------------------------------------

			/** Custom CSS */

			$html_custom_css = $_POST['html_custom_css'] ?? '';
			Helper::updateCustomCssPost( $html_custom_css, 'hd_css', false );

			// ------------------------------------------------------

			/** Echo message success */

			Helper::messageSuccess( 'Settings saved' );

			// Clear LiteSpeed cache, if existing.
			if ( class_exists( '\LiteSpeed\Purge' ) ) {
				\LiteSpeed\Purge::purge_all();
			}

			// Clear wp-rocket cache
			if ( \defined( 'WP_ROCKET_VERSION' ) && \function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}

		?>
        <div class="wrap" id="hd_container">
            <form id="hd_form" method="post" enctype="multipart/form-data">

				<?php wp_nonce_field( '_wpnonce_hd_settings' ); ?>

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

                            <?php
                            $hd_email_list = apply_filters( 'hd_email_list', [] );
		                    if ( ! empty( $hd_email_list ) ) :
                            ?>
                            <li class="email-settings">
                                <a title="EMAIL" href="#email_settings"><?php _e( 'Custom Email', TEXT_DOMAIN ); ?></a>
                            </li>
                            <?php endif; ?>

                            <?php if ( Helper::is_addons_active() ) : ?>
                            <li class="order-settings">
                                <a title="Custom Order" href="#custom_order_settings"><?php _e( 'Custom Order', TEXT_DOMAIN ); ?></a>
                            </li>
                            <?php endif;?>

                            <li class="contact-info-settings">
                                <a title="Contact Info" href="#contact_info_settings"><?php _e( 'Contact Info', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="contact-button-settings">
                                <a title="Contact Button" href="#contact_button_settings"><?php _e( 'Contact Button', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="gutenberg-settings">
                                <a title="Block Editor" href="#block_editor_settings"><?php _e( 'Block Editor', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="optimizer-settings">
                                <a title="Optimizer" href="#optimizer_settings"><?php _e( 'Optimizer', TEXT_DOMAIN ); ?></a>
                            </li>
                            <li class="security-settings">
                                <a title="Security" href="#security_settings"><?php _e( 'Security', TEXT_DOMAIN ); ?></a>
                            </li>

							<?php if ( Helper::is_woocommerce_active() ) : ?>
                            <li class="woocommerce-settings">
                                <a title="WooCommerce" href="#woocommerce_settings"><?php _e( 'WooCommerce', TEXT_DOMAIN ); ?></a>
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
							<?php require INC_PATH . 'admin/options/aspect_ratio.php'; ?>
                        </div>

		                <?php if ( Helper::is_addons_active() && check_smtp_plugin_active() ) : ?>
                        <div id="smtp_settings" class="group tabs-panel">
							<?php require ADDONS_PATH . 'src/SMTP/options.php'; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $hd_email_list ) ) : ?>
                        <div id="email_settings" class="group tabs-panel">
		                    <?php require INC_PATH . 'admin/options/custom_email.php'; ?>
                        </div>
                        <?php endif; ?>

		                <?php if ( Helper::is_addons_active() ) : ?>
                        <div id="custom_order_settings" class="group tabs-panel">
		                    <?php require ADDONS_PATH . 'src/Custom_Order/options.php'; ?>
                        </div>
		                <?php endif; ?>

                        <div id="contact_info_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/contact_info.php'; ?>
                        </div>

                        <div id="contact_button_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/contact_button.php'; ?>
                        </div>

                        <div id="block_editor_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/block_editor.php'; ?>
                        </div>

                        <div id="optimizer_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/optimizer.php'; ?>
                        </div>

                        <div id="security_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/security.php'; ?>
                        </div>

						<?php if ( Helper::is_woocommerce_active() ) : ?>
                        <div id="woocommerce_settings" class="group tabs-panel">
                            <?php require INC_PATH . 'src/Plugins/WooCommerce/options.php'; ?>
                        </div>
						<?php endif; ?>

                        <div id="comments_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/comments.php'; ?>
                        </div>

                        <div id="custom_script_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/custom_script.php'; ?>
                        </div>

                        <div id="custom_css_settings" class="group tabs-panel">
							<?php require INC_PATH . 'admin/options/custom_css.php'; ?>
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

	/** ---------------------------------------- */

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
							if ( ! extension_loaded( 'openssl' ) && ! defined( 'OPENSSL_ALGO_SHA1' ) ) {
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

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_init(): void {

		// https://wordpress.stackexchange.com/questions/77532/how-to-add-the-category-id-to-admin-page
		$taxonomy_arr = [
			'category',
			'post_tag',
		];

		$taxonomy_arr = apply_filters( 'hd_term_row_actions', $taxonomy_arr );

		foreach ( $taxonomy_arr as $term ) {
			add_filter( "{$term}_row_actions", [ &$this, 'term_action_links' ], 11, 2 );
		}

		// customize row_actions
		$post_type_arr = [
			'user',
			'post',
			'page',
		];
		$post_type_arr = apply_filters( 'hd_post_row_actions', $post_type_arr );

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
		$exclude_thumb_posts = apply_filters( 'hd_post_exclude_columns', $exclude_thumb_posts );

		foreach ( $exclude_thumb_posts as $post ) {
			add_filter( "manage_{$post}_posts_columns", [ $this, 'post_exclude_header' ], 12, 1 );
		}

		// thumb terms
		$thumb_terms = [
			'category',
			'post_tag',
		];

		$thumb_terms = apply_filters( 'hd_term_columns', $thumb_terms );

		foreach ( $thumb_terms as $term ) {
			add_filter( "manage_edit-{$term}_columns", [ &$this, 'term_header' ], 11, 1 );
			add_filter( "manage_{$term}_custom_column", [ &$this, 'term_column' ], 11, 3 );
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return array|mixed
	 */
	public function term_header( $columns ): mixed {
		if ( Helper::is_acf_active() || Helper::is_acf_pro_active() ) {

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

	/** ---------------------------------------- */

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

	/** ---------------------------------------- */

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function post_exclude_header( $columns ): mixed {
		unset( $columns['post_thumb'] );

		return $columns;
	}

	/** ---------------------------------------- */

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

	/** ---------------------------------------- */

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
				} else if ( 'video' == $post_type ) {
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

	/** ---------------------------------------- */

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

	/** ---------------------------------------- */

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
