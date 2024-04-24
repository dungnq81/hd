<?php

$options = [
	'sanitized' => esc_html__( 'Sanitized SVG images', ADDONS_TEXT_DOMAIN ),
	'unrestricted' => esc_html__( 'Unrestricted SVG images', ADDONS_TEXT_DOMAIN ),
	'disable' => esc_html__( 'Disable SVG images', ADDONS_TEXT_DOMAIN ),
];

$svgs = $svgs ?? '';

?>
<div class="section section-radio" id="section_svg">
	<span class="heading !block"><?php _e( 'SVG Images', ADDONS_TEXT_DOMAIN ); ?></span>
    <div class="desc"><?php _e( '<b>Security notice:</b> Every SVG image is an XML file, which can contain <b>malicious code</b>, that can lead to <b>XSS</b> or <b>Injection attacks</b>.', TEXT_DOMAIN ); ?></div>
    <div class="option inline-option">
        <div class="controls">
            <div class="inline-group">
                <?php foreach ( $options as $key => $opt ) : ?>
                <label>
                    <input type="radio" name="svgs" id="svgs-<?=$key?>" value="<?=$key?>" <?php checked( $svgs, $key ); ?> />
                    <span><?=$opt?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="explain"></div>
    </div>
</div>
