<?php

namespace Altis\Workflow;

use QM_Output_Html;
use QM_Collector;

/**
 * Output class
 *
 * Class QM_Output_Notifications
 */
class QM_Output_Notifications extends QM_Output_Html {

	/**
	 * QM_Output_Notifications constructor.
	 *
	 * @param QM_Collector $collector
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );
		add_filter( 'qm/output/menus', [ $this, 'admin_menu' ], 101 );
		add_filter( 'qm/output/title', [ $this, 'admin_title' ], 101 );
		add_filter( 'qm/output/menu_class', [ $this, 'admin_class' ] );
	}

	/**
	 * Outputs data in the footer
	 */
	public function output() {
		$data = $this->collector->get_data();
		?>
		<!-- Print total stats for included files -->
		<div class="qm" id="<?php echo esc_attr( $this->collector->id() ) ?>">
			<table>
				<thead>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'ID', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Type', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Time', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Subject', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Text', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Data', 'query-monitor' ); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				if ( ! empty( $data['notifications'] ) ) {
					foreach ( $data['notifications'] as $notification ) {
						?>
						<tr>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['id'] ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['type'] ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['time'] ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['subject'] ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['text'] ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo wp_json_encode( $notification['data'] ); ?>
							</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Adds data to top admin bar
	 *
	 * @param array $title
	 *
	 * @return array
	 */
	public function admin_title( array $title ) : array {
		$data = $this->collector->get_data();

		$title[] = sprintf(
			/* translators: the number of notifications */
			_nx( '%d notification', '%d notifications', count( $data['notifications'] ), 'Workflow notifications', 'query-monitor' ),
			count( $data['notifications'] )
		);

		return $title;
	}

	/**
	 * @param array $class
	 *
	 * @return array
	 */
	public function admin_class( array $class ) {
		$class[] = 'qm-workflow_notifications';

		return $class;
	}

	/**
	 * @param array $menu
	 *
	 * @return array
	 */
	public function admin_menu( array $menu ) :array {

		$data = $this->collector->get_data();

		$menu[] = $this->menu( [
			'id'    => 'qm-workflow_notifications',
			'href'  => '#qm-workflow_notifications',
			/* translators: the number of notifications */
			'title' => sprintf( _n( '%d Workflow notification', '%d Workflow notifications', count( $data['notifications'] ), 'query-monitor' ), count( $data['notifications'] ) ),
		] );

		return $menu;
	}
}
