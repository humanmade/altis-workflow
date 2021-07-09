/* global altisRepublishStrings */
import { setLocaleData } from '@wordpress/i18n';
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
};

for ( const original in republishStrings ) {
	setLocaleData( {
		[ original ]: [
			republishStrings[ original ],
			'duplicate-post',
		],
	} );
}
