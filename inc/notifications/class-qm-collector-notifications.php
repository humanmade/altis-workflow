<?php
/**
 * Data collector class.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow\Notifications;

use QM_Collector;

/**
 * QM Collector for Notifications.
 */
class QM_Collector_Notifications extends QM_Collector {

	/**
	 * Collector ID.
	 *
	 * @var string
	 */
	public $id = 'altis-notifications';

	/**
	 * Generate the data for the collector output.
	 */
	public function process() {
		$this->data['notifications'] = [];
		$user_id = get_current_user_id();

		if ( empty( $user_id ) ) {
			// QM can be used when logged out with an authentication cookie.
			return;
		}

		$notifications = get_user_meta( $user_id, 'hm.workflows.notification', false );
		foreach ( $notifications as $notification_json ) {
			$notification = json_decode( $notification_json, true );
			$this->data['notifications'][] = $notification;
		}
	}
}
