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
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_duplicate_post_admin_page', 99 );
	add_action( 'admin_init', __NAMESPACE__ . '\\maybe_update_duplicate_post_post_types' );
	add_filter( 'manage_post_posts_columns', __NAMESPACE__ . '\\remove_duplicate_post_original_item_column', 11 );
	add_filter( 'manage_page_posts_columns', __NAMESPACE__ . '\\remove_duplicate_post_original_item_column', 11 );
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
}

/**
 * Remove the Duplicate Post settings page.
 */
function remove_duplicate_post_admin_page() {
	remove_submenu_page( 'options-general.php', 'duplicatepost' );
}

/**
 * Update the Duplicate Post supported post types if the config option defines new or different post types than the defautls.
 *
 * Default supported post types are 'post' and 'page'.
 */
function maybe_update_duplicate_post_post_types() {
	$default_post_types = [ 'post', 'page' ];
	$config = Altis\get_config()['modules']['workflow']['clone-republish']['post-types-enabled'] ?? $default_post_types;
	$post_types_enabled = get_option( 'duplicate_post_types_enabled' );

	if (
		// Check if there's a difference between the default post types and the ones that are saved in the database.
		! array_diff( $default_post_types, $post_types_enabled ) &&
		// Check if there's a difference between what's in the config and what's in the database.
		! array_diff( $config, $post_types_enabled )
	) {
		return;
	}

	// Update the database with non-default post types.
	update_option( 'duplicate_post_types_enabled', $config );
}

/**
 * Remove the duplicate_post_original_item column.
 *
 * This column, on the post list, causes display/formatting issues and doesn't add any value, since the information is repeated in the title column.
 *
 * @param array $columns The array of post columns.
 *
 * @return array         The filtered array of columns.
 */
function remove_duplicate_post_original_item_column( array $columns ) : array {
	unset( $columns['duplicate_post_original_item'] );
	return $columns;
}
