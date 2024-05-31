<?php

$recaptcha_options    = get_option( 'recaptcha__options' );
$recaptcha_site_key   = $recaptcha_options['recaptcha_site_key'] ?? '';
$recaptcha_secret_key = $recaptcha_options['recaptcha_secret_key'] ?? '';
$recaptcha_score      = $recaptcha_options['recaptcha_score'] ?? '0.5';

?>
<h2><?php _e( 'reCAPTCHA v3 Settings', ADDONS_TEXT_DOMAIN ); ?></h2>
<div class="section section-text" id="section_recaptcha_site_key">
    <label class="heading" for="recaptcha_site_key"><?php _e( 'Site key', TEXT_DOMAIN ); ?></label>
    <div class="desc">Use this site key in the HTML code your site serves to users. <a target="_blank" href="https://developers.google.com/recaptcha/docs/v3">See client side integration</a></div>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags( $recaptcha_site_key ); ?>" class="hd-input hd-control" type="text" id="recaptcha_site_key" name="recaptcha_site_key">
        </div>
    </div>
</div>

<div class="section section-text" id="section_recaptcha_secret_key">
    <label class="heading" for="recaptcha_secret_key"><?php _e( 'Secret key', TEXT_DOMAIN ); ?></label>
    <div class="desc">Use this secret key for communication between your site and reCAPTCHA. <a target="_blank" href="https://developers.google.com/recaptcha/docs/verify">See server side integration</a></div>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags( $recaptcha_secret_key ); ?>" class="hd-input hd-control" type="text" id="recaptcha_secret_key" name="recaptcha_secret_key">
        </div>
    </div>
</div>

<div class="section section-text" id="section_recaptcha_score">
    <label class="heading" for="recaptcha_score"><?php _e( 'Score', TEXT_DOMAIN ); ?></label>
    <div class="desc">reCAPTCHA v3 returns a score (1.0 most likely a good interaction, 0.0 most likely a bot). By default, you can use a threshold of 0.5.</div>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags( $recaptcha_score ); ?>" class="hd-input hd-control input-half" type="text" id="recaptcha_score" name="recaptcha_score">
        </div>
    </div>
</div>
