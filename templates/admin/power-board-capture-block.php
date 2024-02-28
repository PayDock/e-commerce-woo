<script type="text/javascript">
    function power_boardPaymentCapture(orderId, operation) {
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
<button type="button" onclick="power_boardPaymentCapture(<?php echo $order->get_id(); ?>, 'power_board-capture-charge')"
        class="button">
    Capture charge
</button>
<button type="button" onclick="power_boardPaymentCapture(<?php echo $order->get_id(); ?>, 'power_board-cancel-authorised')"
        class="button">
    Cancel charge
</button>