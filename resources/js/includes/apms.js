import {__} from '@wordpress/i18n';
import {createElement, useEffect} from 'react';
import {decodeEntities} from "@wordpress/html-entities";
import {getSetting} from "@woocommerce/settings";
import validateData from "./wallets/validate-form";
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {select} from '@wordpress/data';
import {CART_STORE_KEY, CHECKOUT_STORE_KEY} from '@woocommerce/block-data';

const textDomain = 'power_board';
const labels = {
    defaultLabel: __('Power Board Payments', textDomain),
    placeOrderButtonLabel: __('Place Order by Power Board', textDomain),
    validationError: __('Please fill in the required fields of the form to display payment methods', textDomain),
    notAvailable: __('The payment method is not available in your country.', textDomain),
}
let wasInit = false;
export default (id, defaultLabel, buttonId, dataFieldsRequired, countries) => {

    const settingKey = `power_board_${id}_a_p_m_s_block_data`;
    const paymentName = `power_board_${id}_a_p_m_s_gateway`;

    const settings = getSetting(settingKey, {});
    const label = decodeEntities(settings.title) || __(defaultLabel, textDomain);
    const Content = (props) => {
        const cart = select(CART_STORE_KEY);
        const store = select(CHECKOUT_STORE_KEY);
        const {eventRegistration, emitResponse} = props;
        const {onPaymentSetup, onCheckoutValidation} = eventRegistration;

        const billingAddress = cart.getCustomerData().billingAddress;
        const countriesError = jQuery('.power-board-country-available');
        const validationError = jQuery('.power-board-validation-error');
        const buttonElement = jQuery('#' + buttonId);
        const orderButton = jQuery('.wc-block-components-checkout-place-order-button');
        const paymentCompleteElement = jQuery('#paymentCompleted');

        let validationSuccess = validateData(billingAddress, dataFieldsRequired);
        let isAvailableCountry = !!countries.find(
            (element) => element === billingAddress.country.toLowerCase()
        );
        let button = null;
        let meta = null
        let data = {...settings};
        data.customers = '';
        data.styles = '';
        data.supports = '';

        validationError.hide();
        countriesError.hide();
        buttonElement.hide();

        if (!validationSuccess) {
            wasInit = false;
            validationError.show();
        } else if (validationSuccess && !isAvailableCountry) {
            wasInit = false;
            countriesError.show();
        } else if (validationSuccess && isAvailableCountry) {
            buttonElement.show();
        }
        setTimeout(() => {
            if ((validationSuccess && 'zip' === id) && !wasInit) {
                wasInit = true;
                button = new window.cba.ZipmoneyCheckoutButton('#' + buttonId, settings.publicKey, settings.gatewayId);

                meta = {
                    charge: {
                        amount: settings.amount,
                        currency: settings.currency,
                    }
                }

                data.gatewayType = 'zippay'
            } else if ((validationSuccess && 'afterpay' === id) && !wasInit) {
                wasInit = true;
                button = new window.cba.AfterpayCheckoutButton('#' + buttonId, settings.publicKey, settings.gatewayId);
                meta = {
                    amount: settings.amount,
                    currency: settings.currency,
                    email: billingAddress.email,
                    first_name: billingAddress.first_name,
                    last_name: billingAddress.last_name
                }

                data.gatewayType = 'afterpay'
            }

            if (button) {
                button.onFinishInsert('input[name="payment_source_apm_token"]', 'payment_source_token');
                button.setMeta(meta);
                button.on('finish', () => {
                    if (settings.directCharge) {
                        data.directCharge = true
                    }

                    if (settings.fraud) {
                        data.fraud = true
                        data.fraudServiceId = settings.fraudServiceId
                    }

                    if (orderButton !== null) {
                        orderButton.click()
                    }
                    paymentCompleteElement.show();
                })
            }
        }, 100)

        useEffect(() => {
            const unsubscribe = onPaymentSetup(async () => {
                const paymentSourceToken = document.querySelector('input[name="payment_source_apm_token"]')
                if (paymentSourceToken === null) {
                    return;
                }

                data.paymentSourceToken = paymentSourceToken.value;
                if (data.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
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
                unsubscribe();
            };
        }, [
            emitResponse.responseTypes.ERROR,
            emitResponse.responseTypes.SUCCESS,
            onPaymentSetup,
            onCheckoutValidation,
        ]);

        return createElement(
            'div',
            {id: 'powerBoardWidgetApm'},
            createElement('div', {
                id: 'paymentCompleted', style: {
                    display: 'none',
                    'background-color': settings.styles.background_color,
                    'color': settings.styles.success_color,
                    'font-size': settings.styles.font_size,
                    'font-family': settings.styles.font_family,
                }
            }, 'Payment Details Collected'),
            createElement(
                'div',
                null,
                decodeEntities(settings.description || '')
            ),
            createElement(
                "div",
                {class: 'logo-comm-bank'},
                createElement(
                    "img",
                    {src: '/wp-content/plugins/power_board/assets/images/logo.png'}
                ),
            ),
            createElement('div', {
                class: 'apms-button-wrapper',
            }, createElement('button',
                {
                    type: 'button',
                    id: buttonId,
                    class: `btn-apm btn-apm-${id}`,
                    style: {
                        display: 'none',
                    }
                },
                createElement('img',
                    {
                        src: `/wp-content/plugins/power_board/assets/images/${id}.png`,
                    },
                ),
            ),),
            createElement(
                'div',
                {
                    class: 'power-board-validation-error',
                },
                labels.validationError
            ),
            createElement(
                'input',
                {
                    type: 'hidden',
                    name: 'payment_source_apm_token'
                }
            ),
            createElement(
                "div",
                {
                    class: 'power-board-country-available',
                    style: {
                        display: 'none'
                    }
                },
                labels.notAvailable
            ),
        )
    }
    const Label = (props) => {
        const {PaymentMethodLabel} = props.components;
        return <PaymentMethodLabel text={label}/>;
    };

    const PaydokApms = {
        name: paymentName,
        label: <Label/>,
        content: <Content/>,
        edit: <Content/>,
        placeOrderButtonLabel: labels.placeOrderButtonLabel,
        canMakePayment: () => true,
        ariaLabel: label,
        supports: {
            features: settings.supports,
        },
    };

    registerPaymentMethod(PaydokApms);
}
