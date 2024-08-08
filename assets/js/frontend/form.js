setTimeout(() => jQuery(function ($) {
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
            window.widgetPowerBoard.hideElements(['submit_button', 'email', 'phone']);
        },
    }

    window.powerBoardValidation = powerBoardValidation;

    const idPowerBoardWidgetCard = 'radio-control-wc-payment-method-options-power_board_gateway';
    const idPowerBoardWidgetBankAccount = 'radio-control-wc-payment-method-options-power_board_bank_account_gateway';

    const searchParams = new URLSearchParams(window.location.search);
    function initPowerBoardWidgetBankAccount() {
        lastInit = idPowerBoardWidgetBankAccount;
        const powerBoardBankAccountSettings = window.wc.wcSettings.getSetting('power_board_bank_account_block_data', {});

        if (!powerBoardBankAccountSettings.isActive) {
            return;
        }

        const htmlWidget = document.getElementById('powerBoardWidgetBankAccount')

        if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
            powerBoardValidation.passWidgetToWrapper('powerBoardWidgetBankAccount')
            return;
        }

        const gateway = 'not_configured';

        const bankAccount = new cba.Configuration(gateway, 'bank_account');
        bankAccount.setFormFields(['account_routing']);

        powerBoardValidation.createWidgetDiv('powerBoardWidgetBankAccount');
        const widget = new cba.HtmlWidget('#powerBoardWidgetBankAccount', powerBoardBankAccountSettings.publicKey, 'not_configured', 'bank_account', 'payment_source');
        widget.setFormFields(['account_routing']);

        window.widgetPowerBoardBankAccount = widget;
        if (powerBoardBankAccountSettings.hasOwnProperty('styles'))
            widget.setStyles(powerBoardBankAccountSettings.styles);

        if (
            powerBoardBankAccountSettings.hasOwnProperty('styles')
            && typeof powerBoardBankAccountSettings.styles.custom_elements !== "undefined"
        ) {
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

    function initPowerBoardWidgetCard() {
        lastInit = idPowerBoardWidgetCard;

        const htmlWidget = document.getElementById('powerBoardWidgetCard')
        if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
            powerBoardValidation.passWidgetToWrapper('powerBoardWidgetCard')
            return;
        }

        const powerBoardCardSettings = window.wc.wcSettings.getSetting('power_board_data', {});
        powerBoardValidation.createWidgetDiv('powerBoardWidgetCard');

        let isPermanent = powerBoardCardSettings.hasOwnProperty('card3DSFlow')
            && ("SESSION_VAULT" === powerBoardCardSettings.card3DSFlow) && (
                powerBoardCardSettings.hasOwnProperty('card3DS')
                && 'DISABLE' !== powerBoardCardSettings.card3DS
            )

        let gatewayId = isPermanent ? powerBoardCardSettings.gatewayId : 'not_configured';

        widget = new cba.HtmlWidget('#powerBoardWidgetCard', powerBoardCardSettings.publicKey, gatewayId);

        window.widgetPowerBoard = widget;
        if (powerBoardCardSettings.hasOwnProperty('styles')) {
            widget.setStyles(powerBoardCardSettings.styles);
        }

        if (powerBoardCardSettings.hasOwnProperty('styles') && typeof powerBoardCardSettings.styles.custom_elements !== "undefined") {
            $.each(powerBoardCardSettings.styles.custom_elements, function (element, styles) {
                widget.setElementStyle(element, styles);
            });
        }

        if (powerBoardCardSettings.hasOwnProperty('styles') && powerBoardCardSettings.cardSupportedCardTypes !== '') {
            supportedCard = powerBoardCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')
            widget.setSupportedCardIcons(supportedCard);
        }

        widget.setEnv(powerBoardCardSettings.isSandbox ? 'staging_cba' : 'production_cba');
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

    function setPaymentMethodWatcher() {
        $('.wc-block-components-radio-control__input').on('change', (event) => {
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            switch (event.target.value) {
                case 'power_board_gateway':
                    initPowerBoardWidgetCard();
                    $orderButton.show();
                    break;
                case 'power_board_bank_account_gateway':
                    initPowerBoardWidgetBankAccount();
                    $orderButton.show();
                    break;
                case 'power_board_google-pay_wallets_gateway':
                case 'power_board_apple-pay_wallets_gateway':
                case 'power_board_afterpay-pay_wallets_gateway':
                case 'power_board_pay-pal_wallets_gateway':
                case 'power_board_afterpay_a_p_m_s_gateway':
                case 'power_board_zip_a_p_m_s_gateway':
                    $orderButton.hide();
                    break;
                default:
                    $orderButton.show();
            }
        })
    }

    let wasClick = false;
    let wasInit = false;

    setInterval(() => {
        try {
            const powerBoardAfterpayWalletsSettings = window.wc?.wcSettings?.getSetting('power_board_afterpay_wallet_block_data', {});
            const $radioWidgetCard = $('#' + idPowerBoardWidgetCard);
            const $radioWidgetBankAccount = $('#' + idPowerBoardWidgetBankAccount);
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            const $afterpayRadiobatton = $('#radio-control-wc-payment-method-options-power_board_afterpay_wallets_gateway');
            if (
                powerBoardAfterpayWalletsSettings
                && powerBoardAfterpayWalletsSettings.hasOwnProperty('afterpayChargeId')
                && (powerBoardAfterpayWalletsSettings.afterpayChargeId.length > 0)
                && searchParams.has('afterpay_success')
                && !wasClick
                && $orderButton.length
                && $afterpayRadiobatton.length
            ) {
                wasClick = true
                $afterpayRadiobatton.parent().click();
                $('#paymentCompleted').show();
                $('#powerBoardWalletAfterpayButton').hide();
                $orderButton.hide();
                $('#paymentSourceWalletsToken').val(JSON.stringify({
                    data: {
                        id: powerBoardAfterpayWalletsSettings.afterpayChargeId,
                        status: (searchParams.get('direct_charge') === 'true') ? 'paid' : 'pending'
                    }
                }));
                $orderButton.click();
            }

            if ($radioWidgetCard[0] && $radioWidgetCard[0].checked && !wasInit) {
                wasInit = true;
                initPowerBoardWidgetCard();
                setPaymentMethodWatcher();
            } else if ($radioWidgetBankAccount[0] && $radioWidgetBankAccount[0].checked && !wasInit) {
                wasInit = true;
                initPowerBoardWidgetBankAccount()
                setPaymentMethodWatcher();
            }
        } catch (e) {
            console.log(e);
        }

    }, 100)

}), 1000)
