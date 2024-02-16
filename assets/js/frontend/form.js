jQuery(function ($) {
    var lastInit = '';
    setTimeout(() => {
        const idPaydockWidgetCard = 'radio-control-wc-payment-method-options-paydock_gateway';
        const idPaydockWidgetBankAccount = 'radio-control-wc-payment-method-options-paydock_bank_account_gateway';
        const idPaydockWidgetWallets = 'radio-control-wc-payment-method-options-paydock_wallets_gateway';
        const idPaydockWidgetApm = 'radio-control-wc-payment-method-options-paydock_apms_gateway';
        const $radioWidgetCard = $('#' + idPaydockWidgetCard);
        const $radioWidgetBankAccount = $('#' + idPaydockWidgetBankAccount);
        const $radioWidgetApm = $('#' + idPaydockWidgetApm);

        $radioWidgetCard.on('change', function () {
            initPaydockWidgetCard();
        });

        $radioWidgetBankAccount.on('change', function () {
            initPaydockWidgetBankAccount();
        });

        $radioWidgetApm.on('change', function () {
            initPaydockWidgetApm();
        });

        if ($radioWidgetCard.attr('checked') === 'checked') {
            initPaydockWidgetCard();
        } else if ($radioWidgetBankAccount.attr('checked') === 'checked') {
            initPaydockWidgetBankAccount();
        } else if ($radioWidgetApm.attr('checked') === 'checked') {
            initPaydockWidgetApm();
        }

        function initPaydockWidgetApm() {
            if ($('#paydockWidgetApm').length === 0) {
                return;
            }

            lastInit = idPaydockWidgetApm;

            const paydockApmSettings = window.wc.wcSettings.getSetting('paydock_apms_data', {});
            paydockApmSettings.email = $('#email').val()

            const zippay = new paydock.ZipmoneyCheckoutButton('#zippay', paydockApmSettings.publicKey, paydockApmSettings.zippayGatewayId);
            zippay.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token');
            zippay.setMeta({
                charge: {
                    amount: paydockApmSettings.amount,
                    currency: paydockApmSettings.currency,
                }
            });

            zippay.on('finish', function () {
                paydockApmSettings.gatewayType = 'zippay'
                if (paydockApmSettings.zippaySaveCard) {
                    paydockApmSettings.apmSaveCard = true
                }
                if (paydockApmSettings.zippayDirectCharge) {
                    paydockApmSettings.directCharge = true
                }
                if (paydockApmSettings.zippayFraud) {
                    paydockApmSettings.fraud = true
                    paydockApmSettings.fraudServiceId = paydockApmSettings.zippayFraudServiceId
                }
                paydockApmSettings.gatewayId = paydockApmSettings.zippayGatewayId
                document.getElementById('paydockWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                const saveCard = document.querySelector('.amps-save-card');
                if (paydockApmSettings.zippaySaveCard && saveCard !== null) {
                    saveCard.setAttribute('style', '')
                }
            });

            const afterpay = new paydock.AfterpayCheckoutButton('#afterpay', paydockApmSettings.publicKey, paydockApmSettings.afterpayGatewayId)
            afterpay.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token')
            afterpay.showEnhancedTrackingProtectionPopup(true)
            afterpay.setMeta({
                amount: paydockApmSettings.amount,
                currency: paydockApmSettings.currency,
                email: paydockApmSettings.email,
                first_name: $('#shipping-first_name').val(),
                last_name: $('#shipping-last_name').val(),
            });

            afterpay.on('finish', function () {
                paydockApmSettings.gatewayType = 'afterpay'
                if (paydockApmSettings.afterpaySaveCard) {
                    paydockApmSettings.apmSaveCard = true
                }
                if (paydockApmSettings.afterpayDirectCharge) {
                    paydockApmSettings.directCharge = true
                }
                if (paydockApmSettings.afterpayFraud) {
                    paydockApmSettings.fraud = true
                    paydockApmSettings.fraudServiceId = paydockApmSettings.afterpayFraudServiceId
                }
                paydockApmSettings.gatewayId = paydockApmSettings.afterpayGatewayId
                document.getElementById('paydockWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                const saveCard = document.querySelector('.amps-save-card');
                if (paydockApmSettings.afterpaySaveCard && saveCard !== null) {
                    saveCard.setAttribute('style', '')
                }
            });
        }

        function initPaydockWidgetBankAccount() {
            if ($('#paydockWidgetBankAccount').length === 0) {
                return;
            }
            lastInit = idPaydockWidgetBankAccount;

            const paydockBankAccountSettings = window.wc.wcSettings.getSetting('paydock_bank_account_block_data', {});

            const gateway = paydockBankAccountSettings.bankAccountSaveAccount
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
            widget.setStyles(paydockBankAccountSettings.styles);

            if (typeof paydockBankAccountSettings.styles.custom_elements !== "undefined") {
                $.each(paydockBankAccountSettings.styles.custom_elements, function (element, styles) {
                    widget.setElementStyle(element, styles);
                });
            }

            widget.onFinishInsert('input[name="payment_source_bank_account_token"]', 'payment_source');
            widget.hideElements(['submit_button']);
            widget.interceptSubmitForm('#widget');
            widget.load();
        }

        function initPaydockWidgetWallets(orderButton) {
            const paydockWalletsSettings = window.wc.wcSettings.getSetting('paydock_wallets_block_data', {});
            if (
                (
                    ($('#paydockWalletsApplePay').length === 0)
                    && ($('#paydockWalletsGooglePay').length === 0)
                    && ($('#paydockWalletsAfterPay').length === 0)
                    && ($('#paydockWalletsPaypal').length === 0)
                )
                || (undefined === window.paydockWallets)
            ) {
                return;
            }
            lastInit = idPaydockWidgetWallets;

            for (let key in window.paydockWallets) {
                let id = '#';
                let height = 45;
                if ('apple_pay' === key) {
                    id += 'paydockWalletsGoogleApplePay'
                    height = 45;
                } else if ('google_pay' === key) {
                    id += 'paydockWalletsGoogleApplePay'
                    height = 45;
                } else if ('afterpay' === key) {
                    id += 'paydockWalletsAfterPay'
                    height = 35;
                } else if ('pay_pal_smart_button' === key) {
                    id += 'paydockWalletsPaypal'
                    height = 55;
                }
                let button = new paydock.WalletButtons(
                    id,
                    window.paydockWallets[key].resource.data.token,
                    {
                        country: window.paydockWallets[key].county,
                        style: {
                            height: height,
                        },
                        wallets: ["google", "apple"],
                    }
                );
                if (paydockWalletsSettings.isSandbox) {
                    button.setEnv('sandbox');
                }

                button.onPaymentSuccessful((data) => {
                    $('#paymentSourceWalletsToken').val(JSON.stringify(data))
                    $('#paymentCompleted').show();
                    $('#paydockWalletsGoogleApplePay').hide();
                    $('#paydockWalletsAfterPay').hide();
                    $('#paydockWalletsPaypal').hide();
                    orderButton.show();
                    orderButton.click();
                });

                button.onPaymentError((data) => console.log("The payment was not successful"));
                button.onPaymentInReview((data) => console.log("The payment is on fraud review"));
                button.setupWalletCallback((data) => console.log('blah blah', data))

                button.load();
            }
        }

        function initPaydockWidgetCard() {
            if ($('#paydockWidgetCard').length === 0) {
                return;
            }
            lastInit = idPaydockWidgetCard;

            const paydockCardSettings = window.wc.wcSettings.getSetting('paydock_data', {});

            if (paydockCardSettings.cardSaveCardOption === 'WITHOUT_GATEWAY' && !['IN_BUILD', 'STANDALONE'].includes(paydockCardSettings.card3DS)) {
                widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockCardSettings.publicKey, 'not_configured');
            } else {
                widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockCardSettings.publicKey, paydockCardSettings.gatewayId);
            }

            window.widget = widget;
            widget.setStyles(paydockCardSettings.styles);

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

        setInterval(() => {
            var $paymentMethods = $('input[name=radio-control-wc-payment-method-options]:checked');
            let $orderButton = $('.wc-block-components-checkout-place-order-button');

            for (var i = 0; i < $paymentMethods.length; i++) {
                if (
                    ($paymentMethods[i].type === 'radio')
                    && $paymentMethods[i].checked
                    && ($paymentMethods[i].id !== lastInit)) {
                    switch ($paymentMethods[i].id) {
                        case idPaydockWidgetCard:
                            initPaydockWidgetCard();
                            $orderButton.show();
                            break;
                        case idPaydockWidgetBankAccount:
                            initPaydockWidgetBankAccount();
                            $orderButton.show();
                            break;
                        case idPaydockWidgetWallets:
                            initPaydockWidgetWallets($orderButton);
                            $orderButton.hide();
                            $('#paymentCompleted').hide();
                            break;
                        case idPaydockWidgetApm:
                            initPaydockWidgetApm();
                            $orderButton.show();
                            break;
                        default:
                            lastInit = 'undefined';
                            $orderButton.show();
                    }
                }
            }
        }, 100)
    });
}, 1000)
