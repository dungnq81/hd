<h2><?php _e( 'Social Settings', ADDONS_TEXT_DOMAIN ); ?></h2>

<?php
$social_options    = get_option( 'social__options' );
$hd_social_follows = apply_filters( 'hd_social_follows', [] );

if ( ! empty( $hd_social_follows ) ) :
	foreach ( $hd_social_follows as $key => $social ) :

		if ( empty( $social['name'] ) ) {
			break;
		}

        $icon = '';

?>
<div class="section section-text" id="section_social">
    <span class="heading !block"><?php _e( $social['name'], ADDONS_TEXT_DOMAIN ); ?></span>
    <div class="option">
        <div class="controls">
            <label for="<?=$key?>">

            </label>
			<input value="<?=esc_attr_strip_tags( $social['url'] )?>" class="hd-input hd-control" type="text" id="<?=$key?>" name="<?=$key?>">
		</div>
    </div>
</div>
<?php endforeach; endif;
