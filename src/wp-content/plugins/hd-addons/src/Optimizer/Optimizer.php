<?php

namespace Addons\Optimizer;

use Addons\Base\Singleton;

use Addons\Optimizer\Options\Del_Attached_Media\Del_Attached_Media;
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

	use Singleton;

	// ------------------------------------------------------

	private function init(): void {

		( Heartbeat::get_instance() );
		( SVG::get_instance() );
		( Lazy_Load::get_instance() );
		( Del_Attached_Media::get_instance() );
	}
}
