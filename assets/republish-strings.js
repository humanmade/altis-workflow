/* global altisRepublishStrings */
import React from 'react';

import { Button } from '@wordpress/components';
import { dispatch, select, subscribe } from '@wordpress/data';
import { createInterpolateElement } from '@wordpress/element';
import { setLocaleData } from '@wordpress/i18n';

/**
 * Saves the current post and redirects to the revision screen.
 *
 * This is copy pasta of an unexported function in duplicate-post/js/src/duplicate-post-strings.js.
 */
const saveAndCompare = () => {
	dispatch( 'core/editor' ).savePost();

	let wasSavingPost      = false;
	let wasSavingMetaboxes = false;
	let wasAutoSavingPost  = false;

	/**
	 * Determines when the redirect needs to happen.
	 *
	 * @returns {void}
	 */
	subscribe( () => {
		const completed = redirectOnSaveCompletion(
			altisRepublishStrings.checkLink, {
				wasSavingPost,
				wasSavingMetaboxes,
				wasAutoSavingPost,
			}
		);

		wasSavingPost      = completed.isSavingPost;
		wasSavingMetaboxes = completed.isSavingMetaBoxes;
		wasAutoSavingPost  = completed.isAutosavingPost;
	} );
};

/**
 * Redirects to url when saving in the block editor has completed.
 *
 * This is copy pasta of a function in duplicate-post/js/src/duplicate-post-functions.js. While this function is exported, using the exported function would require locking Duplicate Post to a specific version since the uncompiled files are not part of the package.
 *
 * @param {string} url         The url to redirect to.
 * @param {object} editorState The current editor state regarding saving the post, metaboxes and autosaving.
 *
 * @returns {object} The updated editor state.
 */
const redirectOnSaveCompletion = ( url, editorState ) => {
	const isSavingPost       = select( 'core/editor' ).isSavingPost();
	const isAutosavingPost   = select( 'core/editor' ).isAutosavingPost();
	const hasActiveMetaBoxes = select( 'core/edit-post' ).hasMetaBoxes();
	const isSavingMetaBoxes  = select( 'core/edit-post' ).isSavingMetaBoxes();

	// When there are custom meta boxes, redirect after they're saved.
	if ( hasActiveMetaBoxes && ! isSavingMetaBoxes && editorState.wasSavingMetaboxes ) {
		redirectWithoutWarning( url );
	}

	// When there are no custom meta boxes, redirect after the post is saved.
	if ( ! hasActiveMetaBoxes && ! isSavingPost && editorState.wasSavingPost && ! editorState.wasAutoSavingPost ) {
		redirectWithoutWarning( url );
	}

	return {
		isSavingPost,
		isSavingMetaBoxes,
		isAutosavingPost,
	};
};

/**
 * This redirects without showing the warning that occurs due to a Gutenberg bug.
 *
 * Edits made to the post on the PHP side are not correctly recognized and thus the warning for unsaved changes is shown.
 * By updating the post status ourselves on the JS side as well we avoid this.
 *
 * This is copy pasta of an unexported function in duplicate-post/js/src/duplicate-post-functions.js.
 *
 * @param {string} url The url to redirect to.
 *
 * @returns {void}
 */
const redirectWithoutWarning = url => {
	const currentPostStatus = select( 'core/editor' ).getCurrentPostAttribute( 'status' );
	const editedPostStatus  = select( 'core/editor' ).getEditedPostAttribute( 'status' );

	if ( currentPostStatus === 'dp-rewrite-republish' && editedPostStatus === 'publish' ) {
		dispatch( 'core/editor' ).editPost( { status: currentPostStatus } );
	}

	window.location.assign( url );
};

/**
 * The strings to replace.
 */
const republishStrings = {
	'Publish': altisRepublishStrings.publish,
	'Publish:': altisRepublishStrings.publishColon,
	'Publish on:': altisRepublishStrings.publishOn,
	'Are you ready to publish?': altisRepublishStrings.readyToPublish,
	'Schedule': altisRepublishStrings.scedule,
	'Schedule…': altisRepublishStrings.scheduleEllipses,
	'post action/button label\u0004Schedule': altisRepublishStrings.schedule,
	'Are you ready to schedule?': altisRepublishStrings.readyToSchedule,
	'is now scheduled. It will go live on': altisRepublishStrings.nowScheduled,
	'Double-check your settings before publishing.': createInterpolateElement(
		altisRepublishStrings.changesMerged,
		{
			button: <Button isSecondary onClick={ saveAndCompare } />,
			br: <br />,
		}
	),
	'Your work will be published at the specified date and time.': createInterpolateElement(
		altisRepublishStrings.scheduledCheck,
		{
			button: <Button isSecondary onClick={ saveAndCompare } />,
			br: <br />,
		}
	),
};

/**
 * Loop through and replace strings.
 */
for ( const original in republishStrings ) {
	setLocaleData( {
		[ original ]: [
			republishStrings[ original ],
			'duplicate-post',
		],
	} );
}
