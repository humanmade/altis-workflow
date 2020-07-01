<?php

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
						<?php _e( 'ID', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php _e( 'Type', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php _e( 'Time', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php _e( 'Subject', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php _e( 'Text', 'query-monitor' ); ?>
					</th>
					<th scope="col">
						<?php _e( 'Data', 'query-monitor' ); ?>
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
								<?php echo $notification['id']; ?>
							</td>
							<td class="qm-ltr">
								<?php echo $notification['type']; ?>
							</td>
							<td class="qm-ltr">
								<?php echo $notification['time']; ?>
							</td>
							<td class="qm-ltr">
								<?php echo $notification['subject']; ?>
							</td>
							<td class="qm-ltr">
								<?php echo $notification['text']; ?>
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
			_nx( '% notification', '%s notifications', count( $data['notifications'] ), 'Workflow notifications', 'query-monitor' ),
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
			'title' => sprintf( _n( '1 Workflow notification', '%d Workflow notifications', count( $data['notifications'] ), 'query-monitor' ), count( $data['notifications'] ) )
		] );

		return $menu;
	}
}
