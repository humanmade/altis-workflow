<?php
/**
 * Workflow Module functions.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use Altis;
use QM_Collectors;

/**
 * Bootstrap Workflow module.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_workflows', 0 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_publication_checklist', 0 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_query_monitor_debug' );
}

/**
 * Load notifications plugin.
 *
 * @return void
 */
function load_workflows() {
	$config = Altis\get_config()['modules']['workflow']['notifications'] ?? null;
	if ( ! $config ) {
		return;
	}
	require_once Altis\ROOT_DIR . '/vendor/humanmade/workflows/plugin.php';
}

/**
 * Load the Publication Checklist feature, if enabled.
 */
function load_publication_checklist() {
	$config = Altis\get_config()['modules']['workflow']['publication-checklist'] ?? null;
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

	require_once Altis\ROOT_DIR . '/vendor/humanmade/publication-checklist/plugin.php';
}

/**
 * Load the QM Collector for debugging notifications.
 */
function load_query_monitor_debug() {

	if ( ! class_exists( QM_Collectors::class ) ) {
		return;
	}

	require_once __DIR__ . '/class-qm-collector-notifications.php';
	QM_Collectors::add( new QM_Collector_Notifications() );

	add_filter(
		'qm/outputter/html',
		function( array $output, QM_Collectors $collectors ) {
			require_once __DIR__ . '/class-qm-output-notifications.php';
			$collector = QM_Collectors::get( 'workflow_notifications' );
			if ( $collector !== null ) {
				$output['workflow_notifications'] = new QM_Output_Notifications( $collector );
			}
			return $output;
		},
		101,
		2
	);
}
