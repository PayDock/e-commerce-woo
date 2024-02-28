import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';

const settings = getSetting('power_board_apms_data', {});

const textDomain = 'power_board';
const labels = {
    defaultLabel: __('PowerBoard Payments', textDomain),
    placeOrderButtonLabel: __('Place Order by PowerBoard', textDomain),
    validationError: __('Please fill required fields of the form to display payment methods', textDomain),
    availableAfterpay: __('Payment method Afterpay is not avalaible for your country!!!', textDomain),
    availableZippay: __('Payment method Zippay is not avalaible for your country!!!', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

const Content = (props) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup, onCheckoutValidation } = eventRegistration;

    useEffect(() => {
        const unsubscribe = onPaymentSetup(async () => {
            const paymentSourceToken = document.querySelector('input[name="payment_source_apm_token"]')
            if (paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = { ...settings }
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
            unsubscribe();
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
                { src: '/wp-content/plugins/power_board/assets/images/logo.png' }
            ),
        ),
        createElement(
            'div',
            {
                id: 'powerBoardWidgetApm',
                class: 'power_board-widget-content',
                style: {
                    display: 'none',
                    'text-align': 'center'
                }
            },
            createElement('button',
                {
                    type: 'button',
                    src: '/wp-content/plugins/power_board/assets/images/zip_money.png',
                    id: 'zippay',
                    class: 'btn-apm btn-apm-zippay',
                    style: {
                        display: 'none',
                    }
                },
                createElement('img',
                    {
                        src: '/wp-content/plugins/power_board/assets/images/zip_money.png',
                    },
                ),
            ),
            createElement('button',
                {
                    type: 'button',
                    src: '/wp-content/plugins/power_board/assets/images/zip_money.png',
                    id: 'afterpay',
                    class: 'btn-apm btn-apm-afterpay',
                    style: {
                        display: 'none',
                    }
                },
                createElement('img',
                    {
                        src: '/wp-content/plugins/power_board/assets/images/afterpay_icon.png',
                    },
                ),
            ),
        ),
        createElement(
            'div',
            {
                class: 'power_board-validation-error',
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
                class: 'power_board-country-available-afterpay',
                style:{
                    display:'none'
                }
            },
            labels.availableAfterpay
        ),
        createElement(
            "div",
            {
                class: 'power_board-country-available-zippay',
                style:{
                    display:'none'
                }
            },
            labels.availableZippay
        ),
    );
};

const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return <PaymentMethodLabel text={label} />;
};

const PaydokApms = {
    name: 'power_board_apms_gateway',
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
