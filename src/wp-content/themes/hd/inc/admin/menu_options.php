<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

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

    <?php

    $menus = [
        'aspect_ratio_post_type' => Helper::filter_setting_options( 'aspect_ratio_post_type', [] ),

    ];

    ?>
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
            <li class="optimizer-settings">
            <a title="Optimizer" href="#optimizer_settings"><?php _e( 'Optimizer', TEXT_DOMAIN ); ?></a>
        </li>
            <li class="security-settings">
            <a title="Security" href="#security_settings"><?php _e( 'Security', TEXT_DOMAIN ); ?></a>
        </li>
            <li class="social-settings">
            <a title="Social" href="#social_settings"><?php _e( 'Social', TEXT_DOMAIN ); ?></a>
        </li>
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

	    <?php if ( Helper::is_woocommerce_active() ) : ?>
            <li class="woocommerce-settings">
            <a title="WooCommerce" href="#woocommerce_settings"><?php _e( 'WooCommerce', TEXT_DOMAIN ); ?></a>
        </li>
	    <?php endif; ?>

        <li class="custom-script-settings">
            <a title="Custom Scripts" href="#custom_script_settings"><?php _e( 'Custom Scripts', TEXT_DOMAIN ); ?></a>
        </li>
        <li class="custom-css-settings">
            <a title="Custom CSS" href="#custom_css_settings"><?php _e( 'Custom CSS', TEXT_DOMAIN ); ?></a>
        </li>
    </ul>
</div>
