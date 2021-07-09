<?php
/**
 * Workflow Module functions.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow;

use Altis;

/**
 * Bootstrap Workflow module.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_notifications', 0 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_publication_checklist', 0 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_duplicate_posts' );
}

/**
 * Load notifications plugin.
 *
 * @return void
 */
function load_notifications() {
	Notifications\setup();
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
 * Load Yoast Duplicate Posts, if enabled.
 */
function load_duplicate_posts() {
	$config = Altis\get_config()['modules']['workflow']['clone-amend'] ?? null;

	// Bail if Clone & Republish is disabled.
	if ( ! $config ) {
		return;
	}

	// Load the main plugin file.
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/duplicate-post.php';

	Clone_Amend\setup();
}
