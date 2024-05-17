<del aria-hidden="true"><?php echo esc_html_e( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ) ?></del>
<ins><?php echo esc_html_e( wc_price( $capturedAmount, array( 'currency' => $order->get_currency() ) ) ) ?></ins>
