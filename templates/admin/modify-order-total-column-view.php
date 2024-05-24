<del aria-hidden="true"><?php echo esc_html_e( wc_price( $order->get_total(),
		[ 'currency' => $order->get_currency() ] ) ) ?></del>
<ins><?php echo esc_html_e( wc_price( $capturedAmount, [ 'currency' => $order->get_currency() ] ) ) ?></ins>
