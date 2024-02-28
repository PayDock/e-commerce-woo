import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';
import {
    inBuild3Ds,
    standalone3Ds,
    selectSavedCardsComponent,
    cvvCode,
    checkboxSavedCardsComponent,
    sleep
} from '../includes/wc-paydock';

const settings = getSetting('paydock_data', {});

const textDomain = 'pay_dock';
const labels = {
    defaultLabel: __('Paydock Payments', textDomain),
    saveCardLabel: __('Save card', textDomain),
    selectTokenLabel: __('Saved cards', textDomain),
    cvvCodeLabel: __('Security number', textDomain),
    placeOrderButtonLabel: __('Place Order by Paydock', textDomain),
    fillDataError: __('Please fill card data', textDomain),
    requiredDataError: __('Please fill required fields of the form to display payment methods', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

let formSubmittedAlready = false;
const Content = (props) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup, onCheckoutValidation } = eventRegistration;

    useEffect(() => {
        const validation = onCheckoutValidation(async () => {
            if (window.hasOwnProperty('paydockValidation')) {
                if (!paydockValidation.wcFormValidation()) {
                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: labels.requiredDataError
                    }
                }
            }

            if (settings.selectedToken.length > 0) {
                if (settings.cvv.trim().length === 0) {
                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: labels.fillDataError,
                    }
                }

                const selectedToken = settings.tokens.find(item => item.vault_token === settings.selectedToken)
                if (typeof selectedToken !== undefined && selectedToken.hasOwnProperty('customer_id')) {
                    return true;
                } else {
                    if (['IN_BUILD', 'STANDALONE'].includes(settings.card3DS)) {
                        settings.charge3dsId = settings.card3DS == 'IN_BUILD' ? await inBuild3Ds(true) : await standalone3Ds()
                        if (settings.charge3dsId === false) {
                            return {
                                type: emitResponse.responseTypes.ERROR,
                                errorMessage: labels.fillDataError,
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

            window.widget.updateFormValues({
                email: document.getElementById('email').value,
                phone: phoneValue
            });

            window.widget.trigger(window.paydock.TRIGGER.SUBMIT_FORM);

            let result = false;
            window.widget.on(window.paydock.EVENT.FINISH, (event) => {
                result = true

                const savedCards = document.querySelector('.paydock-select-saved-cards')
                if (savedCards !== null) {
                    savedCards.style = 'display: none'
                }
            })

            const paymentSourceToken = document.querySelector('[name="payment_source_token"]')
            for (let second = 1; second <= 100; second++) {
                await sleep(100);

                if (paymentSourceToken !== null && paymentSourceToken.value.length) {
                    if (settings.paymentSourceToken.length === 0) {
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
                    settings.charge3dsId = settings.card3DS == 'IN_BUILD' ? await inBuild3Ds() : await standalone3Ds()
                    if (settings.charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.fillDataError,
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
            const paymentSourceToken = document.querySelector('[name="payment_source_token"]')
            if (paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = { ...settings }
                data.tokens = '';
                data.styles = '';
                data.supports = '';

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
            validation() && unsubscribe();
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
        createElement(
            "div",
            { class: 'logo-comm-bank' },
            createElement(
                "img",
                { src: '/wp-content/plugins/paydock/assets/images/logo.png' }
            ),
        ),
        selectSavedCardsComponent(labels.selectTokenLabel),
        cvvCode(labels.cvvCodeLabel),
        createElement(
            "div",
            { id: 'paydockWidgetCard_wrapper' }
        ),
        createElement(
            "div",
            { id: 'paydockWidget3ds' }
        ),
        createElement(
            "input",
            {
                type: 'hidden',
                name: 'payment_source_token'
            }
        ),
        checkboxSavedCardsComponent(labels.saveCardLabel)
    );
};

const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return <PaymentMethodLabel text={label} />;
};

const Paydok = {
    name: "paydock_gateway",
    label: <Label />,
    content: <Content />,
    edit: <Content />,
    placeOrderButtonLabel: labels.placeOrderButtonLabel,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(Paydok);