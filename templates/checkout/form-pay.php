<?php
/**
 * This file uses functions (get_query_var, esc_html_e, apply_filters, esc_attr, wp_kses_post, do_action, esc_html and wp_nonce_field) from WordPress
 * This file uses functions (wc_get_order, wc_display_item_meta, wc_get_template and wc_wp_theme_get_element_class_name) from WooCommerce
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedFunctionInspection
 * @noinspection PhpUndefinedClassInspection
 */

defined( 'ABSPATH' ) || exit;

function get_order_from_query_var(): ?WC_Order {

	$order_id = get_query_var( 'order-pay' );

	if ( ! empty( $order_id ) ) {
		$order = wc_get_order( $order_id );
	} else {
		$order = false;
	}

	return $order;
}

$order = $order ?? get_order_from_query_var();

if ( ! empty( $order ) && is_object( $order ) ) {

	$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	?>
	<form name="checkout" name="checkout" id="order_review" method="post">
		<table class="shop_table">
			<thead>
				<tr>
					<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
					<th class="product-quantity"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
					<th class="product-total"><?php esc_html_e( 'Totals', 'woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( count( $order->get_items() ) > 0 ) : ?>
					<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
						<?php
						if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
							continue;
						}
						?>
						<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
							<td class="product-name">
								<?php
									echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

									do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

									wc_display_item_meta( $item );

									do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
								?>
							</td>
							<td class="product-quantity">
								<?php
									echo apply_filters(
										'woocommerce_order_item_quantity_html',
										' <strong class="product-quantity">' . sprintf(
											'&times;&nbsp;%s',
											esc_html( $item->get_quantity() )
										) . '</strong>',
										$item
									);
								?>
							</td>
							<td class="product-subtotal">
								<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<?php if ( $totals ) : ?>
					<?php foreach ( $totals as $total ) : ?>
						<tr>
							<th scope="row" colspan="2"><?php echo esc_html( $total['label'] ); ?></th>
							<td class="product-total"><?php echo wp_kses_post( $total['value'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tfoot>
		</table>

		<?php
		do_action( 'woocommerce_pay_order_before_payment' );
		?>

		<div id="payment">
			<?php if ( $order->needs_payment() ) : ?>
				<ul class="wc_payment_methods payment_methods methods">
					<?php
					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $gateway ) {
							wc_get_template( 'checkout/payment-method.php', [ 'gateway' => $gateway ] );
						}
					} else {
						echo '<li>';
						wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
						echo '</li>';
					}
					?>
				</ul>
			<?php endif; ?>
			<div class="form-row">
				<input type="hidden" name="woocommerce_pay" value="1" />

				<?php wc_get_template( 'checkout/terms.php' ); ?>

				<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

				<button type="submit" class="button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" id="place_order">
					<?php esc_html_e( 'Pay for order', 'woocommerce' ); ?>
				</button>

				<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

				<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
			</div>
		</div>
	</form>
	<?php
}
?>
