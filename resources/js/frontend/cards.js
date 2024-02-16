import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';
import {
    inBuild3Ds,
    standalone3Ds,
    selectSavedCardsComponent,
    checkboxSavedCardsComponent
} from '../includes/wc-paydock';

const settings = getSetting('paydock_data', {});

const textDomain = 'paydock-for-woo';
const labels = {
    defaultLabel: __('Paydock Payments', textDomain),
    saveCardLabel: __('Save card', textDomain),
    selectTokenLabel: __('Saved cards', textDomain),
    placeOrderButtonLabel: __('Place Order by Paydock', textDomain),
    fillDataError: __('Please fill card data', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

let sleepSetTimeout_ctrl;
const sleep = (ms) => {
    clearInterval(sleepSetTimeout_ctrl);
    return new Promise(resolve => sleepSetTimeout_ctrl = setTimeout(resolve, ms));
}

let formSubmittedAlready = false;
const Content = (props) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup, onCheckoutValidation } = eventRegistration;

    useEffect(() => {
        const validation = onCheckoutValidation(async () => {
            if (settings.selectedToken.trim().length > 0) {
                if (settings.card3DS == 'IN_BUILD' || settings.card3DS === 'STANDALONE')
                {
                    settings.charge3dsId = settings.card3DS == 'IN_BUILD' ? await inBuild3Ds(true) : await standalone3Ds()
                    if (settings.charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.fillDataError,
                        }
                    }
                }
                return true;
            }

            if (formSubmittedAlready) {
                return true;
            }

            window.widget.trigger(window.paydock.TRIGGER.SUBMIT_FORM);

            let result = false;
            window.widget.on(window.paydock.EVENT.FINISH, (event) => {
                settings.paymentSourceToken = event.payment_source
                result = true;
            })

            for (let second = 1; second <= 100; second++) {
                await sleep(100);

                if (result) {
                    formSubmittedAlready = true;
                    break;
                }
            }

            if (result) {
                if (settings.card3DS == 'IN_BUILD' || settings.card3DS === 'STANDALONE') {
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
            const paymentSourceToken = document.querySelector('input[name="payment_source_token"]')
            if(paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = settings
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
        selectSavedCardsComponent(labels.selectTokenLabel),
        createElement(
            "div",
            { id: 'paydockWidgetCard' }
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