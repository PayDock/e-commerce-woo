<?php
declare( strict_types=1 );
/**
 * This file uses functions (wpautop, wp_kses_post, esc_attr, absint, get_query_var, wp_json_encode and esc_html__) from WordPress
 * This file uses functions (wc_esc_json, is_wc_endpoint_url and wc_get_order) from WooCommerce
 *
 * @noinspection PhpUndefinedFunctionInspection
 * @var array $data
 */

echo wp_kses_post(
	wpautop( $data['description'] )
);

?>
<fieldset id="wc-classic-power-board-checkout" class="wc-payment-form powerboard">
	<div id="classic-powerBoardCheckout_wrapper">
	</div>
	<div id="fields-validation-error" style="display: none;">
		<p class="power-board-validation-error">Please fill in the required fields of the form to display payment methods</p>
	</div>
	<div id="loading">
		<p class="loading-text">Loading...</p>
	</div>

	<input id="chargeid" type="hidden" name="chargeid">
	<input id="checkoutorder" type="hidden" name="checkoutorder">
	<input id="classic-<?php echo esc_attr( $data['id'] ); ?>-nonce" type="hidden" name="_wpnonce"
			value="<?php echo esc_attr( $data['nonce'] ); ?>">
	<input id="classic-<?php echo esc_attr( $data['id'] ); ?>-settings" type="hidden"
			value='<?php echo esc_attr( wc_esc_json( $data['settings'] ) ); ?>'>
	<div id="paymentSourceToken"></div>
</fieldset>
<?php

if ( is_wc_endpoint_url( 'order-pay' ) ) {
	$order_id = get_query_var( 'order-pay' );

	if ( ! empty( $order_id ) ) {
		$order_object = wc_get_order( $order_id );

		if ( ! empty( $order_object ) ) {
			$order_data = [
				'order_id'            => $order_object->get_id(),
				'total_price'         => $order_object->get_total() * 100,
				'total_tax'           => $order_object->get_total_tax() * 100,
				'currency_code'       => get_woocommerce_currency(),
				'currency_symbol'     => get_woocommerce_currency_symbol(),
				'billing_first_name'  => $order_object->get_billing_first_name(),
				'billing_last_name'   => $order_object->get_billing_last_name(),
				'billing_address_1'   => $order_object->get_billing_address_1(),
				'billing_address_2'   => $order_object->get_billing_address_2(),
				'billing_city'        => $order_object->get_billing_city(),
				'billing_state'       => $order_object->get_billing_state(),
				'billing_postcode'    => $order_object->get_billing_postcode(),
				'billing_country'     => $order_object->get_billing_country(),
				'billing_email'       => $order_object->get_billing_email(),
				'billing_phone'       => $order_object->get_billing_phone(),
				'shipping_first_name' => $order_object->get_shipping_first_name(),
				'shipping_last_name'  => $order_object->get_shipping_last_name(),
				'shipping_address_1'  => $order_object->get_shipping_address_1(),
				'shipping_address_2'  => $order_object->get_shipping_address_2(),
				'shipping_city'       => $order_object->get_shipping_city(),
				'shipping_state'      => $order_object->get_shipping_state(),
				'shipping_postcode'   => $order_object->get_shipping_postcode(),
				'shipping_country'    => $order_object->get_shipping_country(),
			];

			?>
			<script type="text/javascript">
				let orderData = <?php echo wp_json_encode( $order_data ); ?>;
			</script>
			<?php
		} else {
			echo esc_html__( 'No order found', 'power-board' );
		}
	}
}
