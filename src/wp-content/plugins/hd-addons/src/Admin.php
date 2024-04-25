<?php

namespace Addons;

\defined( 'ABSPATH' ) || die;

final class Admin {
	public function __construct() {
		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function admin_menu(): void {
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	}
}
