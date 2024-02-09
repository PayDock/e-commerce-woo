jQuery(function ($) {
    setTimeout(() => {
        const idPaydockWidgetCard = 'radio-control-wc-payment-method-options-paydock_gateway';
        const idPaydockWidgetBankAccount = 'radio-control-wc-payment-method-options-paydock_bank_account_gateway';
        const $radioWidgetCard = $('#' + idPaydockWidgetCard);
        const $radioWidgetBankAccount = $('#' + idPaydockWidgetBankAccount);
        const paydockCardSettings = window.wc.wcSettings.getSetting('paydock_data', {});
        const paydockBankAccountSettings = window.wc.wcSettings.getSetting('paydock_bank_account_block_data', {});

        $radioWidgetCard.on('change', function () {
            initPaydockWidgetCard();
        });

        $radioWidgetBankAccount.on('change', function () {
            initPaydockWidgetBankAccount();
        });

        if ($radioWidgetCard.attr('checked') === 'checked') {
            initPaydockWidgetCard();
        } else if ($radioWidgetBankAccount.attr('checked') === 'checked') {
            initPaydockWidgetBankAccount();
        }

        function initPaydockWidgetBankAccount() {
            if ($('#paydockWidgetBankAccount').length === 0) {
                return;
            }

            let gateway = paydockBankAccountSettings.saveAccount
                ? paydockBankAccountSettings.gatewayId
                : 'not_configured';

            var bankAccount = new paydock.Configuration(gateway, 'bank_account');
            bankAccount.setFormFields(['account_routing']);
            var widget = new paydock.HtmlMultiWidget(
                '#paydockWidgetBankAccount',
                paydockBankAccountSettings.publicKey,
                [bankAccount]
            );

            window.widgetBankAccount = widget;
            widget.setStyles(paydockCardSettings.styles);
            const texts = {};
            if (typeof paydockBankAccountSettings.title !== "undefined") {
                texts.title = paydockBankAccountSettings.title;
            }
            if (typeof paydockBankAccountSettings.description !== "undefined") {
                texts.title_description = paydockBankAccountSettings.description;
            }

            widget.setTexts(texts);

            if (typeof paydockCardSettings.styles.custom_elements !== "undefined") {
                $.each(paydockCardSettings.styles.custom_elements, function (element, styles) {
                    widget.setElementStyle(element, styles);
                });
            }

            widget.onFinishInsert('input[name="payment_source_bank_account_token"]', 'payment_source');
            widget.hideElements(['submit_button']);
            widget.interceptSubmitForm('#widget');
            widget.load();
        }

        function initPaydockWidgetCard() {
            if ($('#paydockWidgetCard').length === 0) {
                return;
            }

            if (!paydockCardSettings.cardSaveCard && paydockCardSettings.card3DS !== 'IN_BUILD' && paydockCardSettings.card3DS !== 'STANDALONE') {
                widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockCardSettings.publicKey, 'not_configured');
            } else {
                widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockCardSettings.publicKey, paydockCardSettings.gatewayId);
            }

            window.widget = widget;
            widget.setStyles(paydockCardSettings.styles);

            const texts = {};
            if (typeof paydockCardSettings.paymentCardTitle !== "undefined") {
                texts.title = paydockCardSettings.paymentCardTitle;
            }
            if (typeof paydockCardSettings.paymentCardDescription !== "undefined") {
                texts.title_description = paydockCardSettings.paymentCardDescription;
            }
            widget.setTexts(texts);

            if (typeof paydockCardSettings.styles.custom_elements !== "undefined") {
                $.each(paydockCardSettings.styles.custom_elements, function (element, styles) {
                    widget.setElementStyle(element, styles);
                });
            }

            if (paydockCardSettings.cardSupportedCardTypes !== '') {
                supportedCard = paydockCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')
                widget.setSupportedCardIcons(supportedCard);
            }

            widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
            widget.hideElements(['submit_button', 'email']);
            widget.interceptSubmitForm('#widget');
            widget.load();
        }
    }, 1000)
});
