window.paydockWalletsButtons = {};
(function () {
    const afterpayCountries = ['au','nz','us','ca','uk','gb','fr','it','es','de'];
    const zippayCountries = ['au','nz','us','ca'];
    let lastInit = '';

    const powerBoardValidation = {
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

    window.powerBoardValidation = powerBoardValidation;

    jQuery(function ($) {
        const idPowerBoardWidgetCard = 'radio-control-wc-payment-method-options-power_board_gateway';
        const idPowerBoardWidgetBankAccount = 'radio-control-wc-payment-method-options-power_board_bank_account_gateway';
        const idPowerBoardWidgetWallets = 'radio-control-wc-payment-method-options-power_board_wallets_gateway';
        const idPowerBoardWidgetApm = 'radio-control-wc-payment-method-options-power_board_apms_gateway';

        const $radioWidgetCard = $('#' + idPowerBoardWidgetCard);
        const $radioWidgetBankAccount = $('#' + idPowerBoardWidgetBankAccount);
        const $radioWidgetApm = $('#' + idPowerBoardWidgetApm);
        const $radioWidgetWallets = $('#' + idPowerBoardWidgetWallets);

        const searchParams = new URLSearchParams(window.location.search);
        const powerBoardWalletsSettings = window.wc.wcSettings.getSetting('power_board_wallets_block_data', {});

        function initPowerBoardWidgetApm(checkoutButton) {
            lastInit = idPowerBoardWidgetApm;

            if ($('#powerBoardWidgetApm').length === 0) {
                return;
            }

            if (!powerBoardValidation.wcFormValidation()) {
                return;
            } else {
                $('#powerBoardWidgetApm').parent().find('.power_board-validation-error').css('display', 'none')
                $('#powerBoardWidgetApm').css('display', 'block')
            }

            checkoutButton.hide();

            const powerBoardApmSettings = window.wc.wcSettings.getSetting('power_board_apms_data', {});
            powerBoardApmSettings.email = $('#email').val()

            const zippayButton = document.querySelector('#zippay')
            if (powerBoardApmSettings.zippayEnable || zippayButton === null) {
                zippayButton.style = ''
                const zippay = new cba.ZipmoneyCheckoutButton('#zippay', powerBoardApmSettings.publicKey, powerBoardApmSettings.zippayGatewayId)
                zippay.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token')
                zippay.setMeta({
                    charge: {
                        amount: powerBoardApmSettings.amount,
                        currency: powerBoardApmSettings.currency,
                    }
                });

                zippay.on('finish', function () {
                    powerBoardApmSettings.gatewayType = 'zippay'
                    if (powerBoardApmSettings.zippayDirectCharge) {
                        powerBoardApmSettings.directCharge = true
                    }
                    if (powerBoardApmSettings.zippayFraud) {
                        powerBoardApmSettings.fraud = true
                        powerBoardApmSettings.fraudServiceId = powerBoardApmSettings.zippayFraudServiceId
                    }
                    powerBoardApmSettings.gatewayId = powerBoardApmSettings.zippayGatewayId
                    document.getElementById('powerBoardWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                    if (checkoutButton !== null) {
                        checkoutButton.click()
                    }
                });
            }

            const afterpayButton = document.querySelector('#afterpay')

            if (powerBoardApmSettings.afterpayEnable || afterpayButton === null) {
                afterpayButton.style = ''
                const afterpay = new cba.AfterpayCheckoutButton('#afterpay', powerBoardApmSettings.publicKey, powerBoardApmSettings.afterpayGatewayId)
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
                    amount: powerBoardApmSettings.amount,
                    currency: powerBoardApmSettings.currency,
                    email: powerBoardApmSettings.email,
                    first_name: firstName,
                    last_name: lastName,
                });

                afterpay.on('finish', function () {
                    powerBoardApmSettings.gatewayType = 'afterpay'
                    if (powerBoardApmSettings.afterpayDirectCharge) {
                        powerBoardApmSettings.directCharge = true
                    }
                    if (powerBoardApmSettings.afterpayFraud) {
                        powerBoardApmSettings.fraud = true
                        powerBoardApmSettings.fraudServiceId = powerBoardApmSettings.afterpayFraudServiceId
                    }
                    powerBoardApmSettings.gatewayId = powerBoardApmSettings.afterpayGatewayId
                    document.getElementById('powerBoardWidgetApm').innerHTML = '<div style="color:#008731">Payment data collected</div>';

                    if (checkoutButton !== null) {
                        checkoutButton.click()
                    }
                });
            }
        }

        function initPowerBoardWidgetBankAccount() {
            lastInit = idPowerBoardWidgetBankAccount;

            const htmlWidget = document.getElementById('powerBoardWidgetBankAccount')
            if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
                powerBoardValidation.passWidgetToWrapper('powerBoardWidgetBankAccount')
                return;
            }

            const powerBoardBankAccountSettings = window.wc.wcSettings.getSetting('power_board_bank_account_block_data', {});

            const gateway = powerBoardBankAccountSettings.bankAccountSaveAccount
                ? powerBoardBankAccountSettings.gatewayId
                : 'not_configured';

            var bankAccount = new cba.Configuration(gateway, 'bank_account');
            bankAccount.setFormFields(['account_routing']);

            powerBoardValidation.createWidgetDiv('powerBoardWidgetBankAccount');
            var widget = new cba.HtmlMultiWidget(
                '#powerBoardWidgetBankAccount',
                powerBoardBankAccountSettings.publicKey,
                [bankAccount]
            );

            window.widget2BankAccount = widget;
            widget.setStyles(powerBoardBankAccountSettings.styles);

            if (typeof powerBoardBankAccountSettings.styles.custom_elements !== "undefined") {
                $.each(powerBoardBankAccountSettings.styles.custom_elements, function (element, styles) {
                    widget.setElementStyle(element, styles);
                });
            }

            widget.onFinishInsert('input[name="payment_source_bank_account_token"]', 'payment_source');
            widget.hideElements(['submit_button']);
            widget.interceptSubmitForm('#widget');
            widget.load();

            widget.on(window.cba.EVENT.AFTER_LOAD, () => {
                if ($('#powerBoardWidgetBankAccount_wrapper').length > 0) {
                    powerBoardValidation.passWidgetToWrapper('powerBoardWidgetBankAccount')
                }
            })
        }

        function initWalletsButtonsWidgets(orderButton) {
            for (let key in window.powerBoardWallets) {
                let id = '#';
                let height = 45;

                switch (key) {
                    case 'apple_pay':
                    case 'google_pay':
                        id += 'powerBoardWalletsGoogleApplePay'
                        break;
                    case 'afterpay':
                        id += 'powerBoardWalletsAfterPay'
                        height = 35;
                        break;
                    case 'pay_pal_smart_button':
                        id += 'powerBoardWalletsPaypal'
                        height = 55;
                        break;
                }
                const config =
                    {
                        country: window.powerBoardWallets[key].county,
                        style: {
                            height: height,
                        }
                    };

                if ('apple_pay' === key) {
                    config['wallets'] = ['apple'];
                    config['amount_label'] = "Total";
                }

                if ('pay_pal_smart_button' === key) {
                    config['pay_later'] = window.powerBoardWallets[key].pay_later;
                }

                $(id).children().remove()

                const button = new cba.WalletButtons(
                    id,
                    window.powerBoardWallets[key].resource.data.token,
                    config
                );
                if (powerBoardWalletsSettings.isSandbox) {
                    button.setEnv('sandbox');
                }

                button.onPaymentSuccessful((data) => {
                    $('#paymentSourceWalletsToken').val(JSON.stringify(data))
                    $('#paymentCompleted').show();
                    $('#powerBoardWalletsGoogleApplePay, #powerBoardWalletsAfterPay, #powerBoardWalletsPaypal').hide();
                    orderButton.show();
                    orderButton.click();
                });

                button.onPaymentError((data) => {
                    orderButton.click();
                    initPaydockWidgetWallets(orderButton);
                });
                button.onPaymentInReview((data) =>{
                    $('#paymentSourceWalletsToken').val(JSON.stringify(data))
                    $('#paymentCompleted').show();
                    $('#paydockWalletsGoogleApplePay, #paydockWalletsAfterPay, #paydockWalletsPaypal').hide();

                    orderButton.show();
                    orderButton.click();
                });

                button.load();

                window.paydockWalletsButtons[key] = button;
            }
        }

        function initPowerBoardWidgetWallets(orderButton) {
            lastInit = idPowerBoardWidgetWallets;

            if (!powerBoardValidation.wcFormValidation()) {
                return;
            } else {
                $('#powerBoardWidgetWallets').parent().find('.powerBoard-validation-error').css('display', 'none')
                $('#powerBoardWidgetWallets')
            }

            if ($('#powerBoardWalletsApplePay, #powerBoardWalletsGooglePay, #powerBoardWalletsAfterPay, #powerBoardWalletsPaypal').length === 0) {
                return;
            }
            let billingData = {};
            const {CHECKOUT_STORE_KEY, CART_STORE_KEY} = window.wc.wcBlocksData;

            billingData['order_id'] = window.wp.data.select(CHECKOUT_STORE_KEY).getOrderId();
            billingData['total'] = window.wp.data.select(CART_STORE_KEY).getCartTotals();
            billingData['address'] = window.wp.data.select(CART_STORE_KEY).getCustomerData().billingAddress;

            window.axios.post('/wp-json/power_board/v1/wallets/charge', billingData).then((response) => {
                window.powerBoardWallets = response.data;
                initWalletsButtonsWidgets(orderButton)
            })
        }

        function initPowerBoardWidgetCard() {
            lastInit = idPowerBoardWidgetCard;

            const htmlWidget = document.getElementById('powerBoardWidgetCard')
            if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
                powerBoardValidation.passWidgetToWrapper('powerBoardWidgetCard')
                return;
            }

            const powerBoardCardSettings = window.wc.wcSettings.getSetting('power_board_block_data', {});

            let gatewayId = powerBoardCardSettings.gatewayId;
            switch (true) {
                case powerBoardCardSettings.card3DS === 'STANDALONE':
                case powerBoardCardSettings.cardSaveCardOption === 'WITHOUT_GATEWAY':
                case powerBoardCardSettings.card3DS !== 'IN_BUILD':
                    gatewayId = 'not_configured'
                    break;
            }

            powerBoardValidation.createWidgetDiv('powerBoardWidgetCard');
            widget2 = new cba.HtmlWidget('#powerBoardWidgetCard', powerBoardCardSettings.publicKey, gatewayId);

            window.widget2 = widget;
            widget.setStyles(powerBoardCardSettings.styles);

            if (typeof powerBoardCardSettings.styles?.custom_elements !== "undefined") {
                $.each(powerBoardCardSettings.styles.custom_elements, function (element, styles) {
                    widget.setElementStyle(element, styles);
                });
            }

            if (powerBoardCardSettings.cardSupportedCardTypes && powerBoardCardSettings.cardSupportedCardTypes !== '') {
                supportedCard = powerBoardCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')
                widget.setSupportedCardIcons(supportedCard);
            }

            widget.setFormFields(['email', 'phone']);
            widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
            widget.interceptSubmitForm('#widget');
            widget.load();

            widget.on(window.cba.EVENT.AFTER_LOAD, () => {
                widget.hideElements(['submit_button', 'email', 'phone']);
                if ($('#powerBoardWidgetCard_wrapper').length > 0) {
                    powerBoardValidation.passWidgetToWrapper('powerBoardWidgetCard')
                }
            })
        }

        let initInterval = setInterval(() => {
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            if ($orderButton.length === 0) {
                return;
            }

            initPowerBoardWidgetCard();
            initPowerBoardWidgetBankAccount();

            switch (true) {
                case $radioWidgetCard.attr('checked') === 'checked':
                    break;
                case $radioWidgetBankAccount.attr('checked') === 'checked':
                    break;
                case $radioWidgetApm.attr('checked') === 'checked':
                    initPowerBoardWidgetApm();
                    break;
            }

            $radioWidgetCard.on('change', function () {
                initPowerBoardWidgetCard();
            });

            $radioWidgetBankAccount.on('change', function () {
                initPowerBoardWidgetBankAccount();
            });

            $radioWidgetApm.on('change', function () {
                initPowerBoardWidgetApm($orderButton);
            });

            $radioWidgetWallets.on('change', function () {
                initPowerBoardWidgetWallets($orderButton);
            });

            let wasClick = false;
            setInterval(() => {
                if (
                    powerBoardWalletsSettings.hasOwnProperty('afterpayChargeId')
                    && (powerBoardWalletsSettings.afterpayChargeId.length > 0)
                    && searchParams.has('afterpay_success')
                    && !wasClick
                ) {
                    wasClick = true
                    $('#' + idPowerBoardWidgetWallets).parent().click();
                    $radioWidgetWallets
                    $('#paymentCompleted').show();
                    $('#paymentSourceWalletsToken').val(JSON.stringify({
                        data:{
                            id:powerBoardWalletsSettings.afterpayChargeId,
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
                        case idPowerBoardWidgetCard:
                            initPowerBoardWidgetCard();
                            $orderButton.show();
                            break;
                        case idPowerBoardWidgetBankAccount:
                            initPowerBoardWidgetBankAccount();
                            $orderButton.show();
                            break;
                        case idPowerBoardWidgetWallets:
                            initPowerBoardWidgetWallets($orderButton);
                            $orderButton.hide();
                            $('#paymentCompleted').hide();
                            break;
                        case idPowerBoardWidgetApm:
                            initPowerBoardWidgetApm($orderButton);
                            break;
                        default:
                            lastInit = 'undefined';
                            $orderButton.show();
                    }
                })
                try{
                    let {CHECKOUT_STORE_KEY, CART_STORE_KEY} = window.wc.wcBlocksData;
                    let powerBoardApmSettings = window.wc.wcSettings.getSetting('power_board_apms_data', {});
                    choisenCountry = window.wp.data.select(CART_STORE_KEY).getCustomerData().billingAddress.country;
                    $afterpayNotice = $('.power_board-country-available-afterpay');
                    $zippayNotice = $('.power_board-country-available-zippay');

                    if(
                        (
                            powerBoardWalletsSettings.wallets.hasOwnProperty('afterpay')
                            || powerBoardApmSettings.afterpayEnable
                        )
                        && !afterpayCountries.includes(choisenCountry.toLowerCase())
                    ){
                        $afterpayNotice.show();
                    }else{
                        $afterpayNotice.hide();
                    }

                    if(powerBoardApmSettings.zippayEnable
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
