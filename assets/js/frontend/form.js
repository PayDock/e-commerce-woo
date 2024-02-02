jQuery(function ($) {
    setTimeout(() => {
        const idPaydockWidgetCard = 'radio-control-wc-payment-method-options-paydock_gateway';
        const $radio = $('#' + idPaydockWidgetCard);
        const paydockSettings = window.wc.wcSettings.getSetting('paydock_data', {});

        if ($radio.attr('checked') === 'checked') {
            initPaydockWidgetCard();
        }

        $radio.on('change', function () {
            initPaydockWidgetCard();
        });

        function initPaydockWidgetCard() {
            if ($('#paydockWidgetCard').length === 0) {
                return;
            }

            const widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockSettings.publicKey, paydockSettings.gatewayId);
            window.widget = widget;
            // widget.setStyles(paydockSettings.styles);

            const texts = {};
            if (typeof paydockSettings.paymentCardTitle !== "undefined") {
                texts.title = paydockSettings.paymentCardTitle;
            }
            if (typeof paydockSettings.paymentCardDescription !== "undefined") {
                texts.title_description = paydockSettings.paymentCardDescription;
            }
            widget.setTexts(texts);

            if (typeof paydockSettings.styles.custom_elements !== "undefined") {
                $.each(paydockSettings.styles.custom_elements, function (element, styles) {
                    console.log(element, styles)
                    widget.setElementStyle(element, styles);
                });
            }

            widget.setSupportedCardIcons(['mastercard', 'visa']);
            widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
            widget.hideElements(['submit_button', 'email']);
            widget.interceptSubmitForm('#widget');
            widget.load();
        }
    }, 1000)
});
