<?php
/**
 * Data collector class
 */

namespace Altis\Workflow\Notifications;

use QM_Collector;

class QM_Collector_Notifications extends QM_Collector {

	public $id = 'workflow_notifications';

	/**
	 * Defines the name of the collector.
	 *
	 * @return string|void
	 */
	public function name() {
		return __( 'Workflow Notifications', 'altis' );
	}

	/**
	 * Generate the data for the collector output.
	 */
	public function process() {
		$this->data['notifications'] = [];
		$user_id = get_current_user_id();
		$notifications = get_user_meta( $user_id, 'hm.workflows.notification', false );
		foreach ( $notifications as $notification_json ) {
			$notification = json_decode( $notification_json, true );
			$this->data['notifications'][] = $notification;
		}
	}
}
