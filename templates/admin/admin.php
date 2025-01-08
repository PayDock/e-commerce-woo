<?php
/**
 * @var array $tabs
 * @var \PowerBoard\Services\TemplateService $template_service
 * @var array $records
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
	<?php foreach ( $tabs as $key => $value ) : ?>
		<a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=<?php echo esc_attr( $key ); ?>"
			class="nav-tab <?php echo $value['active'] ? 'nav-tab-active' : ''; ?>">
			<?php echo esc_html( $value['label'] ); ?>
		</a>
	<?php endforeach; ?>
</nav>
<?php if ( 'power_board_log' !== $this->current_section && isset( $form_fields ) ) : ?>
	<?php $template_service->setting_service->parent_generate_settings_html( $form_fields, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  --  the following require is safe it is not a user input. ?>
<?php else : ?>
	<table class="wp-list-table widefat fixed striped table-view-list orders wc-orders-list-table wc-orders-list-table-shop_order">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-order_status sorted
				<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'id' === $records['orderBy'] ) ) : ?>
					asc
					<?php else : ?>
					desc
				<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'id',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span>ID</span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
				<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'created_at' === $records['orderBy'] ) ) : ?>
					asc
					<?php else : ?>
					desc
				<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'created_at',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Date', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
				<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'operation' === $records['orderBy'] ) ) : ?>
					asc
					<?php else : ?>
					desc
				<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'operation',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Operation', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
			<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'status' === $records['orderBy'] ) ) : ?>
				asc
				<?php else : ?>
				desc
			<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'status',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Status', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status"><?php echo esc_html__( 'Message', 'power-board' ); ?></th>
		</tr>
		</thead>
		<tbody id="the-list" data-wp-lists="list:order">
		<?php if ( empty( $records['data'] ) ) : ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="3"><?php echo esc_html__( 'No items found', 'power-board' ); ?></td>
			</tr>
		<?php else : ?>
			<?php foreach ( $records['data'] as $record ) : ?>
				<tr>
					<td>
						<?php echo esc_html( $record->id ); ?>
					</td>
					<td class="order_date column-order_date">
						<?php echo esc_html( $record->created_at ); ?>
					</td>
					<td>
						<?php echo esc_html( $record->operation ); ?>
					</td>
					<?php
						$allowed_statuses = [ 'completed', 'processing', 'on-hold', 'failed', 'refunded', 'cancelled', 'pending' ];
						$status = in_array( $record->status, $allowed_statuses ) ? $record->status : 'pending';
						$status_class = 'order-status status-' . sanitize_html_class( $status ) . ' tips';
					?>
					<td>
						<mark class="<?php echo esc_attr( $status_class ); ?>">
							<span><?php echo esc_html( ucfirst( $status ) ); ?></span>
						</mark>
					</td>
					<td>
						<?php echo esc_html( $record->message ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
		<tfoot>
		<tr>
			<th scope="col" class="manage-column column-order_status sorted
				<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'id' === $records['orderBy'] ) ) : ?>
					asc
					<?php else : ?>
					desc
				<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'id',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span>ID</span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
			<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'id' === $records['orderBy'] ) ) : ?>
				asc
				<?php else : ?>
				desc
			<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'created_at',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Date', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
			<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'id' === $records['orderBy'] ) ) : ?>
				asc
				<?php else : ?>
				desc
			<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'operation',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Operation', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status sorted
				<?php if ( ! empty( $records['order'] ) && ( 'asc' === $records['order'] ) && ( 'id' === $records['orderBy'] ) ) : ?>
					asc
					<?php else : ?>
					desc
				<?php endif; ?>
			">
				<a href="
					<?php
						echo esc_url(
							add_query_arg(
								array(
									'orderBy' => 'status',
									'order'   => 'desc' === $records['order'] ? 'asc' : 'desc',
								)
							)
						);
					?>
					">
					<span><?php echo esc_html__( 'Status', 'power-board' ); ?></span>
					<span class="sorting-indicators">
						<span class="sorting-indicator asc" aria-hidden="true"></span>
						<span class="sorting-indicator desc" aria-hidden="true"></span>
					</span>
				</a>
			</th>
			<th scope="col" class="manage-column column-order_status"><?php echo esc_html__( 'Message', 'power-board' ); ?></th>
		</tr>
		</tfoot>
	</table>
	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions">
		</div>
		<div class="alignleft actions"></div>
		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php echo 'From ' . esc_html( $records['from'] ) . ' to ' . esc_html( $records['to'] ) . ' of ' . esc_html( $records['count'] ); ?>
				records
			</span>
			<span class="pagination-links">
				<?php if ( $records['current'] <= 1 ) : ?>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
				<?php else : ?>
					<a class="next-page button"
						href="<?php echo esc_url( add_query_arg( array( 'page_number' => 1 ) ) ); ?>">
						<span class="screen-reader-text">First page</span>
						<span aria-hidden="true">«</span>
					</a>
					<a class="last-page button"
						href="<?php echo esc_url( add_query_arg( array( 'page_number' => $records['current'] - 1 ) ) ); ?>">
						<span class="screen-reader-text">Prev page</span>
						<span aria-hidden="true">‹</span>
					</a>
				<?php endif; ?>
				<span class="screen-reader-text"><?php echo esc_html__( 'Current Page', 'power-board' ); ?></span>
				<span id="table-paging" class="paging-input">
					<span class="tablenav-paging-text">
						<?php echo esc_html( $records['current'] ); ?> of <span
								class="total-pages"><?php echo esc_html( $records['last_page'] ); ?></span>
					</span>
				</span>
				<?php if ( $records['current'] >= $records['last_page'] ) : ?>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
				<?php else : ?>
					<a class="next-page button"
						href="<?php echo esc_url( add_query_arg( array( 'page_number' => $records['current'] + 1 ) ) ); ?>">
						<span class="screen-reader-text">Next page</span>
						<span aria-hidden="true">›</span>
					</a>
					<a class="last-page button"
						href="<?php echo esc_url( add_query_arg( array( 'page_number' => $records['last_page'] ) ) ); ?>">
						<span class="screen-reader-text">Last page</span>
						<span aria-hidden="true">»</span>
					</a>
				<?php endif; ?>
			</span>
		</div>
		<br class="clear">
	</div>

	<?php for ( $i = 1; $i <= $records['last_page']; $i++ ) : ?>

	<?php endfor; ?>
<?php endif; ?>
