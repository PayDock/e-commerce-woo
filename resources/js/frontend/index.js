import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';

const settings = getSetting('paydock_data', {});

const defaultLabel = __(
    'Paydock Payments',
    'paydock-for-woo'
);

const placeOrderButtonLabel = __(
    'Place Order by Paydock',
    'paydock-for-woo'
);

const fillDataError = __(
    'Please fill card data',
    'paydock-for-woo'
);

function getPromiseFromEvent(item, event) {
    return new Promise((resolve) => {
        const listener = () => {
            item.removeEventListener(event, listener);
            resolve();
        }
        item.addEventListener(event, listener);
    })
}

const label = decodeEntities(settings.title) || defaultLabel;

let sleepSetTimeout_ctrl;

function sleep(ms) {
    clearInterval(sleepSetTimeout_ctrl);
    return new Promise(resolve => sleepSetTimeout_ctrl = setTimeout(resolve, ms));
}

const Content = (props) => {
    const {eventRegistration, emitResponse} = props;
    const {onPaymentSetup} = eventRegistration;

    useEffect(() => {
        const unsubscribe = onPaymentSetup(async () => {
            window.widget.trigger('submit_form');
            let result = false;

            window.widget.on(paydock.EVENT.FINISH, (data) => {
                result = true;
            })

            for (let second = 1; second <= 100; second++) {
                await sleep(100);

                if (result) {
                    break;
                }
            }

            // Here we can do any processing we need, and then emit a response.
            // For example, we might validate a custom field, or perform an AJAX request, and then emit a response indicating it is valid or not.

            const paymentSourceToken = document.querySelector('input[name="payment_source_token"]').value;
            const gatewayId = settings.gatewayId;
            const cardDirectCharge = settings.cardDirectCharge;
            const cardSaveCard = settings.cardSaveCard;
            const customDataIsValid = !!paymentSourceToken.length;

            if (customDataIsValid) {
                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            paymentSourceToken,
                            gatewayId,
                            cardDirectCharge,
                            cardSaveCard
                        },
                    },
                };
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                message: fillDataError,
            };
        });
        return () => {
            unsubscribe();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentSetup,
    ]);

    const description = createElement(
        "div",
        null,
        decodeEntities(settings.description || '')
    );

    const widget = createElement(
        "div",
        {id: 'paydockWidgetCard'}
    );

    const input = createElement(
        "input",
        {
            type: 'hidden',
            name: 'payment_source_token'
        }
    );

    return createElement('div', null, description, widget, input);
};

const Label = (props) => {
    const {PaymentMethodLabel} = props.components;
    return <PaymentMethodLabel text={label}/>;
};

const Paydok = {
    name: "paydock_gateway",
    label: <Label />,
    content: <Content />,
    edit: <Content />,
    placeOrderButtonLabel: placeOrderButtonLabel,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(Paydok);