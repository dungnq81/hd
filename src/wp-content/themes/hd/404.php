<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package HD
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

wp_safe_redirect( Helper::home() );
