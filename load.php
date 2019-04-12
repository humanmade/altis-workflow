<?php
/**
 * Workflow Module.
 *
 * @package hm-platform/workflow
 */

namespace HM\Platform\Workflow;

use function HM\Platform\register_module;

require_once __DIR__ . '/inc/namespace.php';

add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled' => false,
		'posts-workflow' => true,
		'editorial-workflow' => true,
	];
	register_module( 'workflow', __DIR__, 'Workflow', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
