<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Paydock\Enums\OrderListColumns;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\WalletPaymentMethods;
?>
<span class="paydock-order-actions">
    <?php
        $paydock_charge_meta = $order->get_meta( 'paydock_directly_charged' );
        $partiallyRefunded = in_array( $order->get_meta('paydock_refunded_status'), [
          'wc-pb-p-refund',
          'pb-p-refund'
        ] );
        $order_directly_charged = ! empty( $paydock_charge_meta ) ? $paydock_charge_meta : false;
        $showCancelButton = ! in_array( $order->get_meta(OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey()), [
            WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->getLabel(),
            WalletPaymentMethods::AFTERPAY()->getLabel(),
            OtherPaymentMethods::AFTERPAY()->getLabel(),
            OtherPaymentMethods::ZIPPAY()->getLabel(),
          ] ) || $order_directly_charged == false;

        if ( $order_directly_charged == false ) :
    ?>
        <button type="button"
                onclick="paydockPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'paydock-capture-charge')"
                class="button">
			Capture charge
		</button>
    <?php endif; ?>
	  <?php if ( $showCancelButton && !$partiallyRefunded) :?>
          <button type="button"
                  onclick="handlePaydockPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'paydock-cancel-authorised')"
                  class="button">
          Cancel charge
      </button>
    <?php endif; ?>
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
                onclick="handlePaydockPaymentCapture(<?php echo esc_attr( $order->get_id() ); ?>, 'paydock-capture-charge')"
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
