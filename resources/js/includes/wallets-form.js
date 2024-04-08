import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {select} from '@wordpress/data';
import {CART_STORE_KEY, CHECKOUT_STORE_KEY} from '@woocommerce/block-data';
import {createElement, useEffect} from 'react';
import validateData from './wallets/validate-form';
import initButton from './wallets/init';
import axios from 'axios';


const textDomain = 'pay_dock';
const labels = {
    validationError: __('Please fill in the required fields of the form to display payment methods', textDomain),
    fillDataError: __('The payment service does not accept payment. Please try again later or choose another payment method.', textDomain),
    notAvailable: __('Payment method is not avalaible for your country!!!', textDomain)
}

const afterpayCountries = ['au', 'nz', 'us', 'ca', 'uk', 'gb', 'fr', 'it', 'es', 'de'];

let localState = {
    initData: null,
    wasInit: false
}
export default (id, defaultLabel, buttonId, dataFieldsRequired) => {
    const settingKey = `paydock_${id}_wallet_block_data`;
    const paymentName = `paydock_${id}_wallets_gateway`;

    const settings = getSetting(settingKey, {});
    const label = decodeEntities(settings.title) || __(defaultLabel, textDomain);
    const Content = (props) => {
        const store = select(CHECKOUT_STORE_KEY);
        const cart = select(CART_STORE_KEY);
        const {eventRegistration, emitResponse} = props;
        const {onPaymentSetup, onCheckoutValidation} = eventRegistration;
        const billingAddress = cart.getCustomerData().billingAddress;
        const afterpayCountriesError = jQuery('.paydock-country-available');
        const completed = jQuery("#paymentCompleted");

        let validationSuccess = validateData(billingAddress, dataFieldsRequired);

        jQuery('.wc-block-components-checkout-place-order-button').hide();
        let button = jQuery('#' + buttonId).length

        if ((new URLSearchParams(window.location.search)).get('afterpay_success') === 'true') {
            completed.show();
        } else if (('paydockWalletAfterpayButton' === buttonId)
            && validationSuccess
            && !afterpayCountries.find((element) => element === billingAddress.country.toLowerCase())) {
            afterpayCountriesError.show()
        } else if (validationSuccess && !localState.initData && !localState.wasInit) {
            afterpayCountriesError.hide()
            localState.wasInit = true;
            let billingData = {
                type: id,
                order_id: store.getOrderId(),
                total: cart.getCartTotals(),
                address: cart.getCustomerData().billingAddress
            }

            axios.post('/wp-json/paydock/v1/wallets/charge', billingData).then((response) => {
                localState.initData = response.data
                setTimeout(() => {
                    initButton(id, '#' + buttonId, localState.initData, settings.isSandbox)
                }, 100)
            }).catch((e) => {
                localState.wasInit = false;
            })
        } else if (validationSuccess && localState.initData && !button) {
            afterpayCountriesError.hide()
            setTimeout(() => {
                initButton(id, '#' + buttonId, localState.initData, settings.isSandbox)
            }, 100)
        }

        useEffect(() => {
            const oncheckout = onCheckoutValidation(async (data) => {
                if (!validationSuccess) {
                    return {
                        type: emitResponse.responseTypes.ERROR, errorMessage: labels.fillDataError
                    }
                }

                if (document.getElementById('paymentSourceWalletsToken').value
                    && (new URLSearchParams(window.location.search)).get('afterpay_success') !== 'false') {
                    return true;
                }

                return {
                    type: emitResponse.responseTypes.ERROR, errorMessage: labels.fillDataError,
                }
            });

            const unsubscribe = onPaymentSetup(async (data) => {
                if (document.getElementById('paymentSourceWalletsToken').value
                    && (new URLSearchParams(window.location.search)).get('afterpay_success') !== 'false') {
                    return {
                        type: emitResponse.responseTypes.SUCCESS, meta: {
                            paymentMethodData: {
                                payment_response: document.getElementById('paymentSourceWalletsToken').value,
                                wallets: JSON.stringify(settings.wallets)
                            }
                        },
                    };
                }

                return {
                    type: emitResponse.responseTypes.ERROR, errorMessage: labels.fillDataError,
                }
            });
            return () => {
                unsubscribe() && oncheckout() && onEmitter;
            };
        }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup,]);

        const description = createElement("div", null, decodeEntities(settings.description || ''));

        const input = createElement("input", {
            type: 'hidden', id: 'paymentSourceWalletsToken'
        });

        const paymentWasSuccessElement = createElement('div', {
            id: 'paymentCompleted', style: {
                display: 'none',
                'background-color': settings.styles.background_color,
                'color': settings.styles.success_color,
                'font-size': settings.styles.font_size,
                'font-family': settings.styles.font_family,
            }
        }, 'Payment Details Collected')

        const wallets = [createElement('div', {
            id: buttonId, class: "paudock-wallets-buttons",
        }),];

        return createElement(
            'div',
            null,
            description,
            createElement(
                "div",
                {class: 'logo-comm-bank'},
                createElement(
                    "img",
                    {src: '/wp-content/plugins/paydock/assets/images/logo.png'}
                ),
            ),
            paymentWasSuccessElement,
            createElement(
                'div',
                {id: 'paydockWidgetWallets', class: 'paydock-widget-content',},
                ...wallets
            ), createElement(
                'div',
                {class: 'paydock-validation-error', style: {display: validationSuccess ? 'none' : ''}},
                labels.validationError
            ), createElement(
                "div",
                {class: 'paydock-country-available', style: {display: 'none'}},
                labels.notAvailable
            ),
            input
        );
    };


    const Label = (props) => {
        const {PaymentMethodLabel} = props.components;
        return <PaymentMethodLabel text={label}/>;
    };

    const PaydokWalletBlock = {
        name: paymentName,
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
}
