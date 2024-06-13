<?php

namespace Addons\Optimizer\Options;

use Addons\Htaccess\Abstract_Htaccess;

\defined( 'ABSPATH' ) || die;

class Gzip extends Abstract_Htaccess {

	/**
	 * @var string
	 */
	public string $template = 'gzip.tpl';

	/**
	 * @var array|string[]
	 */
	public array $rules = [
		'enabled'     => '/\#\s+Gzip/si',
		'disabled'    => '/\#\s+Gzip(.+?)\#\s+Gzip\s+END(\n)?/ims',
		'disable_all' => '/\#\s+Gzip(.+?)\#\s+Gzip\s+END(\n)?/ims',
	];
}