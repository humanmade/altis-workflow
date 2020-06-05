<?php
/**
 * Workflow Module.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow; // phpcs:ignore

use Altis;

add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled' => true,
		'posts-workflow' => false,
		'editorial-workflow' => false,
		'publication-checklist' => [
			'enabled' => true,
			'block-on-failing' => false,
			'hide-column' => false,
		],
	];
	Altis\register_module( 'workflow', __DIR__, 'Workflow', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
