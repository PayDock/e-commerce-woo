(function () {
    const afterpayCountries = ['au','nz','us','ca','uk','gb','fr','it','es','de'];
    const zippayCountries = ['au','nz','us','ca'];
    let lastInit = '';

    const paydockValidation = {
        shouldValidateWcForm: true,
        wcFormSetBlurEventListener() {
            const checkoutFormElements = document.querySelectorAll('.wc-block-checkout__form input, .wc-block-checkout__form select');
            if (checkoutFormElements.length === 0) {
                return;
            }

            const self = this
            checkoutFormElements.forEach(element => {
                switch (element.type) {
                    case 'email':
                    case 'text':
                    case 'tel':
                        element.addEventListener('blur', () => {
                            if (!self.shouldValidateWcForm) {
                                return;
                            }

                            if (self.wcFormValidation()) {
                                self.shouldValidateWcForm = false
                                lastInit = ''
                            }
                        })
                        break;
                }
            })
        },
        lastWcFormValidation: false,
        wcFormValidation() {
            const checkoutFormElements = document.querySelectorAll('.wc-block-checkout__form input, .wc-block-checkout__form select');
            if (checkoutFormElements.length === 0) {
                return null;
            }

            const BreakException = {};

            let result = true

            try {
                checkoutFormElements.forEach(element => {
                    if (!element.required) {
                        return
                    }

                    result = element.validity.valid;
                    if (result === false) {
                        throw BreakException
                    }
                })
            } catch (e) {
                if (e !== BreakException) throw e
            }

            this.lastWcFormValidation = result
            this.shouldValidateWcForm = !result

            return result;
        },
        createWidgetDiv(id) {
            if (document.getElementById(id) !== null) {
                return null;
            }

            const div = document.createElement('div')
            div.setAttribute('id', id)
            div.setAttribute('style', 'display:none')

            document.body.append(div)

            return div;
        },
        passWidgetToWrapper(id) {
            const wrapper = document.getElementById(id + '_wrapper')
            const htmlWidget = document.getElementById(id)
            if (wrapper.querySelector('#' + id) === null && htmlWidget !== null) {
                const clonnedHtmlWidget = htmlWidget.cloneNode(true)
                clonnedHtmlWidget.setAttribute('style', '');
                document.getElementById(id + '_wrapper').append(clonnedHtmlWidget);
            }
        },
    }

    window.paydockValidation = paydockValidation;

    jQuery(function ($) {
        const idPaydockWidgetCard = 'radio-control-wc-payment-method-options-paydock_gateway';
        const idPaydockWidgetBankAccount = 'radio-control-wc-payment-method-options-paydock_bank_account_gateway';
        const idPaydockWidgetWallets = 'radio-control-wc-payment-method-options-paydock_wallets_gateway';
        const idPaydockWidgetApm = 'radio-control-wc-payment-method-options-paydock_apms_gateway';

        const $radioWidgetCard = $('#' + idPaydockWidgetCard);
        const $radioWidgetBankAccount = $('#' + idPaydockWidgetBankAccount);
        const $radioWidgetApm = $('#' + idPaydockWidgetApm);
        const $radioWidgetWallets = $('#' + idPaydockWidgetWallets);

        const searchParams = new URLSearchParams(window.location.search);
        const paydockWalletsSettings = window.wc.wcSettings.getSetting('paydock_wallets_block_data', {});

        function initPaydockWidgetApm(checkoutButton) {
            lastInit = idPaydockWidgetApm;

            if ($('#paydockWidgetApm').length === 0) {
                return;
            }

            if (!paydockValidation.wcFormValidation()) {
                return;
            } else {
                $('#paydockWidgetApm').parent().find('.paydock-validation-error').css('display', 'none')
                $('#paydockWidgetApm').css('display', 'block')
            }

            checkoutButton.hide();

            const paydockApmSettings = window.wc.wcSettings.getSetting('paydock_apms_data', {});
            paydockApmSettings.email = $('#email').val()

            const zippayButton = document.querySelector('#zippay')
            if (paydockApmSettings.zippayEnable || zippayButton === null) {
                zippayButton.style = ''
                const zippay = new paydock.ZipmoneyCheckoutButton('#zippay', paydockApmSettings.publicKey, paydockApmSettings.zippayGatewayId)
                zippay.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token')
                zippay.setMeta({
                    charge: {
                        amount: paydockApmSettings.amount,
                        currency: paydockApmSettings.currency,
                    }
                });

                zippay.on('finish', function () {
                    paydockApmSettings.gatewayType = 'zippay'
                    if (paydockApmSettings.zippayDirectCharge) {
                        paydockApmSettings.directCharge = true
                    }
                    if (paydockApmSettings.zippayFraud) {
                        paydockApmSettings.fraud = true
                        paydockApmSettings.fraudServiceId = paydockApmSettings.zippayFraudServiceId
                    }
                    paydockApmSettings.gatewayId = paydockApmSettings.zippayGatewayId
                    document.getElementById('paydockWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                    if (checkoutButton !== null) {
                        checkoutButton.click()
                    }
                });
            }

            const afterpayButton = document.querySelector('#afterpay')
            if (paydockApmSettings.afterpayEnable || afterpayButton === null) {
                afterpayButton.style = ''
                const afterpay = new paydock.AfterpayCheckoutButton('#afterpay', paydockApmSettings.publicKey, paydockApmSettings.afterpayGatewayId)
                afterpay.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token')
                afterpay.showEnhancedTrackingProtectionPopup(true)
                
                let firstName = '';
                let lastName = '';
                if ($('#shipping-first_name').length > 0) {
                    firstName = $('#shipping-first_name').val()
                }
                if ($('#billing-first_name').length > 0) {
                    firstName = $('#billing-first_name').val()
                }
                if ($('#shipping-last_name').length > 0) {
                    lastName = $('#shipping-last_name').val()
                }
                if ($('#billing-last_name').length > 0) {
                    lastName = $('#billing-last_name').val()
                }

                afterpay.setMeta({
                    amount: paydockApmSettings.amount,
                    currency: paydockApmSettings.currency,
                    email: paydockApmSettings.email,
                    first_name: firstName,
                    last_name: lastName,
                });

                afterpay.on('finish', function () {
                    paydockApmSettings.gatewayType = 'afterpay'
                    if (paydockApmSettings.afterpayDirectCharge) {
                        paydockApmSettings.directCharge = true
                    }
                    if (paydockApmSettings.afterpayFraud) {
                        paydockApmSettings.fraud = true
                        paydockApmSettings.fraudServiceId = paydockApmSettings.afterpayFraudServiceId
                    }
                    paydockApmSettings.gatewayId = paydockApmSettings.afterpayGatewayId
                    document.getElementById('paydockWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                    if (checkoutButton !== null) {
                        checkoutButton.click()
                    }
                });
            }
        }

        function initPaydockWidgetBankAccount() {
            lastInit = idPaydockWidgetBankAccount;

            const htmlWidget = document.getElementById('paydockWidgetBankAccount')
            if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
                paydockValidation.passWidgetToWrapper('paydockWidgetBankAccount')
                return;
            }

            const paydockBankAccountSettings = window.wc.wcSettings.getSetting('paydock_bank_account_block_data', {});

            const gateway = paydockBankAccountSettings.bankAccountSaveAccount
                ? paydockBankAccountSettings.gatewayId
                : 'not_configured';

            var bankAccount = new paydock.Configuration(gateway, 'bank_account');
            bankAccount.setFormFields(['account_routing']);

            paydockValidation.createWidgetDiv('paydockWidgetBankAccount');
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

            widget.on(window.paydock.EVENT.AFTER_LOAD, () => {
                if ($('#paydockWidgetBankAccount_wrapper').length > 0) {
                    paydockValidation.passWidgetToWrapper('paydockWidgetBankAccount')
                }
            })
        }

        function initWalletsButtonsWidgets(orderButton) {
            for (let key in window.paydockWallets) {
                let id = '#';
                let height = 45;

                switch (key) {
                    case 'apple_pay':
                    case 'google_pay':
                        id += 'paydockWalletsGoogleApplePay'
                        break;
                    case 'afterpay':
                        id += 'paydockWalletsAfterPay'
                        height = 35;
                        break;
                    case 'pay_pal_smart_button':
                        id += 'paydockWalletsPaypal'
                        height = 55;
                        break;
                }
                let config =
                    {
                        country: window.paydockWallets[key].county,
                        style: {
                            height: height,
                        }
                    };

                if ('apple_pay' === key) {
                    config['wallets'] = ['apple'];
                    config['amount_label'] = "Total";
                }

                if ('pay_pal_smart_button' === key) {
                    config['pay_later'] = window.paydockWallets[key].pay_later;
                }

                const button = new paydock.WalletButtons(
                    id,
                    window.paydockWallets[key].resource.data.token,
                    config
                );
                if (paydockWalletsSettings.isSandbox) {
                    button.setEnv('sandbox');
                }

                button.onPaymentSuccessful((data) => {
                    $('#paymentSourceWalletsToken').val(JSON.stringify(data))
                    $('#paymentCompleted').show();
                    $('#paydockWalletsGoogleApplePay, #paydockWalletsAfterPay, #paydockWalletsPaypal').hide();
                    orderButton.show();
                    orderButton.click();
                });

                button.onPaymentError((data) => orderButton.click());
                button.onPaymentInReview((data) => orderButton.click());

                button.load();
            }
        }

        function initPaydockWidgetWallets(orderButton) {
            lastInit = idPaydockWidgetWallets;

            if (!paydockValidation.wcFormValidation()) {
                return;
            } else {
                $('#paydockWidgetWallets').parent().find('.paydock-validation-error').css('display', 'none')
                $('#paydockWidgetWallets')
            }

            if (window.paydockWallets) {
                initWalletsButtonsWidgets();
                return;
            }

            if ($('#paydockWalletsApplePay, #paydockWalletsGooglePay, #paydockWalletsAfterPay, #paydockWalletsPaypal').length === 0) {
                return;
            }
            let billingData = {};
            const {CHECKOUT_STORE_KEY, CART_STORE_KEY} = window.wc.wcBlocksData;

            billingData['order_id'] = window.wp.data.select(CHECKOUT_STORE_KEY).getOrderId();
            billingData['total'] = window.wp.data.select(CART_STORE_KEY).getCartTotals();
            billingData['address'] = window.wp.data.select(CART_STORE_KEY).getCustomerData().billingAddress;

            window.axios.post('/wp-json/paydock/v1/wallets/charge', billingData).then((response) => {
                window.paydockWallets = response.data;
                initWalletsButtonsWidgets(orderButton)
            })
        }

        function initPaydockWidgetCard() {
            lastInit = idPaydockWidgetCard;

            const htmlWidget = document.getElementById('paydockWidgetCard')
            if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
                paydockValidation.passWidgetToWrapper('paydockWidgetCard')
                return;
            }

            const paydockCardSettings = window.wc.wcSettings.getSetting('paydock_data', {});

            let gatewayId = paydockCardSettings.gatewayId;
            switch (true) {
                case paydockCardSettings.card3DS === 'STANDALONE':
                case paydockCardSettings.cardSaveCardOption === 'WITHOUT_GATEWAY':
                case paydockCardSettings.card3DS !== 'IN_BUILD':
                    gatewayId = 'not_configured'
                    break;
            }

            paydockValidation.createWidgetDiv('paydockWidgetCard');
            widget = new paydock.HtmlWidget('#paydockWidgetCard', paydockCardSettings.publicKey, gatewayId);

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

            widget.setFormFields(['email', 'phone']);
            widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
            widget.interceptSubmitForm('#widget');
            widget.load();

            widget.on(window.paydock.EVENT.AFTER_LOAD, () => {
                widget.hideElements(['submit_button', 'email', 'phone']);
                if ($('#paydockWidgetCard_wrapper').length > 0) {
                    paydockValidation.passWidgetToWrapper('paydockWidgetCard')
                }
            })
        }

        let initInterval = setInterval(() => {
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            if ($orderButton.length === 0) {
                return;
            }

            initPaydockWidgetCard();
            initPaydockWidgetBankAccount();

            switch (true) {
                case $radioWidgetCard.attr('checked') === 'checked':
                    break;
                case $radioWidgetBankAccount.attr('checked') === 'checked':
                    break;
                case $radioWidgetApm.attr('checked') === 'checked':
                    initPaydockWidgetApm();
                    break;
            }

            $radioWidgetCard.on('change', function () {
                initPaydockWidgetCard();
            });

            $radioWidgetBankAccount.on('change', function () {
                initPaydockWidgetBankAccount();
            });

            $radioWidgetApm.on('change', function () {
                initPaydockWidgetApm($orderButton);
            });

            $radioWidgetWallets.on('change', function () {
                initPaydockWidgetWallets($orderButton);
            });

            let wasClick = false;
            setInterval(() => {
                if (
                    paydockWalletsSettings.hasOwnProperty('afterpayChargeId')
                    && (paydockWalletsSettings.afterpayChargeId.length > 0)
                    && searchParams.has('afterpay_success')
                    && !wasClick
                ) {
                    wasClick = true
                    $('#' + idPaydockWidgetWallets).parent().click();
                    $radioWidgetWallets
                    $('#paymentCompleted').show();
                    $('#paymentSourceWalletsToken').val(JSON.stringify({
                        data:{
                            id:paydockWalletsSettings.afterpayChargeId,
                            status: (searchParams.get('direct_charge') === 'true') ? 'paid' : 'pending'
                        }
                    }));
                    $orderButton.click();
                }

                const $paymentMethods = $('input[name=radio-control-wc-payment-method-options]:checked');

                $paymentMethods.each((i, $paymentMethod) => {
                    if (
                        $paymentMethod.type !== 'radio'
                        || !$paymentMethod.checked
                        || $paymentMethod.id === lastInit) {
                        return;
                    }

                    switch ($paymentMethod.id) {
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
                            initPaydockWidgetApm($orderButton);
                            break;
                        default:
                            lastInit = 'undefined';
                            $orderButton.show();
                    }
                })
                try{
                    let {CHECKOUT_STORE_KEY, CART_STORE_KEY} = window.wc.wcBlocksData;
                    let paydockApmSettings = window.wc.wcSettings.getSetting('paydock_apms_data', {});
                    choisenCountry = window.wp.data.select(CART_STORE_KEY).getCustomerData().billingAddress.country;
                    $afterpayNotice = $('.paydock-country-available-afterpay');
                    $zippayNotice = $('.paydock-country-available-zippay');

                    if(
                        (
                            paydockWalletsSettings.wallets.hasOwnProperty('afterpay')
                            || paydockApmSettings.afterpayEnable
                        )
                        && !afterpayCountries.includes(choisenCountry.toLowerCase())
                    ){
                        $afterpayNotice.show();
                    }else{
                        $afterpayNotice.hide();
                    }

                    if(paydockApmSettings.zippayEnable
                        && !zippayCountries.includes(choisenCountry.toLowerCase())){
                        $zippayNotice.show();
                    }else{
                        $zippayNotice.hide();
                    }
                }catch (e){
                    console.log(e)
                }
            }, 100)

            clearInterval(initInterval)
        }, 100);
    })
})()
