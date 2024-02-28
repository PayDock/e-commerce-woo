import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {useEffect} from 'react';
import axios from 'axios';

const settings = getSetting('paydock_wallets_block_data', {});

const textDomain = 'pay_dock';
const labels = {
    defaultLabel: __('Paydock Payments', textDomain),
    validationError: __('Please fill required fields of the form to display payment methods', textDomain),
    fillDataError: __('The payment service does not accept payment. Please try again later or choose another ' +
        'payment method.', textDomain),
    availableAfterpay: __('Payment method Afterpay is not avalaible for your country!!!', textDomain)
}
window.axios = axios;
const label = decodeEntities(settings.title) || labels.defaultLabel;

const Content = (props) => {
    const {eventRegistration, emitResponse} = props;
    const {onPaymentSetup, onCheckoutValidation} = eventRegistration;

    useEffect(() => {
        const oncheckout = onCheckoutValidation(async (data) => {
            if (window.hasOwnProperty('paydockValidation')) {
                if (!paydockValidation.wcFormValidation()) {
                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: labels.fillDataError
                    }
                }
            }

            if (document.getElementById('paymentSourceWalletsToken').value
                && (new URLSearchParams(window.location.search)).get('afterpay_success') !== 'false') {
                return true;
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: labels.fillDataError,
            }
        });
        const unsubscribe = onPaymentSetup(async (data) => {
            console.log(data);
            if (document.getElementById('paymentSourceWalletsToken').value
                && (new URLSearchParams(window.location.search)).get('afterpay_success') !== 'false') {
                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            payment_response: document.getElementById('paymentSourceWalletsToken').value
                        }
                    },
                };
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: labels.fillDataError,
            }
        });
        return () => {
            unsubscribe() && oncheckout();
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

    const input = createElement(
        "input",
        {
            type: 'hidden',
            id: 'paymentSourceWalletsToken'
        }
    );
    const paymentWasSuccessElement = createElement('div',
        {
            id: 'paymentCompleted',
            style: {
                display:'none',
                'background-color': settings.styles.background_color,
                'color': settings.styles.success_color,
                'font-size': settings.styles.font_size,
                'font-family': settings.styles.font_family,
            }
        }, 'Payment Details Collected')

    const wallets = [
        createElement('div', {
            id: 'paydockWalletsGoogleApplePay',
            class: "paudock-wallets-buttons",
        }),
        createElement('div', {
            id: 'paydockWalletsAfterPay',
            class: "paudock-wallets-buttons",
        }),
        createElement('div', {
            id: 'paydockWalletsPaypal',
            class: "paudock-wallets-buttons",
        }),
    ];

    return createElement('div', null, description,
        createElement(
            "div",
            {class: 'logo-comm-bank'},
            createElement(
                "img",
                {src: '/wp-content/plugins/paydock/assets/images/logo.png'}
            ),
        ),
        paymentWasSuccessElement,
        createElement('div', {
            id: 'paydockWidgetWallets',

            class: 'paydock-widget-content',
        }, ...wallets),
        createElement(
            'div',
            {
                class: 'paydock-validation-error',
            },
            labels.validationError
        ),
        createElement(
            "div",
            {
                class: 'paydock-country-available-afterpay',
                style:{
                    display:'none'
                }
            },
            labels.availableAfterpay
        ), input);
};


const Label = (props) => {
    const {PaymentMethodLabel} = props.components;
    return <PaymentMethodLabel text={label}/>;
};

const PaydokWalletBlock = {
    name: "paydock_wallets_gateway",
    label: <Label/>,
    content: <Content/>,
    edit: <Content/>,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(PaydokWalletBlock);
