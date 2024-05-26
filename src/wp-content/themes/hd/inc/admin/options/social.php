<h2><?php use Cores\Helper;

	_e( 'Social Settings', ADDONS_TEXT_DOMAIN ); ?></h2>

<?php
$social_options    = get_option( 'social__options' );
$social_follows_links = Helper::filter_setting_options( 'social_follows_links', [] );

if ( ! empty( $social_follows_links ) ) :
	foreach ( $social_follows_links as $key => $social ) :

		if ( empty( $social['name'] ) || empty( $social['icon'] ) ) {
			continue;
		}

		$name = $social['name'];
		$icon = $social['icon'];
		$url  = $social_options[ $key ]['url'] ?? $social['url'];
?>
<div class="section section-text" id="section_social">
    <span class="heading !block"><?php _e( $name, ADDONS_TEXT_DOMAIN ); ?></span>
    <div class="option">
        <div class="controls control-img">
            <label for="<?= $key ?>">
                <?php
                if ( filter_var( $icon, FILTER_VALIDATE_URL ) || str_starts_with( $icon, 'data:' ) ) :
	                echo '<img src="' . $icon . '" alt="' . esc_attr( $name ) . '">';
                elseif ( str_starts_with( $icon, '<svg' ) ) :
	                echo $icon;
                elseif ( is_string( $icon ) ) :
	                echo '<i class="' . $icon . '"></i>';
                endif;
                ?>
            </label>
			<input value="<?= esc_attr_strip_tags( $url ) ?>" class="hd-input hd-control" type="url" id="<?= $key ?>" name="<?= $key ?>-option">
		</div>
    </div>
</div>
<?php endforeach; endif;
