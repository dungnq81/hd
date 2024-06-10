<?php

namespace Addons\Optimizer;

use Addons\Optimizer\Options\Heartbeat\Heartbeat;
use Addons\Optimizer\Options\Lazy_Load\Lazy_Load;
use Addons\Optimizer\Options\SVG\SVG;

\defined( 'ABSPATH' ) || die;

/**
 * Optimizer Class
 *
 * @author HD
 */
final class Optimizer {
	public function __construct() {

		( new Heartbeat() );
		( new SVG() );
		( new Lazy_Load() );
	}
}
