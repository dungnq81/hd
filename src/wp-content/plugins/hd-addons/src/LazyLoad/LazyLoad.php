<?php

namespace Addons\LazyLoad;

\defined( 'ABSPATH' ) || die;

final class LazyLoad {
	public function __construct() {

		// Disable the native lazy loading.
		//add_filter( 'wp_lazy_loading_enabled', '__return_false' );
	}
}
