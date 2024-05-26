<?php

use Cores\Helper;

$aspect_ratio_post_type = Helper::filter_setting_options( 'aspect_ratio_post_type', [] );

?>
<h2><?php _e( 'Aspect Ratio Settings', TEXT_DOMAIN ); ?></h2>
<?php
foreach ( $aspect_ratio_post_type as $ar ) :
	$title = Helper::mbUcFirst( $ar );

	if ( ! $title ) {
		break;
	}

	$w_h    = Helper::getAspectRatioOption( $ar, 'aspect_ratio__options' );
	$width  = $w_h[0] ?? '';
	$height = $w_h[1] ?? '';

?>
<div class="section section-text" id="section_aspect_ratio">
    <span class="heading"><?php _e( $title, TEXT_DOMAIN ); ?></span>
    <div class="desc"><?php echo $title?> images will be viewed at a custom aspect ratio.</div>
    <div class="option inline-option">
        <div class="controls">
            <div class="inline-group">
                <label>
                    Width:
                    <input class="hd-input hd-control" name="<?=$ar?>-width" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr_strip_tags($width); ?>">
                </label>
                <span>x</span>
                <label>
                    Height:
                    <input class="hd-input hd-control" name="<?=$ar?>-height" type="number" pattern="\d*" size="3" min="0" value="<?php echo esc_attr_strip_tags($height); ?>">
                </label>
            </div>
        </div>
    </div>
</div>
<?php
endforeach;
