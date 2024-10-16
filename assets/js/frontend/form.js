const pluginPrefix = window.widgetSettings.pluginPrefix;
const pluginWidgetName = window.widgetSettings.pluginWidgetName;

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
                pluginPrefix + '_gateway',
                pluginPrefix + '_google-pay_wallets_gateway',
                pluginPrefix + '_afterpay_wallets_gateway',
                pluginPrefix + '_pay-pal_wallets_gateway',
                pluginPrefix + '_afterpay_a_p_m_s_gateway',
                pluginPrefix + '_zip_a_p_m_s_gateway',
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

    const pluginValidation = {
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
        pluginCCFormValidation() {
            const { tokens, selectedToken } = window.wc.wcSettings.getSetting(pluginPrefix + '_data', {});
            return (Array.isArray(tokens) && tokens.length > 0 && selectedToken !== "") || window.pluginCardWidget.isValidForm();
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
            window.pluginCardWidget.hideElements(['submit_button']);
        },
    }

    window.pluginValidation = pluginValidation;

    const idPluginWidgetCard = 'radio-control-wc-payment-method-options-' + pluginPrefix + '_gateway';
    const idPluginWidgetBankAccount = 'radio-control-wc-payment-method-options-' + pluginPrefix + '_bank_account_gateway';

    const searchParams = new URLSearchParams(window.location.search);
    function initPluginWidgetBankAccount() {
        lastInit = idPluginWidgetBankAccount;
        const pluginBankAccountSettings = window.wc.wcSettings.getSetting(pluginPrefix + '_bank_account_block_data', {});

        if (!pluginBankAccountSettings.isActive) {
            return;
        }

        const htmlWidget = document.getElementById('pluginWidgetBankAccount')

        if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
            pluginValidation.passWidgetToWrapper('pluginWidgetBankAccount')
            return;
        }

        const gateway = 'not_configured';

        const bankAccount = new window[pluginWidgetName].Configuration(gateway, 'bank_account');
        bankAccount.setFormFields(['account_routing']);

        pluginValidation.createWidgetDiv('pluginWidgetBankAccount');
        const widget = new window[pluginWidgetName].HtmlWidget('#pluginWidgetBankAccount', pluginBankAccountSettings.publicKey, 'not_configured', 'bank_account', 'payment_source');
        widget.setFormFields(['account_routing']);

        window.widgetPluginBankAccount = widget;
        if (pluginBankAccountSettings.hasOwnProperty('styles'))
            widget.setStyles(pluginBankAccountSettings.styles);

        if (
            pluginBankAccountSettings.hasOwnProperty('styles')
            && typeof pluginBankAccountSettings.styles.custom_elements !== "undefined"
        ) {
            $.each(pluginBankAccountSettings.styles.custom_elements, function (element, styles) {
                widget.setElementStyle(element, styles);
            });
        }

        widget.onFinishInsert('input[name="payment_source_bank_account_token"]', 'payment_source');
        widget.hideElements(['submit_button']);
        widget.interceptSubmitForm('#widget');
        widget.load();

        widget.on(window[pluginWidgetName].EVENT.AFTER_LOAD, () => {
            if ($('#pluginWidgetBankAccount_wrapper').length > 0) {
                pluginValidation.passWidgetToWrapper('pluginWidgetBankAccount')
            }
        })
    }

    function initPluginWidgetCard() {
        window.widgetReloaded = lastInit === idPluginWidgetCard;
        lastInit = idPluginWidgetCard;

        const htmlWidget = document.getElementById('pluginWidgetCard')
        if (htmlWidget !== null && htmlWidget.innerHTML.trim().length > 0) {
            pluginValidation.passWidgetToWrapper('pluginWidgetCard');
            reloadWidget();
            return;
        }

        const pluginCardSettings = window.wc.wcSettings.getSetting(pluginPrefix + '_data', {});
        pluginValidation.createWidgetDiv('pluginWidgetCard');

        let isPermanent = pluginCardSettings.hasOwnProperty('card3DSFlow')
            && ("SESSION_VAULT" === pluginCardSettings.card3DSFlow) && (
                pluginCardSettings.hasOwnProperty('card3DS')
                && 'DISABLE' !== pluginCardSettings.card3DS
            )

        let gatewayId = isPermanent ? pluginCardSettings.gatewayId : 'not_configured';

        widget = new window[pluginWidgetName].HtmlWidget('#pluginWidgetCard', pluginCardSettings.publicKey, gatewayId, "card", "card_payment_source_with_cvv");
        widget.setFormPlaceholders({
            card_name: 'Card holders name *',
            card_number: 'Credit card number *',
            expire_month: 'MM/YY *',
            card_ccv: 'CCV *',
        })

        window.pluginCardWidget = widget;
        if (pluginCardSettings.hasOwnProperty('styles')) {
            widget.setStyles(pluginCardSettings.styles);
        }

        if (pluginCardSettings.hasOwnProperty('styles') && typeof pluginCardSettings.styles.custom_elements !== "undefined") {
            $.each(pluginCardSettings.styles.custom_elements, function (element, styles) {
                widget.setElementStyle(element, styles);
            });
        }

        /*
        if (pluginCardSettings.hasOwnProperty('styles') && pluginCardSettings.cardSupportedCardTypes !== '') {
            supportedCard = pluginCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')

            widget.setSupportedCardIcons(supportedCard, true);
        }
        */

        if(pluginCardSettings.cardSupportedCardTypes !== '') {
            var supportedCardTypes = [];

            var supportedCards = pluginCardSettings.cardSupportedCardTypes.replaceAll(' ', '').split(',')
            $.each(supportedCards, function(index, value) {
                supportedCardTypes.push(value);
            });

            widget.setSupportedCardIcons(supportedCardTypes, true);
        }

        widget.setFormFields(["card_name*","card_number*", "card_ccv*"]);
        widget.setEnv(pluginCardSettings.isSandbox ? 'preproduction_cba' : 'production_cba');
        widget.onFinishInsert('input[name="payment_source_token"]', 'payment_source');
        widget.interceptSubmitForm('#widget');
        widget.hideElements(['submit_button']);
        widget.load();

        let performAfterLoadActions = true
        widget.on(window[pluginWidgetName].EVENT.AFTER_LOAD, () => {
            if (performAfterLoadActions && $('#pluginWidgetCard_wrapper').length > 0) {
                pluginValidation.passWidgetToWrapper('pluginWidgetCard');
                performAfterLoadActions = false;
            }
        })

        widget.on(window[pluginWidgetName].EVENT.FINISH, () => {
            if (!!window.widgetErrorInterval) clearInterval(window.widgetErrorInterval);
            let counter = 0;
            window.widgetErrorInterval = setInterval(() => {
                const errorBanner = document.querySelectorAll('.wc-block-components-notice-banner.is-error')[0];
                const bannerContent = errorBanner?.querySelectorAll('.wc-block-components-notice-banner__content')[0];
                if (bannerContent?.innerText.indexOf('widget_error') > -1) {
                    reloadWidget();
                    bannerContent.innerText = bannerContent?.innerText.replace('widget_error', '')
                    clearInterval(window.widgetErrorInterval);
                } else if (counter === 300) {
                    clearInterval(window.widgetErrorInterval);
                } else {
                    counter++;
                }
            }, 100)
        })
    }

    function reloadWidget() {
        const savedCards = document.querySelector( '.' + pluginPrefix + '-select-saved-cards');
        if (savedCards !== null) {
            savedCards.style = 'display: block';
        }
        const settings = window.wc.wcSettings.getSetting(pluginPrefix + '_data', {});
        settings.selectedToken = '';
        window.pluginCardWidget.reload();
        const paymentSourceToken = document.querySelector('[name="payment_source_token"]');
        paymentSourceToken.value = null;
        window.widgetReloaded = true;
    }

    function setPaymentMethodWatcher() {
        $('.wc-block-components-radio-control__input').on('change', (event) => {
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            switch (event.target.value) {
                case pluginPrefix + '_gateway':
                    initPluginWidgetCard();
                    $orderButton.show();
                    break;
                case pluginPrefix + '_bank_account_gateway':
                    initPluginWidgetBankAccount();
                    $orderButton.show();
                    break;
                case pluginPrefix + '_google-pay_wallets_gateway':
                case pluginPrefix + '_apple-pay_wallets_gateway':
                case pluginPrefix + '_afterpay-pay_wallets_gateway':
                case pluginPrefix + '_pay-pal_wallets_gateway':
                case pluginPrefix + '_afterpay_a_p_m_s_gateway':
                case pluginPrefix + '_zip_a_p_m_s_gateway':
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
            const pluginAfterpayWalletsSettings = window.wc?.wcSettings?.getSetting(pluginPrefix + '_afterpay_wallet_block_data', {});
            const $radioWidgetCard = $('#' + idPluginWidgetCard);
            const $radioWidgetBankAccount = $('#' + idPluginWidgetBankAccount);
            const $orderButton = $('.wc-block-components-checkout-place-order-button');
            const $afterpayRadiobatton = $('#radio-control-wc-payment-method-options-' + pluginPrefix + '_afterpay_wallets_gateway');
            if (
                pluginAfterpayWalletsSettings
                && pluginAfterpayWalletsSettings.hasOwnProperty('afterpayChargeId')
                && (pluginAfterpayWalletsSettings.afterpayChargeId.length > 0)
                && searchParams.has('afterpay_success')
                && !wasClick
                && $orderButton.length
                && $afterpayRadiobatton.length
            ) {
                wasClick = true
                $afterpayRadiobatton.parent().click();
                $('#paymentCompleted').show();
                $('#pluginWalletAfterpayButton').hide();
                $orderButton.hide();
                $('#paymentSourceWalletsToken').val(JSON.stringify({
                    data: {
                        id: pluginAfterpayWalletsSettings.afterpayChargeId,
                        status: (searchParams.get('direct_charge') === 'true') ? 'paid' : 'pending'
                    }
                }));
                $orderButton.click();
            }

            if ($radioWidgetCard[0] && $radioWidgetCard[0].checked && !wasInit) {
                wasInit = true;
                initPluginWidgetCard();
                setPaymentMethodWatcher();
            } else if ($radioWidgetBankAccount[0] && $radioWidgetBankAccount[0].checked && !wasInit) {
                wasInit = true;
                initPluginWidgetBankAccount()
                setPaymentMethodWatcher();
            }
        } catch (e) {
            console.log(e);
        }

    }, 100)

}), 1000)
