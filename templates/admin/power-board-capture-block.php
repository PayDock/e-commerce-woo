<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>
<span class="power-board-order-actions">
    <?php
        $card_direct_charge = $settings->getCardDirectCharge();

        $pb_charge_meta = get_post_meta( $order->get_id(), 'pb_directly_charged', true );
        $order_directly_charged = ! empty( $pb_charge_meta ) ? $pb_charge_meta : false;

        if ( $card_direct_charge == false && $order_directly_charged == false ) :
    ?>
        <button type="button"
                onclick="powerBoardPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'power-board-capture-charge')"
                class="button">
            Capture charge
        </button>
    <?php endif; ?>
    <button type="button"
            onclick="handlePowerBoardPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'power-board-cancel-authorised')"
            class="button">
        Cancel charge
    </button>
</span>
<div class="wc-order-data-row wc-order-partial-paid-items wc-order-data-row-toggle" style="display: none;">
    <table class="wc-order-totals">
        <tr>
            <td class="label"><?php esc_html_e( 'Total available to capture', 'woocommerce' ); ?>:</td>
            <td class="total available-to-capture" data-value="<?php echo esc_html( $order->get_total() ); ?>">
				<?php echo wp_kses_post( wc_price( $order->get_total(), [ 'currency' => $order->get_currency() ] ) ); ?>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label for="refund_amount">
					<?php esc_html_e( 'Capture amount', 'woocommerce' ); ?>:
                </label>
            </td>
            <td class="total">
                <input type="text" id="capture_amount" onchange="validationManualCapture(jQuery(this))"
                       name="capture_amount"
                       value="<?php echo esc_attr( $order->get_total() ); ?>"
                       class="wc_input_price"/>
                <div class="clear"></div>
            </td>
        </tr>

    </table>
    <div class="clear"></div>
    <div class="refund-actions manual-capture-actions">
		<?php /* translators: capture amount  */ ?>
        <button type="button"
                onclick="handlePowerBoardPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'power-board-capture-charge')"
                class="button button-primary">
			<?php esc_html_e( 'Capture ', 'woocommerce' );
			$currency_symbol = get_woocommerce_currency_symbol( $order->get_currency() );;
			echo esc_html( $currency_symbol );
			?>
            <span class="capture-amount-btn">
                <?php echo esc_attr( $order->get_total() ); ?>
            </span>
			<?php esc_html_e( ' manually', 'woocommerce' ); ?>
        </button>
        <button type="button" class="button cancel-action"
                onclick="cancelActionManualCapture()"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></button>
        <div class="clear"></div>
    </div>
</div>
