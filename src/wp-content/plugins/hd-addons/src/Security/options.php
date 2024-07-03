<?php

use Addons\Security\Options\Login_Attempts;

$security_options = get_option( 'security__options', false );

$illegal_users             = $security_options['illegal_users'] ?? '';
$hide_wp_version           = $security_options['hide_wp_version'] ?? '';
$xml_rpc_off               = $security_options['xml_rpc_off'] ?? '';
$wp_links_opml_off         = $security_options['wp_links_opml_off'] ?? '';
$remove_readme             = $security_options['remove_readme'] ?? '';
$rss_feed_off              = $security_options['rss_feed_off'] ?? '';
$lock_protect_system       = $security_options['lock_protect_system'] ?? '';
$advanced_xss_protection   = $security_options['advanced_xss_protection'] ?? '';
$limit_login_attempts      = $security_options['limit_login_attempts'] ?? 0;
//$two_factor_authentication = $security_options['two_factor_authentication'] ?? '';

?>
<h2><?php _e( 'Security Settings', ADDONS_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_hide_wp_version">
    <label class="heading" for="hide_wp_version"><?php _e( 'Hide WordPress Version', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Many attackers scan sites for vulnerable WordPress versions. By hiding the version from your site HTML, you avoid being marked by hackers for mass attacks.', ADDONS_TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="hide_wp_version" id="hide_wp_version" <?php checked( $hide_wp_version, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_xml_rpc_off">
	<label class="heading" for="xml_rpc_off"><?php _e( 'Disable XMLRPC', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'XMLRPC was designed as a protocol enabling WordPress to communicate with third-party systems but recently it has been used in a number of exploits. Unless you specifically need to use it, we recommend that XML-RPC is always disabled.', ADDONS_TEXT_DOMAIN )?></div>
	<div class="option">
		<div class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="xml_rpc_off" id="xml_rpc_off" <?php checked( $xml_rpc_off, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
	</div>
</div>

<div class="section section-checkbox" id="section_wp_links_opml_off">
	<label class="heading" for="wp_links_opml_off"><?php _e( 'Disable wp-links-opml', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'The wp-links-opml.php file allows users to export their links to an OPML file. This file is rarely used and can be disabled to enhance the security of your WordPress site. Disabling it helps prevent unauthorized access and potential information leakage.', ADDONS_TEXT_DOMAIN )?></div>
	<div class="option">
		<div class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="wp_links_opml_off" id="wp_links_opml_off" <?php checked( $wp_links_opml_off, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
	</div>
</div>

<div class="section section-checkbox" id="section_remove_readme">
	<label class="heading" for="remove_readme"><?php _e( 'Delete the Default Readme.html', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'WordPress comes with a readme.html file containing information about your website. The readme.html is often used by hackers to compile lists of potentially vulnerable sites which can be hacked or attacked.', ADDONS_TEXT_DOMAIN ); ?></div>
	<div class="option">
		<div class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="remove_readme" id="remove_readme" <?php checked( $remove_readme, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Remove the readme.html', ADDONS_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_rss_feed_off">
	<label class="heading" for="rss_feed_off"><?php _e( 'Disable RSS and ATOM Feeds', ADDONS_TEXT_DOMAIN ); ?></label>
	<div class="desc"><?php _e( 'RSS and ATOM feeds are often used to scrape your content and to perform a number of attacks against your site. Only use feeds if you have readers using your site via RSS readers.', ADDONS_TEXT_DOMAIN ); ?></div>
    <div class="option">
		<div class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="rss_feed_off" id="rss_feed_off" <?php checked( $rss_feed_off, 1 ); ?> value="1">
		</div>
		<div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
	</div>
</div>
<div class="section section-checkbox" id="section_lock_protect_system">
    <label class="heading" for="lock_protect_system"><?php _e( 'Lock and Protect System Folders', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'By enabling this option you are ensuring that no unauthorised or malicious scripts can be executed in your system folders. This is an often exploited back door you can close with a simple toggle.', ADDONS_TEXT_DOMAIN ); ?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="lock_protect_system" id="lock_protect_system" <?php checked( $lock_protect_system, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_advanced_xss_protection">
    <label class="heading" for="advanced_xss_protection"><?php _e( 'Advanced XSS Protection', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="advanced_xss_protection" id="advanced_xss_protection" <?php checked( $advanced_xss_protection, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Enabling this option will add extra headers to your site for protection against XSS attacks.', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-checkbox" id="section_illegal_users">
    <label class="heading" for="illegal_users"><?php _e( 'Disable Common Usernames', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Using common usernames like <b>\'admin\'</b> is a security threat that often results in unauthorised access. By enabling this option we will disable the creation of common usernames and if you already have one or more users with a weak username, we\'ll ask you to provide new one(s).', ADDONS_TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="illegal_users" id="illegal_users" <?php checked( $illegal_users, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-select" id="section_limit_login_attempts">
    <label class="heading" for="limit_login_attempts"><?php _e( 'Limit Login Attempts', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Limit the number of times a given user can attempt to log in to your wp-admin with incorrect credentials. Once the login attempt limit is reached, the IP from which the attempts have originated will be blocked first for 1 hour. If the attempts continue after the first hour, the limit will then be triggered for 24 hours and then for 7 days.', ADDONS_TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <div class="select_wrapper">
                <select class="hd-control hd-select" name="limit_login_attempts" id="limit_login_attempts">
                    <?php foreach ( Login_Attempts::$login_attempts_data as $key => $value ) : ?>
                    <option value="<?=$key?>"<?php echo selected( $limit_login_attempts, $key, false ); ?>><?=$value?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="section section-checkbox !hidden" id="section_two_factor_authentication">
    <label class="heading" for="two_factor_authentication"><?php _e( 'Two-factor Authentication for Admin & Editors Users', ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Two-factor authentication forces admin users to login only after providing a token, generated from the Google Authenticator application. When you enable this option, all admin & editor users will be asked to configure their two-factor authentication in the Authenticator app on their next login.', ADDONS_TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input disabled type="checkbox" class="hd-checkbox hd-control" name="two_factor_authentication" id="two_factor_authentication" <?php checked( $two_factor_authentication, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Enable Two-factor authentication', ADDONS_TEXT_DOMAIN ); ?></div>
    </div>
</div>
