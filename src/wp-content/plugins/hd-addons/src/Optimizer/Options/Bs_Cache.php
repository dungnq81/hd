<?php

namespace Addons\Optimizer\Options;

use Addons\Base\Abstract_Htaccess;

\defined( 'ABSPATH' ) || die;

class Bs_Cache extends Abstract_Htaccess {

	/**
	 * @var string
	 */
	public string $template = 'browser-caching.tpl';

	/**
	 * @var array|string[]
	 */
	public array $rules = [
		'enabled'     => '/\#\s+Bs\s+Caching/si',
		'disabled'    => '/\#\s+Bs\s+Caching(.+?)\#\s+Bs\s+Caching\s+END(\n)?/ims',
		'disable_all' => '/\#\s+Bs\s+Caching(.+?)\#\s+Bs\s+Caching\s+END(\n)?/ims',
	];
}
