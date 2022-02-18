<?php
/**
 * QM Notifications output class.
 *
 * @package altis/workflow
 */

namespace Altis\Workflow\Notifications;

use QM_Collector;
use QM_Output_Html;

/**
 * Output class
 *
 * Class QM_Output_Notifications
 */
class QM_Output_Notifications extends QM_Output_Html {

	/**
	 * QM_Output_Notifications constructor.
	 *
	 * @param QM_Collector $collector Collector object instance.
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );
		add_filter( 'qm/output/menus', [ $this, 'admin_menu' ], 101 );
	}

	/**
	 * Defines the name of the collector.
	 *
	 * @return string|void
	 */
	public function name() {
		return __( 'Workflow Notifications', 'altis' );
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
						<?php esc_html_e( 'ID', 'altis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Type', 'altis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Time', 'altis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Subject', 'altis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Text', 'altis' ); ?>
					</th>
					<th scope="col">
						<?php esc_html_e( 'Data', 'altis' ); ?>
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
								<?php echo esc_html( $notification['id'] ?? '' ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['type'] ?? '' ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['time'] ?? '' ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['subject'] ?? '' ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo esc_html( $notification['text'] ?? '' ); ?>
							</td>
							<td class="qm-ltr">
								<?php echo wp_json_encode( $notification['data'] ?? '' ); ?>
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
	 * Add menu item for panel.
	 *
	 * @param array $menu The Query Monitor menu item array.
	 * @return array
	 */
	public function admin_menu( array $menu ) :array {

		$data = $this->collector->get_data();

		$menu[] = $this->menu( [
			'id'    => 'qm-altis-notifications',
			'href'  => '#qm-altis-notifications',
			/* translators: the number of notifications */
			'title' => sprintf( _n( '%d Workflow notification', '%d Workflow notifications', count( $data['notifications'] ), 'altis' ), count( $data['notifications'] ) ),
		] );

		return $menu;
	}
}
