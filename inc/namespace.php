<?php
/**
 * Workflow Module functions.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use Altis;
use Altis\Workflow\Notifications\QM_Collector_Notifications;
use Altis\Workflow\Notifications\QM_Output_Notifications;
use QM_Collectors;

/**
 * Bootstrap Workflow module.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_workflows', 0 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_publication_checklist', 0 );
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

	add_filter( 'qm/collectors', __NAMESPACE__ . '\\register_workflow_notification_qm_collector' );
	add_filter( 'qm/outputter/html', __NAMESPACE__ . '\\register_workflow_notification_qm_output_html' );
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
 * Register the collector to QM.
 *
 * @param array $collectors
 *
 * @return array
 */
function register_workflow_notification_qm_collector( array $collectors ) : array {
	$collectors['altis-workflow'] = new QM_Collector_Notifications();
	return $collectors;
}

/**
 * Add the Collector Output.
 *
 * @param array $output
 * @param QM_Collectors $collectors
 *
 * @return array
 */
function register_workflow_notification_qm_output_html( array $output, QM_Collectors $collectors ) : array {
	$collector = QM_Collectors::get( 'workflow_notifications' );
	if ( $collector !== null ) {
		$output['workflow_notifications'] = new QM_Output_Notifications( $collector );
	}
	return $output;
}
