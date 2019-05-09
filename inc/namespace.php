<?php
/**
 * Workflow Module functions.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use const Altis\ROOT_DIR;

function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_workflows' );
}

function load_workflows() {
	require_once ROOT_DIR . '/vendor/humanmade/workflows/plugin.php';
}
