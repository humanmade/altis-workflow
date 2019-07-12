<?php
/**
 * Workflow Module functions.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use const Altis\ROOT_DIR;
use function Altis\get_config;

function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_workflows', 0 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_publication_checklist', 0 );
}

function load_workflows() {
	require_once ROOT_DIR . '/vendor/humanmade/workflows/plugin.php';
}

/**
 * Load the Publication Checklist feature, if enabled.
 */
function load_publication_checklist() {
	$config = get_config()['modules']['workflow']['publication-checklist'] ?? null;
	if ( ! $config ) {
		return;
	}

	if ( ! is_array( $config ) ) {
		$config = [];
	}

	if ( $config['block-on-failing'] ?? false ) {
		add_filter( 'altis.publication-checklist.block_on_failing', '__return_true' );
	}
	if ( $config['hide-column'] ?? false ) {
		add_filter( 'altis.publication-checklist.show_tasks_column', '__return_false' );
	}

	require_once ROOT_DIR . '/vendor/humanmade/publication-checklist/plugin.php';
}
