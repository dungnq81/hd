<?php

\defined( 'ABSPATH' ) || die;

$del_attached_media = $del_attached_media ?? 0;

?>
<div class="section section-checkbox" id="section_del_attached_media">
    <label class="heading" for="del_attached_media"><?php _e( 'Delete Attached Media', ADDONS_TEXT_DOMAIN ) ?></label>
    <div class="desc"><?php _e( 'Remove all attached media from posts (if enabled). Clear old archives by deleting images associated with posts.', ADDONS_TEXT_DOMAIN ); ?></div>
    <div class="option">
        <div class="controls">
            <input disabled type="checkbox" class="hd-checkbox hd-control" name="del_attached_media" id="del_attached_media" <?php checked( $del_attached_media, 1 );
            ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
