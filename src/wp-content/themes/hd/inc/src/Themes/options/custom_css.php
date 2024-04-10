<?php

use Cores\Helper;

$css = Helper::getCustomPostContent( 'hd_css', false );

?>
<h2><?php _e( 'CSS Settings', HD_TEXT_DOMAIN ); ?></h2>
<div class="section section-textarea" id="section_html_custom_css">
    <label class="heading" for="html_custom_css"><?php _e('Custom CSS', HD_TEXT_DOMAIN) ?></label>
    <div class="option">
        <div class="controls">
            <textarea class="hd-textarea hd-control codemirror_css" name="html_custom_css" id="html_custom_css" rows="8"><?php echo $css?></textarea>
        </div>
    </div>
</div>