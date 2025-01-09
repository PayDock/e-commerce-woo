<?php
/**
 * @var array $data
 * @var WC_Order $order
 * @var float $capturedAmount
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $order ) ) {

	$order_id = isset( $data['order_id'] ) ? $data['order_id'] : 0;

	if ( ! empty( $order_id ) ) {
		$order = wc_get_order( $order_id );
	}

}

if ( $order instanceof WC_Order ) {

	if ( ! empty( $capturedAmount ) ) {
		$capturedAmount = (float) $capturedAmount;
	} else {
		$capturedAmount = 0;
	}

	?>
	<del aria-hidden="true"><?php wc_price( $order->get_total(), [ 'currency' => $order->get_currency() ] ) ?></del><ins><?php wc_price( $capturedAmount, [ 'currency' => $order->get_currency() ] ) ?></ins>
	<?php

}
