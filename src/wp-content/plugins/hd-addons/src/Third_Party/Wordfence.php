<?php

namespace Addons\Third_Party;

\defined( 'ABSPATH' ) || die;

final class Wordfence {

	public static int $ActivatorRemainingDays = 365;

	// --------------------------------------------------

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {
		if ( class_exists( '\wfLicense' ) ) {
			$this->_init();

			\wfLicense::current()->setType( \wfLicense::TYPE_RESPONSE );
			\wfLicense::current()->setPaid( true );
			\wfLicense::current()->setRemainingDays( self::$ActivatorRemainingDays );
			\wfLicense::current()->setConflicting( false );
			\wfLicense::current()->setDeleted( false );
			\wfLicense::current()->getKeyType();
		}
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	private function _init(): void {
		try {
			\wfOnboardingController::_markAttempt1Shown();
			\wfConfig::set( 'onboardingAttempt3', \wfOnboardingController::ONBOARDING_LICENSE );

			if ( empty( \wfConfig::get( 'apiKey' ) ) ) {
				\wordfence::ajax_downgradeLicense_callback();
			}

			\wfConfig::set( 'isPaid', true );
			\wfConfig::set( 'keyType', \wfLicense::KEY_TYPE_PAID_CURRENT );

			$timestamp = ( new \DateTime() )->add( new \DateInterval( 'P1Y' ) )->getTimestamp();

			\wfConfig::set( 'premiumNextRenew', $timestamp );
			\wfWAF::getInstance()->getStorageEngine()->setConfig( 'wafStatus', \wfFirewall::FIREWALL_MODE_ENABLED );

		} catch ( \Exception $exception ) {
		}
	}
}
