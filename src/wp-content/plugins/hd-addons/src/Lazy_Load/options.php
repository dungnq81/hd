<?php

$lazy_load = $lazy_load ?? 0;
$exclude_lazyload = $exclude_lazyload ?? [];
$exclude_lazyload = implode( PHP_EOL, $exclude_lazyload );

?>
<div class="section section-checkbox" id="section_lazyload">
	<label class="heading !block" for="lazy_load"><?php _e( 'Lazy Load Media', ADDONS_TEXT_DOMAIN ); ?></label>
	<div class="desc">Speed up your web application by deferring the loading of below-the-fold images, animated SVGs, videos, and iframes until they enter the viewport.</div>
	<div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="lazy_load" id="lazy_load" <?php checked( $lazy_load, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', TEXT_DOMAIN ); ?></div>
    </div>
</div>

<div class="section section-textarea" id="section_exclude_lazyload">
	<label class="heading inline-heading" for="exclude_lazyload"><?php _e( 'Excluded images or iframes', TEXT_DOMAIN ) ?></label>
    <div class="desc">The keywords include file-name, CSS classes of images or iframe codes that will be excluded.</div>
	<div class="option">
		<div class="controls">
			<textarea class="hd-textarea hd-control" name="exclude_lazyload" id="exclude_lazyload" rows="4"><?php echo $exclude_lazyload; ?></textarea>
		</div>
	</div>
</div>
