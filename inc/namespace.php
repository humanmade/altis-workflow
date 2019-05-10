<?php
/**
 * Workflow Module functions.
 *
 * @package hm-platform/workflow
 */

namespace HM\Platform\Workflow;

use const HM\Platform\ROOT_DIR;

function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_workflows', 0 );
}

function load_workflows() {
	require_once ROOT_DIR . '/vendor/humanmade/workflows/plugin.php';
}
