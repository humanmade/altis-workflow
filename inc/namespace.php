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
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_duplicate_posts', 0 );
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_duplicate_post_admin_page', 999 );
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
	$config = Altis\get_config()['modules']['workflow']['clone-republish'] ?? null;

	// Bail if Clone & Republish is disabled.
	if ( ! $config ) {
		return;
	}

	// Load the main plugin file.
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/duplicate-post.php';

	// Load all the other plugin files. This is normally handled internally by a Composer autoloader.
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/admin/options.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/admin/options-form-generator.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/admin/options-inputs.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/admin/options-page.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/duplicate-post.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/handlers/bulk-handler.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/handlers/check-changes-handler.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/handlers/handler.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/handlers/link-handler.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/handlers/save-post-handler.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/permissions-helper.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/post-duplicator.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/post-republisher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/revisions-migrator.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/admin-bar.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/asset-manager.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/block-editor.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/bulk-actions.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/classic-editor.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/column.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/link-builder.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/metabox.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/post-states.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/row-actions.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/ui/user-interface.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/utils.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/bulk-actions-watcher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/copied-post-watcher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/link-actions-watcher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/original-post-watcher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/republished-post-watcher.php';
	require_once Altis\ROOT_DIR . '/vendor/yoast/duplicate-post/src/watchers/watchers.php';

	// Kick off the main Duplicate Post plugin class.
	new \Yoast\WP\Duplicate_Post\Duplicate_Post();
}

/**
 * Remove the Duplicate Post settings page.
 */
function remove_duplicate_post_admin_page() {
	remove_submenu_page( 'options-general.php', 'duplicatepost' );
}
