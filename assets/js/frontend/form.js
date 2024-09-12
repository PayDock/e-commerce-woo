setTimeout(() => jQuery(function ($) {
    $(document).ready(function() {
        const CONFIG = {
            phoneInputIds: {
                shipping: '#shipping-phone',
                billing: '#billing-phone',
            },
            baseCheckboxIdName: 'radio-control-wc-payment-method-options',
            errorMessageClassName: 'wc-block-components-validation-error',
            paymentOptionsNames: [
                'power_board_gateway',
                'power_board_google-pay_wallets_gateway',
                'power_board_afterpay_wallets_gateway',
                'power_board_pay-pal_wallets_gateway',
                'power_board_afterpay_a_p_m_s_gateway',
                'power_board_zip_a_p_m_s_gateway',
            ],
            phonePattern: /^\+[1-9]{1}[0-9]{3,14}$/,
            errorMessageHtml: `<div class="wc-block-components-validation-error" role="alert"><p>Please enter your phone number in international format, starting with "+"</p></div>`,
        };

        const $submitButton = $('button.wc-block-components-checkout-place-order-button');
        const $shippingWrapper = $('#shipping-fields .wc-block-components-address-address-wrapper');

        const getPhoneInputs = () =>
            Object.entries(CONFIG.phoneInputIds)
                .reduce((acc, [key, selector]) => {
                    const $input = $(selector);
                    if ($input.length) acc[key] = $input;
                    return acc;
                }, {});

        const getPaymentOptionsComponents = () =>
            CONFIG.paymentOptionsNames
                .map(name => $(`#${CONFIG.baseCheckboxIdName}-${name}`).parents().eq(1))
                .filter($component => $component.length);

        const validatePhone = ($input) => {
            const phone = $input.val();
            $input.next(`.${CONFIG.errorMessageClassName}`).remove();
            if (phone && !CONFIG.phonePattern.test(phone)) {
                $input.after(CONFIG.errorMessageHtml);

                return false;
            }

            return true;
        };

        const updateVisibility = (phoneInputs) => {
            const validationResults = Object.entries(phoneInputs).reduce((acc, [key, $input]) => {
                acc[key] = validatePhone($input);
                return acc;
            }, {});

            const allValid = Object.values(validationResults).every(Boolean);
            const shippingValid = validationResults.shipping;

            if (!shippingValid) $shippingWrapper.addClass('is-editing');

            $submitButton.css('visibility', allValid ? 'visible' : 'hidden');

            getPaymentOptionsComponents().forEach($component =>
                $component.css({
                    opacity: allValid ? 1 : 0.5,
                    pointerEvents: allValid ? 'auto' : 'none',
                })
            );
        };

        const initPhoneNumbersValidation = () => {
            const phoneInputs = getPhoneInputs();
            if (!Object.keys(phoneInputs).length) return;

            Object.values(phoneInputs).forEach($input =>
                $input.on('blur input', () => updateVisibility(phoneInputs))
            );

            updateVisibility(phoneInputs);
        };

        initPhoneNumbersValidation();

        $('.wc-block-checkout__use-address-for-billing input[type="checkbox"]').on("change", initPhoneNumbersValidation);
    });


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
        powerboardCCFormValidation() {
            const { tokens, selectedToken } = window.wc.wcSettings.getSetting('power_board_data', {});
            return (Array.isArray(tokens) && tokens.length > 0 && selectedToken !== "") || window.widgetPowerBoard.isValidForm();
        },
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
            window.widgetPowerBoard.hideElements(['submit_button']);
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
        window.widgetReloaded = lastInit === idPowerBoardWidgetCard;
        lastInit = idPowerBoardWidgetCard;

        const htmlWidget = document.getElementById('powerBoardWidgetCard')
        if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
            powerBoardValidation.passWidgetToWrapper('powerBoardWidgetCard');
            reloadWidget();
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

        widget = new cba.HtmlWidget('#powerBoardWidgetCard', powerBoardCardSettings.publicKey, gatewayId, "card", "card_payment_source_with_cvv");
        widget.setFormPlaceholders({
            card_name: 'Card holders name *',
            card_number: 'Credit card number *',
            expire_month: 'MM/YY *',
            card_ccv: 'CCV *',
        })

        window.widgetPowerBoard = widget;
        if (powerBoardCardSettings.hasOwnProperty('styles')) {
            widget.setStyles(powerBoardCardSettings.styles);
        }

        if (powerBoardCardSettings.hasOwnProperty('styles') && typeof powerBoardCardSettings.styles.custom_elements !== "undefined") {
            $.each(powerBoardCardSettings.styles.custom_elements, function (element, styles) {
                widget.setElementStyle(element, styles);
            });
        }

        /*
        if (powerBoardCardSettings.hasOwnProperty('styles') && powerBoardCardSettings.cardSupportedCardTypes !== '') {
            supportedCard = powerBoardCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')

            widget.setSupportedCardIcons(supportedCard, true);
        }
        */

        if(powerBoardCardSettings.cardSupportedCardTypes !== '') {
            var supportedCardTypes = [];

            var supportedCards = powerBoardCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')
            $.each(supportedCards, function(index, value) {
                supportedCardTypes.push(value);
            });

            widget.setSupportedCardIcons(supportedCardTypes, true);
        }

        widget.setFormFields(["card_name*","card_number*", "card_ccv*"]);
        widget.setEnv(powerBoardCardSettings.isSandbox ? 'preproduction_cba' : 'production_cba');
        widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
        widget.interceptSubmitForm('#widget');
        widget.hideElements(['submit_button']);
        widget.load();

        let performAfterLoadActions = true
        widget.on(window.cba.EVENT.AFTER_LOAD, () => {
            if (performAfterLoadActions && $('#powerBoardWidgetCard_wrapper').length > 0) {
                powerBoardValidation.passWidgetToWrapper('powerBoardWidgetCard');
                performAfterLoadActions = false;
            }
        })

        widget.on(window.cba.EVENT.FINISH, () => {
            const widgetErrorInterval = setInterval(() => {
                const errorBanner = document.querySelectorAll('.wc-block-components-notice-banner.is-error')[0];
                const bannerContent = errorBanner?.querySelectorAll('.wc-block-components-notice-banner__content')[0];
                if (bannerContent?.innerText.indexOf('widget_error') > -1) {
                    reloadWidget();
                    bannerContent.innerText = bannerContent?.innerText.replace('widget_error', '')
                    clearInterval(widgetErrorInterval);
                }
            }, 100)
        })
    }

    function reloadWidget() {
        window.widgetPowerBoard.reload();
        const paymentSourceToken = document.querySelector('[name="payment_source_token"]');
        paymentSourceToken.value = null;
        window.widgetReloaded = true
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
