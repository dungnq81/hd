<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

?>
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

        <div id="optimizer_settings" class="group tabs-panel">
			<?php include ADDONS_PATH . 'src/Optimizer/options.php'; ?>
        </div>

        <div id="security_settings" class="group tabs-panel">
			<?php include ADDONS_PATH . 'src/Security/options.php'; ?>
        </div>

        <div id="social_settings" class="group tabs-panel">
			<?php include ADDONS_PATH . 'src/Social/options.php'; ?>
        </div>

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

	<?php if ( Helper::is_woocommerce_active() ) : ?>
        <div id="woocommerce_settings" class="group tabs-panel">
            <?php include INC_PATH . 'src/Plugins/WooCommerce/options.php'; ?>
        </div>
	<?php endif; ?>

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