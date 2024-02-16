import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';
// import { sleep } from '../includes/wc-paydock';
import {
    checkboxSavedApmsComponent
} from '../includes/wc-paydock';

const settings = getSetting('paydock_apms_data', {});

const textDomain = 'paydock-for-woo';
const labels = {
    defaultLabel: __('Paydock Payments', textDomain),
    placeOrderButtonLabel: __('Place Order by Paydock', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

let formSubmittedAlready = false;
const Content = (props) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup, onCheckoutValidation } = eventRegistration;

    useEffect(() => {
        const validation = onCheckoutValidation(async () => {
            if (settings.selectedToken.trim().length > 0) {
                return true;
            }

            if (formSubmittedAlready) {
                return true;
            }

            let result = true;
            if (result) {
                return true;
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: labels.fillDataError,
            }
        });

        const unsubscribe = onPaymentSetup(async () => {
            const paymentSourceToken = document.querySelector('input[name="payment_source_apm_token"]')
            if (paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = settings
                data.customers = '';
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
            'div',
            null,
            decodeEntities(settings.description || '')
        ),
        createElement(
            "div",
            { class: 'logo-comm-bank' },
            createElement(
                "img",
                { src: '/wp-content/plugins/paydock/assets/images/commBank_logo.png' }
            ),
        ),
        createElement(
            'div',
            { id: 'paydockWidgetApm' },
            createElement('img',
                {
                    src: '/wp-content/plugins/paydock/assets/images/zip_money.png',
                    id: 'zippay',
                    class: 'btn-apm-zippay'
                },
            ),
            createElement('img',
                {
                    src: '/wp-content/plugins/paydock/assets/images/afterpay_icon.png',
                    id: 'afterpay',
                    class: 'btn-apm-afterpay'
                },
            )
        ),
        createElement(
            'input',
            {
                type: 'hidden',
                name: 'payment_source_apm_token'
            }
        ),
        checkboxSavedApmsComponent()
    );
};

const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return <PaymentMethodLabel text={label} />;
};

const PaydokApms = {
    name: 'paydock_apms_gateway',
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

registerPaymentMethod(PaydokApms);