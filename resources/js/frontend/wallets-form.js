import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {useEffect} from 'react';
import axios from 'axios';

const settings = getSetting('power_board_wallets_block_data', {});

const textDomain = 'power_board';
const labels = {
    defaultLabel: __('PowerBoard Payments', textDomain),
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
            if (window.hasOwnProperty('powerBoardValidation')) {
                if (!powerBoardValidation.wcFormValidation()) {
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
            id: 'powerBoardWalletsGoogleApplePay',
            class: "paudock-wallets-buttons",
        }),
        createElement('div', {
            id: 'powerBoardWalletsPaypal',
            class: "paudock-wallets-buttons",
        }),
        createElement('div', {
            id: 'powerBoardWalletsAfterPay',
            class: "paudock-wallets-buttons",
        }),
        createElement('div', {
            id: 'powerBoardWalletsPaypal',
            class: "paudock-wallets-buttons",
        }),
    ];

    return createElement('div', null, description,
        createElement(
            "div",
            {class: 'logo-comm-bank'},
            createElement(
                "img",
                {src: '/wp-content/plugins/power_board/assets/images/logo.png'}
            ),
        ),
        paymentWasSuccessElement,
        createElement('div', {
            id: 'powerBoardWidgetWallets',

            class: 'power_board-widget-content',
        }, ...wallets),
        createElement(
            'div',
            {
                class: 'power_board-validation-error',
            },
            labels.validationError
        ),
        createElement(
            "div",
            {
                class: 'power_board-country-available-afterpay',
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
    name: "power_board_wallets_gateway",
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
