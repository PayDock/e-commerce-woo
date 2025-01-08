<?php
/**
 * @var WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $order ) || ! $order instanceof WC_Order ) {
	exit;
}

if ( empty( $captured_amount ) ) {

	$captured_amount = $order->get_meta( 'capture_amount' );

	if ( empty( $captured_amount ) ) {
		$captured_amount = 0;
	}

}

?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		const capturedAmount = jQuery('.wc-order-totals .captured-amount');

		const availableToRefundHtml = jQuery('.wc-order-refund-items').find("td.label:contains('available to refund')").parent().find('.total');
		const netPaymentHtml = jQuery('.wc-order-totals-items').find("td.label:contains('Net Payment')").parent().find('.total');

		function updateElementContent(element, content) {
			element.html(content);
			element.find('.amount').remove();
			element.find('.available-to-refund-amount').removeClass('hidden');
		}

		function updateAmountValues(sourceElement, targetElement) {
			const amountHtml = sourceElement.html();
			updateElementContent(targetElement, amountHtml);
		}

		if (availableToRefundHtml.length) {
			updateAmountValues(capturedAmount, availableToRefundHtml);
		}

		if (netPaymentHtml.length) {
			updateAmountValues(capturedAmount, netPaymentHtml);
		}
	});
</script>
<table class="wc-order-totals" style="border-top: 1px solid #999; color: #5b841b; margin-top:12px; padding-top:12px">
	<tbody>
	<tr>
		<td class="label"><?php echo esc_html__( 'Captured Amount', 'power-board' ); ?>:</td>
		<td width="1%"></td>
		<td class="total  captured-amount">
			<span class="woocommerce-Price-amount">
				<bdi>
					<span class="woocommerce-Price-currencySymbol">
						<?php
							$currency_symbol = get_woocommerce_currency_symbol( $order->get_currency() );
							echo esc_html( $currency_symbol );
						?>
					</span>
					<span class="amount"><?php echo esc_html( number_format( (float) $captured_amount, 2, '.', '' ) ); ?></span>
					<span class="available-to-refund-amount hidden">
						<?php echo esc_html( number_format( $captured_amount - $order->get_total_refunded(), 2, '.', '' ) ); ?>
					</span>
				</bdi>
			</span>
		</td>
	</tr>
	</tbody>
</table>
