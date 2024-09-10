import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {createElement, useEffect} from 'react';
import {
    checkboxSavedCardsComponent,
    inBuild3Ds,
    selectSavedCardsComponent,
    sleep,
    standalone3Ds
} from '../includes/wc-paydock';
import {select} from '@wordpress/data';
import {CART_STORE_KEY} from '@woocommerce/block-data';

const cart = select(CART_STORE_KEY);
const settings = getSetting('paydock_data', {});

const textDomain = 'paydock';
const labels = {
    defaultLabel: __('Paydock Payments', textDomain),
    saveCardLabel: __('Save payment details', textDomain),
    selectTokenLabel: __('Saved payment details', textDomain),
    fillDataError: __('Please fill in the card data', textDomain),
    notSupport3DS: __('Payment has been rejected by PowerBoard. Please try a different payment method'),
    fillCCDataError: __('Please fill in the required credit card form fields', textDomain),
    requiredDataError: __('Please fill in the required fields of the form to display payment methods', textDomain),
    additionalDataRejected: __('Payment has been rejected by Paydock. Please try again in a few minutes', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

let formSubmittedAlready = false;
const Content = (props) => {
    const {eventRegistration, emitResponse} = props;
    const {onPaymentSetup, onCheckoutValidation, onShippingRateSelectSuccess} = eventRegistration;

    jQuery('.wc-block-components-checkout-place-order-button').show();

    useEffect(() => {
        let newAmount = null;

        const unsubscribeFromShippingEvent = onShippingRateSelectSuccess(async () => {
            const { total_price: currentTotalPrice } = cart.getCartTotals();

            newAmount = Number(currentTotalPrice / 100).toFixed(2);
        });

        const validation = onCheckoutValidation(async () => {
            var errorMessageDismissButton = document.querySelectorAll('.wc-block-components-notice-banner__dismiss')[0]
            if (errorMessageDismissButton) {
                errorMessageDismissButton.click();
            }

            formSubmittedAlready = window.widgetReloaded ? false : formSubmittedAlready

            if (window.hasOwnProperty('paydockValidation')) {
                if (!paydockValidation.paydockCCFormValidation()) {
                    var validationState = window.widgetPaydock.getValidationState();

                    var invalid_fields = [];
                    validationState.invalid_fields.forEach(field => {
                        switch(field) {
                            case "card_name":
                                invalid_fields.push("Card Name");
                                break;
                            case "card_number":
                                invalid_fields.push("Card Number");
                                break;
                            case "expiry_date":
                                invalid_fields.push("Expiry Date");
                                break;
                            case "card_ccv":
                                invalid_fields.push("Card CCV");
                                break;
                        }
                    });

                    var errorMessage = labels.fillCCDataError + (invalid_fields.length ? `: ${invalid_fields.join(", ")}` : "");

                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: errorMessage
                    }
                }

                if (!paydockValidation.wcFormValidation()) {
                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: labels.requiredDataError
                    }
                }
            }

            if (!window.widgetReloaded && settings.selectedToken.length > 0) {
                const selectedToken = settings.tokens.find(item => item.vault_token === settings.selectedToken)
                if (!!selectedToken && selectedToken.hasOwnProperty('customer_id')) {
                    return true;
                } else {
                    if (['IN_BUILD', 'STANDALONE'].includes(settings.card3DS)) {
                        settings.charge3dsId = settings.card3DS === 'IN_BUILD'
                            ? await inBuild3Ds(true, newAmount)
                            : await standalone3Ds();

                        if (settings.charge3dsId === false) {
                            return {
                                type: emitResponse.responseTypes.ERROR,
                                errorMessage: labels.notSupport3DS,
                            }
                        }

                        if ('error' === settings.charge3dsId) {
                            return {
                                type: emitResponse.responseTypes.ERROR,
                                errorMessage: labels.additionalDataRejected,
                            }
                        }

                    }
                }

                return true;
            }

            if (formSubmittedAlready) {
                return true;
            }

            let phoneValue = '';
            if (document.getElementById('shipping-phone') !== null) {
                phoneValue = document.getElementById('shipping-phone').value
            }

            if (document.getElementById('billing-phone') !== null) {
                phoneValue = document.getElementById('billing-phone').value
            }

            window.widgetPaydock.updateFormValues({
                email: document.getElementById('email').value,
                phone: phoneValue
            });
            window.widgetPaydock.trigger(window.paydock.TRIGGER.SUBMIT_FORM);

            let result = false;
            window.widgetPaydock.on(window.paydock.EVENT.FINISH, () => {
                result = true

                const savedCards = document.querySelector('.paydock-select-saved-cards')
                if (savedCards !== null) {
                    savedCards.style = 'display: none'
                }
            })

            const paymentSourceToken = document.querySelector('[name="paydock_payment_source_token"]')
            for (let second = 1; second <= 100; second++) {
                await sleep(100);
                if (paymentSourceToken !== null && paymentSourceToken.value.length) {
                    if (paymentSourceToken.value === 'error') {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.additionalDataRejected,
                        }
                    }
                    if (settings.paymentSourceToken.length === 0 || window.widgetReloaded) {
                        settings.paymentSourceToken = paymentSourceToken.value
                    }
                    result = true
                }

                if (result) {
                    formSubmittedAlready = true
                    break;
                }
            }

            if (result) {
                if (['IN_BUILD', 'STANDALONE'].includes(settings.card3DS)) {
                    settings.charge3dsId = settings.card3DS === 'IN_BUILD'
                        ? await inBuild3Ds(false, newAmount)
                        : await standalone3Ds();

                    if (settings.charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.notSupport3DS,
                        }
                    }

                    if ('error' === settings.charge3dsId) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.additionalDataRejected,
                        }
                    }
                }

                return true;
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: labels.fillDataError,
            }
        });

        const unsubscribe = onPaymentSetup(async () => {
            const paymentSourceToken = document.querySelector('[name="paydock_payment_source_token"]')
            if (paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = {...settings}
                data.tokens = '';
                data.styles = '';
                data.supports = '';

                if(data.total_limitation){
                    delete data.total_limitation;
                }

                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: data
                    },
                };
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                message: labels.fillDataError,
            };
        });
        return () => {
            validation() && unsubscribe() && unsubscribeFromShippingEvent();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentSetup,
        onCheckoutValidation,
    ]);

    return createElement('div',
        null,
        createElement(
            "div",
            null,
            decodeEntities(settings.description || '')
        ),
        selectSavedCardsComponent(labels.selectTokenLabel),
        createElement(
            "div",
            {id: 'paydockWidgetCard_wrapper'}
        ),
        createElement(
            "div",
            {id: 'paydockWidget3ds'}
        ),
        createElement(
            "input",
            {
                type: 'hidden',
                name: 'paydock_payment_source_token'
            }
        ),
        checkboxSavedCardsComponent(labels.saveCardLabel)
    );
};

const Paydok = {
    name: "paydock_gateway",
    label: createElement(() =>
        createElement(
            "div",
            {
                className: 'paydock-payment-method-label'
            },
            createElement("img", {
                src: '/wp-content/plugins/paydock/assets/images/icons/card.png',
                alt: label,
                className: 'paydock-payment-method-label-icon card'
            }),
            "  " + label,
            createElement("img", {
                src: '/wp-content/plugins/paydock/assets/images/logo.png',
                alt: label,
                className: 'paydock-payment-method-label-logo'
            })
        )
    ),
    content: <Content/>,
    edit: <Content/>,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(Paydok);
