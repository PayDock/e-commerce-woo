/*
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
*/