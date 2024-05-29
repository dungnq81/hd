<?php

namespace Addons\SMTP;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use WP_Error;

\defined( 'ABSPATH' ) || die;

final class SMTP {
	public function __construct() {

		if ( $this->smtpConfigured() && check_smtp_plugin_active() ) {
			add_filter( 'pre_wp_mail', [ &$this, 'pre_wp_mail' ], 11, 2 );
		}

		// SMTP alert
		add_action( 'admin_notices', [ &$this, 'options_admin_notice' ] );
	}

	// ------------------------------------------------------

	/**
	 * @param $null
	 * @param $atts
	 *
	 * @return void
	 * @throws Exception
	 */
	public function pre_wp_mail( $null, $atts ): void {
		$this->_smtp_pre_wp_mail( $null, $atts, 'smtp__options' );
	}

	// -------------------------------------------------------------

	/**
	 * SMTP Mailer plugin - https://vi.wordpress.org/plugins/smtp-mailer/
	 *
	 * @param $null
	 * @param $atts
	 * @param string|null $option_name
	 *
	 * @return void
	 * @throws Exception
	 */
	private function _smtp_pre_wp_mail( $null, $atts, ?string $option_name = null ): void {

		if ( isset( $atts['to'] ) ) {
			$to = $atts['to'];
		}

		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}

		if ( isset( $atts['subject'] ) ) {
			$subject = $atts['subject'];
		}

		if ( isset( $atts['message'] ) ) {
			$message = $atts['message'];
		}

		if ( isset( $atts['headers'] ) ) {
			$headers = $atts['headers'];
		}

		if ( isset( $atts['attachments'] ) ) {
			$attachments = $atts['attachments'];
			if ( ! is_array( $attachments ) ) {
				$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
			}
		}

		$option_name = $option_name ?: 'smtp__options';
		$options     = get_option( $option_name );

		global $phpmailer;

		// (Re)create it if it's gone missing.
		if ( ! ( $phpmailer instanceof PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$phpmailer = new PHPMailer( true );

			$phpmailer::$validator = static function ( $email ) {
				return (bool) is_email( $email );
			};
		}

		// Headers.
		$cc       = [];
		$bcc      = [];
		$reply_to = [];

		if ( empty( $headers ) ) {
			$headers = [];
		} else {
			if ( ! is_array( $headers ) ) {
				/*
				 * Explode the headers out, so this function can take
				 * both string headers and an array of headers.
				 */
				$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}

			$headers = [];

			// If it's actually got contents.
			if ( ! empty( $tempheaders ) ) {

				// Iterate through the raw headers.
				foreach ( (array) $tempheaders as $header ) {

					if ( ! str_contains( $header, ':' ) ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
							$parts    = preg_split( '/boundary=/i', trim( $header ) );
							$boundary = trim( str_replace( [ "'", '"' ], '', $parts[1] ) );
						}
						continue;
					}

					// Explode them out.
					[ $name, $content ] = explode( ':', trim( $header ), 2 );

					// Cleanup crew.
					$name    = trim( $name );
					$content = trim( $content );

					switch ( strtolower( $name ) ) {

						// Mainly for legacy -- process a "From:" header if it's there.
						case 'from':
							$bracket_pos = strpos( $content, '<' );
							if ( false !== $bracket_pos ) {

								// Text before the bracketed email is the "From" name.
								if ( $bracket_pos > 0 ) {
									$from_name = substr( $content, 0, $bracket_pos );
									$from_name = trim( str_replace( '"', '', $from_name ) );
								}

								$from_email = substr( $content, $bracket_pos + 1 );
								$from_email = trim( str_replace( '>', '', $from_email ) );

								// Avoid setting an empty $from_email.
							} elseif ( '' !== trim( $content ) ) {
								$from_email = trim( $content );
							}

							break;

						case 'content-type':
							if ( str_contains( $content, ';' ) ) {
								[ $type, $charset_content ] = explode( ';', $content );
								$content_type = trim( $type );
								if ( false !== stripos( $charset_content, 'charset=' ) ) {
									$charset = trim( str_replace( [ 'charset=', '"' ], '', $charset_content ) );
								} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
									$boundary = trim( str_replace( [
										'BOUNDARY=',
										'boundary=',
										'"'
									], '', $charset_content ) );
									$charset  = '';
								}

								// Avoid setting an empty $content_type.
							} elseif ( '' !== trim( $content ) ) {
								$content_type = trim( $content );
							}
							break;
						case 'cc':
							$cc = array_merge( (array) $cc, explode( ',', $content ) );
							break;
						case 'bcc':
							$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
							break;
						case 'reply-to':
							$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
							break;
						default:
							// Add it to our grand headers array.
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}
		}

		// Empty out the values that may be set.
		$phpmailer->clearAllRecipients();
		$phpmailer->clearAttachments();
		$phpmailer->clearCustomHeaders();
		$phpmailer->clearReplyTos();
		$phpmailer->Body    = '';
		$phpmailer->AltBody = '';

		// Set "From" name and email.

		$from_email = apply_filters( 'wp_mail_from', $options['smtp_from_email'] );
		$from_name  = apply_filters( 'wp_mail_from_name', $options['smtp_from_name'] );

		try {
			$phpmailer->setFrom( $from_email, $from_name, false );
		} catch ( Exception $e ) {
			$mail_error_data                             = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
			$mail_error_data['phpmailer_exception_code'] = $e->getCode();

			/** This filter is documented in wp-includes/pluggable.php */
			do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_error_data ) );

			return;
		}

		// reply_to
		$smtp_reply_to = '';
		$smtp_reply_to = apply_filters( 'smtp_reply_to', $smtp_reply_to );

		if ( ! empty( $smtp_reply_to ) ) {
			$temp_reply_to_addresses = explode( ",", $smtp_reply_to );
			$reply_to                = [];

			foreach ( $temp_reply_to_addresses as $temp_reply_to_address ) {
				$reply_to[] = trim( $temp_reply_to_address );
			}
		}

		// Set mail's subject and body.
		$phpmailer->Subject = $subject;
		$phpmailer->Body    = $message;

		// Set destination addresses, using appropriate methods for handling addresses.
		$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

		foreach ( $address_headers as $address_header => $addresses ) {
			if ( empty( $addresses ) ) {
				continue;
			}

			foreach ( (array) $addresses as $address ) {
				try {
					// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
					$recipient_name = '';

					if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) && count( $matches ) === 3 ) {
						[ $recipient_name, $address ] = $matches;
						//$recipient_name = $matches[1];
						//$address        = $matches[2];
					}

					switch ( $address_header ) {
						case 'to':
							$phpmailer->addAddress( $address, $recipient_name );
							break;
						case 'cc':
							$phpmailer->addCc( $address, $recipient_name );
							break;
						case 'bcc':
							$phpmailer->addBcc( $address, $recipient_name );
							break;
						case 'reply_to':
							$phpmailer->addReplyTo( $address, $recipient_name );
							break;
					}
				} catch ( Exception $e ) {
					continue;
				}
			}
		}

		$phpmailer->isSMTP();
		$phpmailer->Host = $options['smtp_host'];

		// Whether to use SMTP authentication
		if ( isset( $options['smtp_auth'] ) && $options['smtp_auth'] === "true" ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $options['smtp_username'];
			$phpmailer->Password = base64_decode( $options['smtp_password'] );
		}

		// Additional settings

		$type_of_encryption = $options['smtp_encryption'];
		if ( $type_of_encryption === "none" ) {
			$type_of_encryption = '';
		}
		$phpmailer->SMTPSecure = $type_of_encryption;

		// SMTP port
		$phpmailer->Port        = $options['smtp_port'];
		$phpmailer->SMTPAutoTLS = false;

		// disable ssl certificate verification if checked
		if ( ! empty( $options['smtp_disable_ssl_verification'] ) ) {
			$phpmailer->SMTPOptions = [
				'ssl' => [
					'verify_peer'       => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true,
				]
			];
		}

		// Set Content-Type and charset.

		// If we don't have a Content-Type from the input headers.
		if ( ! isset( $content_type ) ) {
			$content_type = 'text/plain';
		}

		$content_type           = apply_filters( 'wp_mail_content_type', $content_type );
		$phpmailer->ContentType = $content_type;

		// Set whether it's a plaintext, depending on $content_type.
		if ( 'text/html' === $content_type ) {
			$phpmailer->isHTML( true );
		}

		// If we don't have a charset from the input headers.
		if ( ! isset( $charset ) ) {
			$charset = get_bloginfo( 'charset' );
		}

		$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

		// Set custom headers.
		if ( ! empty( $headers ) ) {
			foreach ( (array) $headers as $name => $content ) {

				// Only add custom headers, not added automatically by PHPMailer.
				if ( ! in_array( $name, [ 'MIME-Version', 'X-Mailer' ], true ) ) {
					try {
						$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
					} catch ( Exception $e ) {
						continue;
					}
				}
			}

			if ( ! empty( $boundary ) &&
			     false !== stripos( $content_type, 'multipart' )
			) {
				$phpmailer->addCustomHeader( sprintf( 'Content-Type: %s; boundary="%s"', $content_type, $boundary ) );
			}
		}

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $filename => $attachment ) {
				$filename = is_string( $filename ) ? $filename : '';

				try {
					$phpmailer->addAttachment( $attachment, $filename );
				} catch ( Exception $e ) {
					continue;
				}
			}
		}

		/**
		 * Fires after PHPMailer is initialized.
		 *
		 * @param PHPMailer $phpmailer The PHPMailer instance (passed by reference).
		 */
		do_action_ref_array( 'phpmailer_init', [ &$phpmailer ] );

		$mail_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );

		// Send!
		try {
			$send = $phpmailer->send();
			do_action( 'wp_mail_succeeded', $mail_data );

			return;

		} catch ( Exception $e ) {
			$mail_data['phpmailer_exception_code'] = $e->getCode();

			do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', $e->getMessage(), $mail_data ) );

			return;
		}
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public function smtpConfigured(): bool {
		$smtp_options    = get_option( 'smtp__options' );
		$smtp_configured = true;

		if ( isset( $smtp_options['smtp_auth'] ) && $smtp_options['smtp_auth'] === "true" ) {
			if ( empty( $smtp_options['smtp_username'] ) || empty( $smtp_options['smtp_password'] ) ) {
				$smtp_configured = false;
			}
		}

		if ( empty( $smtp_options['smtp_host'] ) ||
		     empty( $smtp_options['smtp_auth'] ) ||
		     empty( $smtp_options['smtp_encryption'] ) ||
		     empty( $smtp_options['smtp_port'] ) ||
		     empty( $smtp_options['smtp_from_email'] ) ||
		     empty( $smtp_options['smtp_from_name'] )
		) {
			$smtp_configured = false;
		}

		return $smtp_configured;
	}

	/** ---------------------------------------- */

	/**
	 * SMTP notices
	 *
	 * @return void
	 */
	public function options_admin_notice(): void {
		if ( ! $this->smtpConfigured() && check_smtp_plugin_active() ) {
			$class   = 'notice notice-error';
			$message = __( 'You need to configure your SMTP credentials in the settings to send emails.', ADDONS_TEXT_DOMAIN );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}
}
