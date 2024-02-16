import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {useEffect} from 'react';
import axios from 'axios';

const settings = getSetting('paydock_wallets_block_data', {});

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
window.chargeRunning = false;

async function canMakePayment(data) {
    if (window.paydockWallets) {
        return true;
    }
    if (window.chargeRunning) {
        return true;
    }

    window.chargeRunning = true;

    await axios.post('/wp-json/paydock/v1/wallets/charge', data).then((response) => {
        window.paydockWallets = response.data;
    })

    return true;
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
            if (document.getElementById('paymentSourceWalletsToken').value) {
                console.log(document.getElementById('paymentSourceWalletsToken').value)
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
                'min-height': '320px',
                'justify-content': 'center',
                'display': 'flex',
                'align-items': 'center',
                'background-color': settings.styles.background_color,
                'color': settings.styles.success_color,
                'font-size': settings.styles.font_size,
                'font-family': settings.styles.font_family,
            }
        }, 'Payment Details Collected')

    const wallets = [
        createElement('div', {
            id: 'paydockWalletsGoogleApplePay',
            style: {
                height: '55px',
                margin: 'auto'
            }
        }),
        createElement('div', {
            id: 'paydockWalletsPaypal',
            style: {
                height: '55px',
                margin: 'auto'
            }
        }),
        createElement('div', {
            id: 'paydockWalletsAfterPay',
            style: {
                height: '55px',
                margin: 'auto'
            }
        }),
    ];

    return createElement('div', null, description,
        createElement(
            "div",
            { class: 'logo-comm-bank' },
            createElement(
                "img",
                { src: '/wp-content/plugins/paydock/assets/images/commBank_logo.png' }
            ),
        ),
        paymentWasSuccessElement,
        createElement('dev', {
            style: {
                display: 'flex',
                'flex-wrap': 'wrap',
                'justify-content': 'space-between',
                'align-items': 'flex-start',
                'margin-top': '15px',
            }
        }, ...wallets), input);
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
    canMakePayment,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(PaydokWalletBlock);
