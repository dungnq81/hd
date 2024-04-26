<?php

namespace Addons\Heartbeat;

\defined( 'ABSPATH' ) || die;

const INTERVAL_LIMIT = 120;

final class Heartbeat {

	public array $options;

	public function __construct() {
		$optimizer_options = get_option( 'optimizer__options', [] );
		$heartbeat = $optimizer_options['heartbeat'] ?? 0;

		if ( ! empty( $heartbeat ) ) {
			$this->_set_intervals();
			$this->add_hooks();
		}
	}

	/**
	 * @return void
	 */
	public function add_hooks(): void {
		if ( @strpos( $_SERVER['REQUEST_URI'], '/wp-admin/admin-ajax.php' ) ) { // phpcs:ignore
			return;
		}

		add_action( 'admin_enqueue_scripts', [ &$this, 'maybe_disable' ], 99 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'maybe_disable' ], 99 );
		add_filter( 'heartbeat_settings', [ &$this, 'maybe_modify' ], 99 );
	}

	/**
	 * @return void
	 */
	private function _set_intervals(): void {
		$this->options = [
			'post'      => [
				'selected' => INTERVAL_LIMIT,
				'default'  => INTERVAL_LIMIT,
			],
			'dashboard' => [
				'selected' => 0,
				'default'  => 0,
			],
			'frontend'  => [
				'selected' => 0,
				'default'  => 0,
			],
		];
	}

	/**
	 * @return void
	 */
	public function maybe_disable(): void {
		foreach ( $this->options as $location => $interval_data ) {

			// Bail if the location doesn't match the specific location.
			if ( $this->check_location( $location ) &&
			     0 == $interval_data['selected'] &&
			     false !== $interval_data['selected']
			) {
				// Deregister the script.
				wp_deregister_script( 'heartbeat' );

				return;
			}
		}
	}

	/**
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function maybe_modify( $settings ): mixed {
		foreach ( $this->options as $location => $interval_data ) {

			// Bail if the location doesn't match the specific location.
			if ( $this->check_location( $location ) &&
			     1 < $interval_data['selected']
			) {
				// Change the interval.
				$settings['interval'] = intval( $interval_data['selected'] );

				// Return the modified settings.
				return $settings;
			}
		}

		return $settings;
	}

	/**
	 * @param $location
	 *
	 * @return bool|int
	 */
	public function check_location( $location ): bool|int {
		return match ( $location ) {
			'dashboard' => ( is_admin() && false === @strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post.php' ) ),
			'frontend' => ! is_admin(),
			'post' => @strpos( $_SERVER['REQUEST_URI'], '/wp-admin/post.php' ),
			default => false,
		};
	}
}