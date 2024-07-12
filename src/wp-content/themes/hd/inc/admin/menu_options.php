<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$hide_menu_options = apply_filters( 'hd_hide_menu_options', [] );

?>
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

        <li class="aspect-ratio-settings<?php in_array_toggle_class( $hide_menu_options, 'aspect-ratio' ); ?>">
            <a class="current" title="Aspect ratio" href="#aspect_ratio_settings"><?php _e( 'Aspect Ratio', TEXT_DOMAIN ); ?></a>
        </li>

	    <?php if ( Helper::is_addons_active() && check_smtp_plugin_active() ) : ?>
        <li class="smtp-settings<?php in_array_toggle_class( $hide_menu_options, 'smtp' ); ?>">
            <a title="SMTP" href="#smtp_settings"><?php _e( 'SMTP', TEXT_DOMAIN ); ?></a>
        </li>
	    <?php endif; ?>

	    <?php if ( Helper::is_addons_active() ) : ?>
        <li class="contact-info-settings<?php in_array_toggle_class( $hide_menu_options, 'contact-info' ); ?>">
            <a title="Contact Info" href="#contact_info_settings"><?php _e( 'Contact Info', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="contact-button-settings<?php in_array_toggle_class( $hide_menu_options, 'contact-button' ); ?>">
            <a title="Contact Button" href="#contact_button_settings"><?php _e( 'Contact Button', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="gutenberg-settings<?php in_array_toggle_class( $hide_menu_options, 'gutenberg' ); ?>">
            <a title="Editor" href="#gutenberg_settings"><?php _e( 'Editor', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="optimizer-settings<?php in_array_toggle_class( $hide_menu_options, 'optimizer' ); ?>">
            <a title="Optimizer" href="#optimizer_settings"><?php _e( 'Optimizer', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="security-settings<?php in_array_toggle_class( $hide_menu_options, 'security' ); ?>">
            <a title="Security" href="#security_settings"><?php _e( 'Security', TEXT_DOMAIN ); ?></a>
        </li>

        <li class="login-security-settings<?php in_array_toggle_class( $hide_menu_options, 'login-security' ); ?>">
            <a title="Login Security" href="#login_security_settings"><?php _e( 'Login Security', TEXT_DOMAIN ); ?></a>
        </li>

        <li class="social-settings<?php in_array_toggle_class( $hide_menu_options, 'social' ); ?>">
            <a title="Social" href="#social_settings"><?php _e( 'Social', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="base-slug-settings<?php in_array_toggle_class( $hide_menu_options, 'base-slug' ); ?>">
            <a title="Remove base slug" href="#base_slug_settings"><?php _e( 'Remove Base Slug', TEXT_DOMAIN ); ?></a>
        </li>

        <?php
		    $custom_emails = Helper::filter_setting_options( 'custom_emails', [] );
		    if ( ! empty( $custom_emails ) ) :
            ?>
            <li class="email-settings<?php in_array_toggle_class( $hide_menu_options, 'email' ); ?>">
                <a title="EMAIL" href="#email_settings"><?php _e( 'Custom Email', TEXT_DOMAIN ); ?></a>
            </li>
		    <?php endif; ?>

        <li class="custom-order-settings<?php in_array_toggle_class( $hide_menu_options, 'custom-order' ); ?>">
            <a title="Custom Order" href="#custom_order_settings"><?php _e( 'Custom Order', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="recaptcha-settings<?php in_array_toggle_class( $hide_menu_options, 'recaptcha' ); ?>">
            <a title="reCAPTCHA" href="#recaptcha_settings"><?php _e( 'reCAPTCHA', TEXT_DOMAIN ); ?></a>
        </li>
	    <?php endif; ?>

	    <?php if ( Helper::is_woocommerce_active() ) : ?>
        <li class="woocommerce-settings<?php in_array_toggle_class( $hide_menu_options, 'woocommerce' ); ?>">
            <a title="WooCommerce" href="#woocommerce_settings"><?php _e( 'WooCommerce', TEXT_DOMAIN ); ?></a>
        </li>
	    <?php endif; ?>

        <li class="custom-script-settings<?php in_array_toggle_class( $hide_menu_options, 'custom-script' ); ?>">
            <a title="Custom Scripts" href="#custom_script_settings"><?php _e( 'Custom Scripts', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="custom-css-settings<?php in_array_toggle_class( $hide_menu_options, 'custom-css' ); ?>">
            <a title="Custom CSS" href="#custom_css_settings"><?php _e( 'Custom CSS', TEXT_DOMAIN ); ?></a>
        </li>
    </ul>
</div>
