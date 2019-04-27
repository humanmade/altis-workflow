<?php
/**
 * Workflow Module.
 *
 * @package hm-platform/workflow
 */

namespace HM\Platform\Workflow;

use function HM\Platform\register_module;

require_once __DIR__ . '/inc/namespace.php';

// Do not initialise if plugin.php hasn't been included yet.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled' => true,
		'posts-workflow' => false,
		'editorial-workflow' => false,
	];
	register_module( 'workflow', __DIR__, 'Workflow', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
