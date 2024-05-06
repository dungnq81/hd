<?php

use Cores\Helper;

$woocommerce__options = Helper::getOption( 'woocommerce__options', false, false );

$remove_legacy_coupon    = $woocommerce__options['remove_legacy_coupon'] ?? '';
$woocommerce_jsonld      = $woocommerce__options['woocommerce_jsonld'] ?? '';
$woocommerce_default_css = $woocommerce__options['woocommerce_default_css'] ?? '';

?>
<h2><?php _e( 'Woocommerce Settings', TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="remove_legacy_coupon">
	<label class="heading" for="remove_legacy_coupon"><?php _e( 'Remove legacy coupon menu', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Fixed WooCommerce Admin notice for removing legacy coupon menu does not disappear.', TEXT_DOMAIN )?></div>
	<div class="option">
		<div class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="remove_legacy_coupon" id="remove_legacy_coupon" <?php checked( $remove_legacy_coupon, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Remove legacy coupon', TEXT_DOMAIN ); ?></div>
	</div>
</div>

<div class="section section-checkbox" id="woocommerce_jsonld">
    <label class="heading" for="woocommerce_jsonld"><?php _e( 'WooCommerce 3 JSON/LD', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Remove the default WooCommerce 3 JSON/LD structured data format', TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="woocommerce_jsonld" id="woocommerce_jsonld" <?php checked( $woocommerce_jsonld, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Remove WooCommerce 3 JSON/LD', TEXT_DOMAIN ); ?></div>
    </div>
</div>

<div class="section section-checkbox" id="woocommerce_default_css">
    <label class="heading" for="woocommerce_default_css"><?php _e( 'Remove WooCommerce CSS', TEXT_DOMAIN ); ?></label>
    <div class="desc">Remove all default CSS of WooCommerce.</div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="woocommerce_default_css" id="woocommerce_default_css" <?php checked( $woocommerce_default_css, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Remove all', TEXT_DOMAIN ); ?></div>
    </div>
</div>
