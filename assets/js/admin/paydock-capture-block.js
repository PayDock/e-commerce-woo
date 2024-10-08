function paydockPaymentCapture() {
    jQuery('span.paydock-order-actions').hide();
    jQuery('div.wc-order-totals-items').slideUp();
    jQuery('#woocommerce-order-items').find('div.wc-order-partial-paid-items').show();
}

function cancelActionManualCapture() {
    jQuery('span.paydock-order-actions').show();
    jQuery('div.wc-order-totals-items').slideDown();
    jQuery('#woocommerce-order-items').find('div.wc-order-partial-paid-items').hide();
}

function validationManualCapture(elem) {
    const captureAmount = Number(jQuery('#capture_amount').val());
    const captureAmountBtn = jQuery('#woocommerce-order-items').find('div.wc-order-partial-paid-items .capture-amount-btn')
    const availableToCapture = Number(jQuery('div.wc-order-partial-paid-items').find('.available-to-capture').data('value'));
    if (captureAmount > availableToCapture) {
        alert("You cannot capture be greater than order total " + availableToCapture);
        elem.val(availableToCapture);
        captureAmountBtn.text(availableToCapture.toFixed(2));
        return false;
    }

    if (captureAmount <= 0) {
        alert("This field should be positive");
        elem.val(availableToCapture);
        captureAmountBtn.text(availableToCapture.toFixed(2));
        return false;
    }

    elem.val(captureAmount);
    captureAmountBtn.text(captureAmount.toFixed(2));
    return true;
}

function handlePaydockPaymentCapture(orderId, operation) {
    if (operation === 'paydock-capture-charge') {
        let validSumManualCapture = validationManualCapture(jQuery('#capture_amount'));
        if (!validSumManualCapture) {
            return;
        }
    }
    const captureAmount = Number(jQuery('#capture_amount').val());
    jQuery.blockUI({
        message: '',
        overlayCSS: {backgroundColor: '#fff', opacity: 0.6, cursor: 'wait'}
    });
    jQuery.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
            action: operation,
            order_id: orderId,
            amount: captureAmount,
            _wpnonce: window.paydockCaptureBlockSettings.wpnonce
        },
        success: function (response) {
            if (response.data.message !== undefined) {
                alert(response.data.message);
            }
            if (response.success == 1) {
                location.reload();
            } else {
                jQuery.unblockUI();
            }
        }
    });
}
