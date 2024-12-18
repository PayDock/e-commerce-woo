<?php
echo wpautop( wp_kses_post( esc_attr( $description ) ) );
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
    <input id="classic-<?php echo esc_attr( $id ) ?>-nonce" type="hidden" name="_wpnonce"
           value="<?php echo esc_attr( $nonce ) ?>">
    <input id="classic-<?php echo esc_attr( $id ) ?>-settings" type="hidden"
           value='<?php echo esc_attr( wc_esc_json( $settings ) ); ?>'>
    <div id="paymentSourceToken"></div>
</fieldset>

<?php

$is_order_pay_page = is_wc_endpoint_url( 'order-pay' ) ? 'true' : 'false';

if ( $is_order_pay_page === 'true' ) {

    $order_id = absint( get_query_var( 'order-pay' ) );
    $order = wc_get_order( $order_id );

    if ( $order ) {

        $order_data = array(
            'billing_first_name'  => $order->get_billing_first_name(),
            'billing_last_name'   => $order->get_billing_last_name(),
            'billing_address_1'   => $order->get_billing_address_1(),
            'billing_address_2'   => $order->get_billing_address_2(),
            'billing_city'        => $order->get_billing_city(),
            'billing_state'       => $order->get_billing_state(),
            'billing_postcode'    => $order->get_billing_postcode(),
            'billing_country'     => $order->get_billing_country(),
            'billing_email'       => $order->get_billing_email(),
            'billing_phone'       => $order->get_billing_phone(),
            'shipping_first_name' => $order->get_shipping_first_name(),
            'shipping_last_name'  => $order->get_shipping_last_name(),
            'shipping_address_1'  => $order->get_shipping_address_1(),
            'shipping_address_2'  => $order->get_shipping_address_2(),
            'shipping_city'       => $order->get_shipping_city(),
            'shipping_state'      => $order->get_shipping_state(),
            'shipping_postcode'   => $order->get_shipping_postcode(),
            'shipping_country'    => $order->get_shipping_country(),
        );

        ?>
        <script type="text/javascript">
            var orderData = <?php echo json_encode( $order_data ); ?>;
            var isOrderPayPage = <?php echo $is_order_pay_page; ?>;
        </script>
        <?php

    } else {
        echo 'No order found';
        exit;
    }

} else {

    ?>
    <script type="text/javascript">
        var isOrderPayPage = false;
    </script>
    <?php

}
