<?php

namespace Libs\Security;

use Cores\Helper;

class Login_Attempts {

	/**
	 * The maximum allowed login attempts.
	 *
	 * @var integer
	 */
	public int $limit_login_attempts = 0;

	/**
	 * Login attempts data
	 *
	 * @var array
	 */
	public array $login_attempts_data = [
		0  => 'OFF',
		3  => '3',
		5  => '5',
		10 => '10',
	];

	// --------------------------------------------------

	/**
	 * The constructor.
	 */
	public function __construct() {
		$security_options           = get_option( 'security__options' );
		$this->limit_login_attempts = $security_options['limit_login_attempts'] ?? 0;
	}

	// --------------------------------------------------

	/**
	 * Restrict access to login for unsuccessfully attempts.
	 *
	 * @return void
	 */
	public function maybe_block_login_access(): void {

		// Get the user ip.
		$user_ip = Helper::getIpAddress();

		// Get login attempts data.
		$login_attempts = Helper::getOption( 'hd_security_unsuccessful_login', [] );

		// Bail if the user doesn't have attempts.
		if ( empty( $login_attempts[ $user_ip ]['timestamp'] ) ) {
			return;
		}

		// Bail if ip has reached the login attempts limit.
		if ( $login_attempts[ $user_ip ]['timestamp'] > time() ) {

			// Update the total blocked logins counter.
			Helper::updateOption( 'hd_security_total_blocked_logins', Helper::getOption( 'hd_security_total_blocked_logins', 0 ) + 1 );

			wp_die(
				esc_html__( 'You don’t have access to this page. Please contact the administrator of this website for further assistance.', TEXT_DOMAIN ),
				esc_html__( 'The access to that page has been restricted by the administrator of this website', TEXT_DOMAIN ),
				[
					'hd_error' => true,
					'response' => 403,
				]
			);
		}

		// Reset the login attempts if the restriction time has ended and the user was banned for maximum amount of time.
		if (
			$login_attempts[ $user_ip ]['attempts'] >= $this->limit_login_attempts * 3 &&
			$login_attempts[ $user_ip ]['timestamp'] < time()
		) {
			unset( $login_attempts[ $user_ip ] );
			Helper::updateOption( 'hd_security_unsuccessful_login', $login_attempts );
		}
	}

	// --------------------------------------------------

	/**
	 * Add a login attempt for specific ip address.
	 *
	 * @param string $error The login error.
	 */
	public function log_login_attempt( string $error ): string {
		global $errors;

		// Check for errors global since custom login urls plugin are not always returning it.
		if ( empty( $errors ) ) {
			return $error;
		}

		// Invalid login
		if (
			in_array( 'empty_username', $errors->get_error_codes(), false ) ||
			in_array( 'invalid_username', $errors->get_error_codes(), false ) ||
			in_array( 'empty_password', $errors->get_error_codes(), false )
		) {
			return $error;
		}

		// Get the current user ip.
		$user_ip = Helper::getIpAddress();

		// Get the login attempts data.
		$login_attempts = Helper::getOption( 'hd_security_unsuccessful_login', [] );

		// Add the ip to the list if it does not exist.
		if ( ! array_key_exists( $user_ip, $login_attempts ) ) {
			$login_attempts[ $user_ip ] = [
				'attempts'  => 0,
				'timestamp' => '',
			];
		}

		// Increase the attempt count.
		$login_attempts[ $user_ip ]['attempts'] ++;
		if ( $login_attempts[ $user_ip ]['attempts'] > 0 ) {
			$errors->add( 'login_attempts', __( sprintf( '<strong>Alert:</strong> You have entered the wrong credentials <strong>%s</strong> times.', $login_attempts[ $user_ip ]['attempts'] ), TEXT_DOMAIN ) );

			if (
				in_array( 'incorrect_password', $errors->get_error_codes(), false ) &&
				in_array( 'login_attempts', $errors->get_error_codes(), false )
			) {
				$error_message = $errors->get_error_messages( 'login_attempts' );
				$error         .= '	' . $error_message[0] . "<br />\n";
			}
		}

		// Check if we are reaching the limits.
		$login_attempts[ $user_ip ]['timestamp'] = match ( $login_attempts[ $user_ip ]['attempts'] ) {

			$login_attempts[ $user_ip ]['attempts'] === $this->limit_login_attempts => time() + 3600,
			$login_attempts[ $user_ip ]['attempts'] === $this->limit_login_attempts * 2 => time() + 86400,
			$login_attempts[ $user_ip ]['attempts'] > $this->limit_login_attempts * 3 => time() + 604800,

			default => '',
		};

		// Update the login attempts data.
		Helper::updateOption( 'hd_security_unsuccessful_login', $login_attempts );

		return $error;
	}

	// --------------------------------------------------

	/**
	 * Reset login attempts on successful login.
	 *
	 * @return void
	 */
	public function reset_login_attempts(): void {
		$user_ip        = Helper::getIpAddress();
		$login_attempts = Helper::getOption( 'hd_security_unsuccessful_login', [] );

		// Bail if the IP doesn't exist in the unsuccessful logins.
		if ( ! array_key_exists( $user_ip, $login_attempts ) ) {
			return;
		}

		unset( $login_attempts[ $user_ip ] );
		Helper::updateOption( 'hd_security_unsuccessful_login', $login_attempts );
	}
}
