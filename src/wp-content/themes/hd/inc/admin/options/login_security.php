<?php

use Cores\Helper;
use Libs\Login_Security\Login_Attempts;

\defined( 'ABSPATH' ) || die;

$login_security_options = Helper::getOption( 'login_security__options', false );

$custom_login_url          = $login_security_options['custom_login_url'] ?? '';
$login_ips_access          = $login_security_options['login_ips_access'] ?? '';
$disable_ips_access        = $login_security_options['disable_ips_access'] ?? '';
$two_factor_authentication = $login_security_options['two_factor_authentication'] ?? '';
$limit_login_attempts      = $login_security_options['limit_login_attempts'] ?? 0;
$illegal_users             = $login_security_options['illegal_users'] ?? '';

echo '<h2>' . __( 'Login Security', TEXT_DOMAIN ) . '</h2>';

$login_security_default = Helper::filter_setting_options( 'login_security', false );
if ( $login_security_default['enable_custom_login_options'] ) :

?>
<div class="section section-text" id="section_custom_login_url">
	<label class="heading" for="custom_login_url"><?php _e( 'Custom Login URL', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Attackers frequently target <b>/wp-admin</b> or <b>/wp-login.php</b> as the default login URL for WordPress. Changing it can help prevent these attacks and provide a more memorable login URL.', TEXT_DOMAIN ); ?></div>
	<div class="option">
		<div class="controls control-prefix">
            <div class="prefix">
                <span class="input-txt" title="<?= Helper::esc_attr_strip_tags( Helper::home() )?>"><?=Helper::home()?></span>
            </div>
            <?php

            $default_login = 'wp-login.php';
            if ( ! empty( $login_security_default['custom_login_url'] ) ) {
                $default_login = $login_security_default['custom_login_url'];
            }

            ?>
			<input disabled value="<?php echo esc_attr_strip_tags( $custom_login_url ); ?>" class="hd-input hd-control" type="text" id="custom_login_url"
                   name="custom_login_url" placeholder="<?=Helper::esc_attr_strip_tags( $default_login )?>">
		</div>
	</div>
</div>
<?php endif; ?>

<div class="section section-select" id="section_login_ips_access">
	<label class="heading" for="login_ips_access"><?php _e( 'Allowlist IPs Login Access', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'By default, your WordPress login page is accessible from any IP address. You can use this feature to restrict login access to specific IPs or ranges of IPs to prevent brute-force attacks or malicious login attempts.<br><b>Ex:</b> 192.168.0.1, 192.168.0.1-100, 192.168.0.1/4', TEXT_DOMAIN ); ?></div>
	<div class="option">
		<div class="controls">
			<div class="select_wrapper">
				<select multiple placeholder="Enter IP addresses" class="hd-control hd-select select2-ips !w[100%]" name="login_ips_access" id="login_ips_access">
                    <?php
                    if ( $login_ips_access ) :
                        foreach ( (array) $login_ips_access as $ip ) :
                    ?>
                    <option selected value="<?=Helper::esc_attr_strip_tags( $ip )?>"><?=$ip?></option>
                    <?php endforeach; endif; ?>
                </select>
			</div>
		</div>
	</div>
</div>

<div class="section section-select" id="section_disable_ips_access">
	<label class="heading" for="disable_ips_access"><?php _e( 'Blocked IPs Access', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'List of IP addresses or ranges of IPs blocked from accessing the login page.<br><b>Ex:</b> 192.168.0.1, 192.168.0.1-100, 192.168.0.1/4', TEXT_DOMAIN ); ?></div>
	<div class="option">
		<div class="controls">
			<div class="select_wrapper">
				<select multiple placeholder="Enter IP addresses" class="hd-control hd-select select2-ips !w[100%]" name="disable_ips_access" id="disable_ips_access">
                    <?php
                    if ( $disable_ips_access ) :
	                    foreach ( (array) $disable_ips_access as $ip ) :
                    ?>
                    <option selected value="<?=Helper::esc_attr_strip_tags( $ip )?>"><?=$ip?></option>
                    <?php endforeach; endif; ?>
                </select>
			</div>
		</div>
	</div>
</div>

<div class="section section-checkbox" id="section_illegal_users">
    <label class="heading" for="illegal_users"><?php _e( 'Disable Common Usernames', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Using common usernames like <b>\'admin\'</b> is a security threat that often results in unauthorised access. By enabling this option we will disable the creation of common usernames and if you already have one or more users with a weak username, we\'ll ask you to provide new one(s).', TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="illegal_users" id="illegal_users" <?php checked( $illegal_users, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Check to activate', TEXT_DOMAIN ); ?></div>
    </div>
</div>

<div class="section section-select" id="section_limit_login_attempts">
    <label class="heading" for="limit_login_attempts"><?php _e( 'Limit Login Attempts', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Limit the number of times a given user can attempt to log in to your wp-admin with incorrect credentials. Once the login attempt limit is reached, the IP from which the attempts have originated will be blocked first for 1 hour. If the attempts continue after the first hour, the limit will then be triggered for 24 hours and then for 7 days.', TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <div class="select_wrapper">
                <select class="hd-control hd-select" name="limit_login_attempts" id="limit_login_attempts">
                    <?php foreach ( Login_Attempts::$login_attempts_data as $key => $value ) : ?>
                    <option value="<?=$key?>"<?= selected( $limit_login_attempts, $key, false ) ?>><?=$value?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="section section-checkbox" id="section_two_factor_authentication">
    <label class="heading" for="two_factor_authentication"><?php _e( 'Two-factor Authentication for Admin & Editors Users', TEXT_DOMAIN ); ?></label>
    <div class="desc"><?php _e( 'Two-factor authentication forces admin users to login only after providing a token, generated from the Google Authenticator application. When you enable this option, all admin & editor users will be asked to configure their two-factor authentication in the Authenticator app on their next login.', TEXT_DOMAIN )?></div>
    <div class="option">
        <div class="controls">
            <input disabled type="checkbox" class="hd-checkbox hd-control" name="two_factor_authentication" id="two_factor_authentication" <?php checked( $two_factor_authentication, 1 ); ?> value="1">
        </div>
        <div class="explain"><?php _e( 'Enable Two-factor authentication', TEXT_DOMAIN ); ?></div>
    </div>
</div>
