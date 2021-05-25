<?php
/**
 * Workflow Module.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use Altis;

add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled' => true,
		'notifications' => [
			'on-post-published' => false,
			'on-submit-for-review' => false,
			'on-update-assignees' => false,
			'on-editorial-comment' => false,
		],
		'publication-checklist' => [
			'enabled' => true,
			'block-on-failing' => false,
			'hide-column' => false,
		],
		'clone-republish' => [
			'enabled' => true,
			'post-types' => [
				'post',
				'page',
			],
		],
	];
	Altis\register_module( 'workflow', __DIR__, 'Workflow', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
