/* global altisAmendPost */

import React from 'react';
import { Button } from '@wordpress/components';
import { select } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { Fragment } from '@wordpress/element';
import { registerPlugin, unregisterPlugin } from '@wordpress/plugins';

unregisterPlugin( 'duplicate-post' );
registerPlugin( 'altis-amend-post', {
	render: () => {
		const currentPostStatus = select( 'core/editor' ).getEditedPostAttribute( 'status' );

		return (
			<Fragment>
				{ ( altisAmendPost.newDraftLink !== '' ) &&
					<PluginPostStatusInfo>
						<Button
							isTertiary={ true }
							className="dp-editor-post-copy-to-draft"
							href={ altisAmendPost.newDraftLink }
						>
							{ altisAmendPost.clonePost }
						</Button>
					</PluginPostStatusInfo>
				}
				{ ( currentPostStatus === 'publish' && altisAmendPost.amendLink !== '' ) &&
					<PluginPostStatusInfo>
						<Button
							isTertiary={ true }
							className="dp-editor-post-rewrite-republish"
							href={ altisAmendPost.amendLink }
						>
							{ altisAmendPost.amendPost }
						</Button>
					</PluginPostStatusInfo>
				}
			</Fragment>
		);
	}
} );
