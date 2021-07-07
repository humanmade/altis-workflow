import { __, setLocaleData } from "@wordpress/i18n";

const republishStrings = {
	'Rewrite & Republish': __( 'Publish Amendment', 'altis-workflow' ),
	'Rewrite &amp; Republish': __( 'Publish Amendment', 'altis-workflow' ),
	'Copy to a new draft': __( 'Clone post', 'altis-workflow' ),
	'Publish': __( 'Publish Amendment', 'altis-workflow' ),
	'Publish:': __( 'Publish Amendment:', 'altis-workflow' ),
	'Publish on:': __( 'Publish Amendment on:', 'altis-workflow' ),
	'Are you ready to publish?': __( 'Are you ready to publish your amendment?', 'altis-workflow' ),
	'Schedule': __( 'Schedule amendment', 'altis-workflow' ),
	'Schedule…': __( 'Schedule amendment…', 'altis-workflow' ),
	'post action/button label\u0004Schedule': __( 'Schedule amendment', 'altis-workflow' ),
	'Are you ready to schedule?': __( 'Are you ready to schedule publishing the amendments to your post?', 'altis-workflow' ),
	'is now scheduled. It will go live on': __( ', the amended post, is now scheduled to replace the original post. It will be published on', 'altis-workflow' ),
};

for ( const original in republishStrings ) {
	setLocaleData( {
		[ original ]: [
			republishStrings[ original ],
			'altis-workflow'
		]
	} );
}
