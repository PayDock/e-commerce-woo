<script type="text/javascript">
    function powerBoardPaymentCapture(orderId, operation) {
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
</script>
<?php if ($order->get_status() == 'pb-authorize'): ?>
    <button type="button" onclick="powerBoardPaymentCapture(<?php echo $order->get_id(); ?>, 'pb-capture-charge')"
            class="button">
        Capture charge
    </button>
<?php endif; ?>
<button type="button" onclick="powerBoardPaymentCapture(<?php echo $order->get_id(); ?>, 'pb-cancel-authorised')"
        class="button">
    Cancel charge
</button>
