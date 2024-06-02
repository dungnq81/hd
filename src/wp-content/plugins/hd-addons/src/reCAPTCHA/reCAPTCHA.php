<?php

namespace Addons\reCAPTCHA;

\defined( 'ABSPATH' ) || die;

$recaptcha_options = get_option( 'recaptcha__options' );

define( "GOOGLE_CAPTCHA_SITE_KEY", $recaptcha_options['recaptcha_site_key'] ?? '' );
define( "GOOGLE_CAPTCHA_SECRET_KEY", $recaptcha_options['recaptcha_secret_key'] ?? '' );
define( "GOOGLE_CAPTCHA_SCORE", $recaptcha_options['recaptcha_score'] ?? '0.5' );

final class reCAPTCHA {
	public function __construct() {
		add_action( 'wp_head', [ &$this, 'add_recaptcha_script' ], 999 );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function add_recaptcha_script(): void {
		if ( GOOGLE_CAPTCHA_SITE_KEY ) {
			echo '<script src="https://www.google.com/recaptcha/api.js?render=' . GOOGLE_CAPTCHA_SITE_KEY . '"></script>';
		}
	}
}
