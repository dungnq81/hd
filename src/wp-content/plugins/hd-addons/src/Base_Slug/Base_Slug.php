<?php

namespace Addons\Base_Slug;

\defined( 'ABSPATH' ) || die;

final class Base_Slug {

	public function __construct() {
		( new Rewrite_PostType() )->run();
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function reset_all(): void {

		// reset
		$custom_base_slug_options = [
			'base_slug_post_type' => [],
			'base_slug_taxonomy'  => [],
		];

		update_option( 'custom_base_slug__options', $custom_base_slug_options );
	}
}
