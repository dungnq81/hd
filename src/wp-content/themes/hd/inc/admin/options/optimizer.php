<?php

use Cores\Helper;

$optimizer_options = Helper::getOption( 'optimizer__options' );
$https_enforce = $optimizer_options['https_enforce'] ?? 0;
$svgs = $optimizer_options['svgs'] ?? 'disable';

?>
<h2><?php _e( 'Optimizer Settings', TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_https_enforce">
    <label class="heading" for="https_enforce"><?php _e( 'HTTPS', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Configures your site to work correctly via HTTPS and forces a secure connection to your site. In order to force HTTPS, we will automatically update your database replacing all insecure links. In addition to that, we will add a rule in your .htaccess file, forcing all requests to go through encrypted connection.', TEXT_DOMAIN ); ?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="https_enforce" id="https_enforce" <?php checked( $https_enforce, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', TEXT_DOMAIN ); ?></div>
    </div>
</div>

<?php
if ( Helper::is_addons_active() ) {
    require ADDONS_PATH . 'src/SVG/options.php';
}
