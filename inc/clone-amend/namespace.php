<?php
/**
 * Clone & Amend features in the Workflow Module.
 *
 * Functionality provided by YOAST Duplicate Post plugin.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow\Clone_Amend;

use Altis;
use WP_Post;
use Yoast\WP\Duplicate_Post\UI\Link_Builder;
use Yoast\WP\Duplicate_Post\Utils;

/**
 * Kick everything off.
 */
function setup() {
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_duplicate_post_admin_page', 99 );
	add_action( 'admin_init', __NAMESPACE__ . '\\filter_duplicate_post_columns', 1000 );
	add_action( 'duplicate_post_post_copy', __NAMESPACE__ . '\\duplicate_post_update_xb_client_ids', 10, 2 );
	add_action( 'admin_init', __NAMESPACE__ . '\\filter_duplicate_posts_bulk_actions' );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\override_duplicate_post_strings', 11 );
	add_action( 'wp_before_admin_bar_render', __NAMESPACE__ . '\\replace_duplicate_post_admin_menu', 11 );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\replace_amended_post_notice', 20 );
	add_filter( 'duplicate_post_enabled_post_types', __NAMESPACE__ . '\\set_enabled_post_types' );
	add_filter( 'pre_option_duplicate_post_roles', __NAMESPACE__ . '\\filter_duplicate_post_roles' );
	add_filter( 'pre_option_duplicate_post_taxonomies_blacklist', __NAMESPACE__ . '\\filter_duplicate_post_excluded_taxonomies' );
	add_filter( 'duplicate_post_excludelist_filter', __NAMESPACE__ . '\\exclude_meta_keys' );
	add_filter( 'altis.analytics.blocks.override_xb_save_post_hook', __NAMESPACE__ . '\\override_xb_post_update', 10, 2 );
	add_filter( 'post_row_actions', __NAMESPACE__ . '\\duplicate_post_row_actions', 11, 2 );
	add_filter( 'page_row_actions', __NAMESPACE__ . '\\duplicate_post_row_actions', 11, 2 );
	add_filter( 'display_post_states', __NAMESPACE__ . '\\duplicate_post_override_post_states', 11, 2 );
}

/**
 * Remove the Duplicate Post settings page.
 */
function remove_duplicate_post_admin_page() {
	remove_submenu_page( 'options-general.php', 'duplicatepost' );
}

/**
 * Override the Duplicate Post supported post types if the config option defines new or different post types than the defautls.
 *
 * Default supported post types are all public post types.
 *
 * Note: The configured option will _replace_ any enabled post types by default.
 *
 * @param array $enabled_post_types The post types that are supported by Duplicate Post by default (not used).
 * @return array The filtered array of supported post types.
 */
function set_enabled_post_types( array $enabled_post_types ) : array {
	$public_post_types = get_post_types( [ 'public' => true ], 'names' );
	$post_types = Altis\get_config()['modules']['workflow']['clone-amend']['post-types'] ?? null;

	if ( ! $post_types ) {
		return $public_post_types;
	}

	return $post_types;
}

/**
 * Remove the duplicate_post_original_item column.
 *
 * This column, on the post list, causes display/formatting issues and doesn't add any value, since the information is repeated in the title column.
 *
 * @param array $columns The array of post columns.
 *
 * @return array The filtered array of columns.
 */
function remove_duplicate_post_original_item_column( array $columns ) : array {
	unset( $columns['duplicate_post_original_item'] );
	return $columns;
}

/**
 * Removes the "Original Post" column in the post list for all supported post types.
 */
function filter_duplicate_post_columns() {
	$enabled_post_types = get_duplicate_post_types();
	foreach ( $enabled_post_types as $post_type ) {
		add_filter( "manage_{$post_type}_posts_columns", __NAMESPACE__ . '\\remove_duplicate_post_original_item_column', 11 );
	}
}

/**
 * Allow the roles defined in the Altis config to duplicate posts by default.
 *
 * @param mixed $roles The duplicate_post_roles value.
 *
 * @return array The filtered array of allowed roles.
 */
function filter_duplicate_post_roles( $roles ) : array {
	$roles = Altis\get_config()['modules']['workflow']['clone-amend']['roles'] ?? $roles;

	return $roles;
}

/**
 * Exclude taxonomies defined in the Altis config, if specified.
 *
 * @param mixed $taxonomies An array of taxonomies to exclude from duplicated posts, if it exists.
 *
 * @return array The filtered array of excluded taxonomies.
 */
function filter_duplicate_post_excluded_taxonomies( $taxonomies ) : array {
	$taxonomies = Altis\get_config()['modules']['workflow']['clone-amend']['taxonomies'] ?? [];

	return $taxonomies;
}

/**
 * Return an array of post types supported by Duplicate Post.
 *
 * @return array The array of enabled post types.
 */
function get_duplicate_post_types() : array {
	return apply_filters( 'duplicate_post_enabled_post_types', [] );
}

/**
 * Exclude Altis A/B Test meta data from duplicated posts.
 *
 * @param array $meta_excludelist The meta exclude list from the Duplicate Post options.
 *
 * @return array The filtered exclude list array.
 */
function exclude_meta_keys( array $meta_excludelist ) : array {
	$meta_excludelist[] = '_altis_ab_test_*';
	$meta_excludelist[] = '_altis_xb_clientId_updated';

	return $meta_excludelist;
}

/**
 * Update XB client IDs when duplicating a post.
 *
 * @param int $new_post_id The duplicated post ID.
 * @param WP_Post $post The original WP_Post object.
 */
function duplicate_post_update_xb_client_ids( int $new_post_id, WP_Post $post ) {
	// Bail if we aren't on a post type supported by Duplicate Posts.
	if ( ! in_array( $post->post_type, get_duplicate_post_types(), true ) ) {
		return;
	}

	$cloned_post = get_post_meta( $new_post_id, '_dp_original', true );
	$amended_post = get_post_meta( $new_post_id, '_dp_is_rewrite_republish_copy', true );

	// Bail if this post hasn't been cloned. All duplicated posts are considered cloned posts.
	if ( ! $cloned_post ) {
		return;
	}

	// Bail if this is a republished post. Only republished posts have both meta keys.
	if ( $amended_post ) {
		return;
	}

	// Check if the client ID has been updated already.
	$client_id_updated = get_post_meta( $new_post_id, '_altis_xb_clientId_updated', true );
	if ( $client_id_updated ) {
		return;
	}

	$pattern = '#<!-- wp:altis/(personalization|experiment)\s+{.*?"clientId":"([a-z0-9-]+)"#';

	$updated_post_content = preg_replace_callback(
		$pattern,
		function ( array $matches ) : string {
			return str_replace( $matches[2], wp_generate_uuid4(), $matches[0] );
		},
		$post->post_content
	);

	// Update the post content.
	$new_post = get_post( $new_post_id );
	$new_post->post_content = $updated_post_content;
	$updated = wp_update_post( $new_post );

	// Don't add the post meta if we've errored.
	if ( ! is_wp_error( $updated ) ) {
		add_post_meta( $new_post_id, '_altis_xb_clientId_updated', true );
	}
}

/**
 * Override the synchronization with the XB shadow post for rewritten posts.
 *
 * Conditionally setting this to true if the post is a rewritten copy prevents saves to the XB shadow post type.
 *
 * @param bool $default The default value whether to override.
 * @param int $post_id The post ID of the post to check.
 *
 * @return bool Whether to override the save_post hook.
 */
function override_xb_post_update( bool $default, int $post_id ) : bool {
	// If this post is a republished post, don't update the shadow XB post.
	if ( get_post_meta( $post_id, '_dp_is_rewrite_republish_copy', true ) ) {
		return true;
	}

	return $default;
}

/**
 * Filter the Duplicate Posts available bulk actions.
 */
function filter_duplicate_posts_bulk_actions() {
	// Get the enabled duplicate post post types.
	$duplicate_post_types = get_duplicate_post_types();

	// Loop through and filter the bulk actions for each enabled post type.
	foreach ( $duplicate_post_types as $post_type ) {
		add_filter( "bulk_actions-edit-$post_type", function( $actions ) {
			$actions['duplicate_post_bulk_rewrite_republish'] = __( 'Create Amendments', 'altis-workflow' );

			return $actions;
		}, 11 );
	}
}

/**
 * Override the Duplicate Post row actions and microcopy.
 *
 * @param array $actions The array of post row actions.
 * @param WP_Post $post The WP_Post object.
 *
 * @return array The filtered array of post row actions.
 */
function duplicate_post_row_actions( array $actions, WP_Post $post ) : array {
	// Bail if we're in an unsupported post type.
	if ( ! in_array( $post->post_type, get_duplicate_post_types(), true ) ) {
		return $actions;
	}

	// If this is a rewritten post, change the edit link.
	if ( get_post_meta( $post->ID, '_dp_is_rewrite_republish_copy', true ) ) {
		$actions['edit'] = str_replace( __( 'Edit' ), __( 'Edit Amendment', 'altis-workflow' ), $actions['edit'] );
	}

	// Remove the clone action.
	unset( $actions['clone'] );

	$linkify = new Link_Builder;
	$new_draft_link = $linkify->build_new_draft_link( $post );
	$amend_link = $linkify->build_rewrite_and_republish_link( $post );

	// Rename the New Draft action to Clone.
	if ( isset( $actions['edit_as_new_draft'] ) ) {
		$actions['edit_as_new_draft'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			$new_draft_link,
			sprintf( __( 'Create a clone of %s', 'altis-workflow' ), $post->post_title ),
			__( 'Clone', 'altis-workflow' )
		);
	}

	// Change Rewrite & Republish to Create Amendment.
	if ( isset( $actions['rewrite'] ) ) {
		$actions['rewrite'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			$amend_link,
			sprintf( __( 'Create an amendment of %s', 'altis-workflow' ), $post->post_title ),
			__( 'Create Amendment', 'altis-workflow' )
		);
	}

	// Re-sort the actions.
	ksort( $actions );

	return $actions;
}

/**
 * Override the post state for post amendments.
 *
 * @param string|array $post_states An array of post display states, if they exist.
 * @param WP_Post $post The WP_Post object.
 *
 * @return string|array The filtered post states.
 */
function duplicate_post_override_post_states( $post_states, WP_Post $post ) {
	$original_post = Utils::get_original( $post );
	if ( ! $original_post ) {
		return $post_states;
	}

	/*
	* This is a draft state in WP Core that only shows up for us on cloned
	* posts. It's confusing to have multiple types of Drafts, so this should
	* just go back to Draft if it's set.
	*
	* @see wp-admin/includes/template.php:2187
	*/
	if ( isset( $post_states[0] ) && $post_states[0] === __( 'Customization Draft' ) ) {
		$post_states[0] = __( 'Draft' );
	}

	if ( ! isset( $post_states['duplicate_post_original_item'] ) ) {
		return $post_states;
	}

	$original_post_edit_link = get_edit_post_link( $original_post->ID );

	$post_states['duplicate_post_original_item'] = sprintf(
		__( 'Amendment of %s', 'altis-workflow' ),
		sprintf( '<a href="%1$s">%2$s</a>', $original_post_edit_link, $original_post->post_title )
	);

	return $post_states;
}

/**
 * Override the duplicate post strings.
 *
 * For all posts, enqueue javascript that replaces the Duplicate Post render function with our own render function with updated strings.
 *
 * For rewrite & republished (amended) posts, we continue and replace all the strings provided by Duplicate Post with our versions.
 */
function override_duplicate_post_strings() {
	$strings = [
		'Republish' => [ __( 'Publish Amendment', 'altis-workflow' ) ],
		'Republish:' => [ __( 'Publish Amendment:', 'altis-workflow' ) ],
		'Republish on:' => [ __( 'Publish Amendment on:', 'altis-workflow' ) ],
		'Are you ready to republish your post?' => [ __( 'Are you ready to publish your amendment?', 'altis-workflow' ) ],
		'Schedule republish' => [ __( 'Schedule amendment', 'altis-workflow' ) ],
		'Schedule republish…' => [ __( 'Schedule amendment…', 'altis-workflow' ) ],
		'Are you ready to schedule the republishing of your post?' => [ __( 'Are you ready to schedule publishing the amendments to your post?', 'altis-workflow' ) ],
		', the rewritten post, is now scheduled to replace the original post. It will be published on' => [ __( ', the amended post, is now scheduled to replace the original post. It will be published on', 'altis-workflow' ) ],
		'After republishing your changes will be merged into the original post and you\'ll be redirected there.<br /><br />Do you want to compare your changes with the original version before merging?<br /><br /><button>Save changes and compare</button>' => [ __( 'After clicking Publish Amendment, your changes will be merged into the original post and you\'ll be redirected there.<br /><br />Do you want to compare your changes with the original version before merging?<br /><br /><button>Save changes and compare</button>', 'altis-workflow' ) ],
		'You\'re about to replace the original with this rewritten post at the specified date and time.<br /><br />Do you want to compare your changes with the original version before merging?<br /><br /><button>Save changes and compare</button>' => [ __( 'You\'re about to replace the original post with this amended post at the specified date and time.<br /><br />Do you want to compare your changes with the original version before publishing?<br /><br /><button>Save changes and compare</button>', 'altis-workflow' ) ],
		'Copy to a new draft' => [ __( 'Clone post', 'altis-workflow' ) ],
		'Rewrite & Republish' => [ __( 'Create amendment', 'altis-workflow' ) ],
	];

	wp_add_inline_script(
		'duplicate_post_edit_script',
		sprintf(
			'wp.i18n.setLocaleData( %s, \'duplicate-post\' );',
			wp_json_encode( $strings )
		),
		'after'
	);
}

/**
 * Remove and replace the Duplicate Post admin bar.
 *
 * Add our own Clone & Amend admin bar where applicable.
 */
function replace_duplicate_post_admin_menu() {
	global $wp_admin_bar;

	// Bail if the admin bar is turned off.
	if ( ! is_admin_bar_showing() ) {
		return;
	}

	// Don't show the options outside of the admin.
	if ( ! is_admin() ) {
		return;
	}

	// Don't show if the duplicate post menu item isn't present.
	if ( empty( $wp_admin_bar->get_node( 'duplicate-post' ) ) ) {
		return;
	}

	$post = get_post();

	// Don't show the menu if we're not on a post page.
	if ( ! $post ) {
		return;
	}

	// Bail on unsupported post types.
	if ( ! in_array( $post->post_type, get_duplicate_post_types(), true ) ) {
		return;
	}

	$is_amended_post = (bool) get_post_meta( $post->ID, '_dp_is_rewrite_republish_copy', 'true' );
	$top_level_menu = $is_amended_post ? __( 'Clone Post', 'altis-workflow' ) : __( 'Clone & Amend', 'altis-workflow' );
	$linkify = new Link_Builder;

	// Remove the duplicate post admin bar.
	$wp_admin_bar->remove_menu( 'duplicate-post' );

	// Add a new admin bar.
	$wp_admin_bar->add_menu( [
		'id'    => 'clone-amend',
		'title' => '<span class="ab-label">' . $top_level_menu . '</span>',
		'href'  => $linkify->build_new_draft_link( $post ),
	] );

	// Don't display an additional Clone Post menu item or a Create Amendment link if the post is an amendment.
	if ( ! $is_amended_post ) {
		$wp_admin_bar->add_menu( [
			'id'     => 'new-draft',
			'parent' => 'clone-amend',
			'title'  => __( 'Clone Post', 'altis-workflow' ),
			'href'   => $linkify->build_new_draft_link( $post ),
		] );
		$wp_admin_bar->add_menu( [
			'id'     => 'rewrite-republish',
			'parent' => 'clone-amend',
			'title'  => __( 'Create Amendment', 'altis-workflow' ),
			'href'   => $linkify->build_rewrite_and_republish_link( $post ),
		] );
	}
}

/**
 * Override the Rewrite & Republish post notice.
 * Replace with our own notice with updated microcopoy.
 */
function replace_amended_post_notice() {
	$rewriting = ! empty( $_REQUEST['rewriting'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$republished = ! empty( $_REQUEST['dprepublished'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$post = get_post();
	$duplicated = ! empty( get_post_meta( $post->ID, '_dp_has_rewrite_republish_copy', true ) );

	// Bail if not rewriting/amending a post.
	if ( ! $rewriting && ! $republished && ! $duplicated ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	if ( $duplicated ) {
		$text = __( 'An amendment of this post was created. Please note that any changes you make to this post will be replaced when the amended version is published.', 'altis-workflow' );
		$status = 'warning';
		$script = 'has_rewrite_and_republish_notice';
	}

	if ( $rewriting ) {
		$text = __( 'You can now start making changes in this duplicate copy of your original post. When you click "Publish Amendment", this updated version will replace the original post.', 'altis-workflow' );
		$status = 'warning';
		$script = 'rewriting_notice';
	}

	if ( $republished ) {
		$text = __( 'Your original post has been replaced with the amended post. You are now viewing the (amended) original post.', 'altis-workflow' );
		$status = 'success';
		$script = 'republished_notice';
	}

	// Update the notice text.
	$notice = [
		'text' => wp_slash( $text ),
		'status' => $status,
		'isDismissible' => true,
	];

	// Add it to the duplicate_post_edit_script.
	wp_add_inline_script(
		'duplicate_post_edit_script',
		"duplicatePostNotices.$script = '" . wp_json_encode( $notice ) . "';",
		'before'
	);
}
