import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {useEffect} from 'react';

const settings = getSetting('paydock_bank_account_block_data', {});

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
            window.widgetBankAccount.trigger(window.paydock.TRIGGER.SUBMIT_FORM);
            let result = false;

            window.widgetBankAccount.on(window.paydock.EVENT.FINISH, (data) => {
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

            const paymentSourceToken = document.querySelector('input[name="payment_source_bank_account_token"]').value;
            const saveVault = document.querySelector('input[name="payment_source_bank_account_save_data"]').checked;
            const gatewayId = settings.gatewayId;
            const customDataIsValid = !!paymentSourceToken.length;
            const saveAccount = settings.saveAccount;
            const saveAccountType = settings.saveAccountType;

            if (customDataIsValid) {
                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            paymentSourceToken,
                            saveVault,
                            gatewayId,
                            saveAccount,
                            saveAccountType
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
        {id: 'paydockWidgetBankAccount'}
    );

    const input = createElement(
        "input",
        {
            type: 'hidden',
            name: 'payment_source_bank_account_token'
        }
    );

    let options = [];
    for (const [key, value] of Object.entries(settings.vaults)) {
        options.push(createElement('option', {value: key}))
    }

    // const savedAccounts = createElement("select", ...options)
    //
    // console.log(options);

    const saveCardCheckBox = createElement(
        "div",
        {
            class: 'wc-block-components-checkbox',
            hidden: !settings.showSaveDataCheckBox
        },
        createElement(
            'label', null, createElement(
                'input',
                {
                    type: 'checkbox',
                    name: 'payment_source_bank_account_save_data',
                    class: 'wc-block-components-checkbox__input'
                }
            ),
            createElement(
                'svg',
                {
                    class: 'wc-block-components-checkbox__mark',
                    'aria-hidden': 'true',
                    xmlns: 'http://www.w3.org/2000/svg',
                    viewBox: '0 0 24 20'
                },
                createElement(
                    'path',
                    {
                        d: 'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z'
                    }
                )
            ),
            createElement(
                'span',
                {
                    class: 'wc-block-components-checkbox__label'
                },
                'Save bank account',
            )
        )
    );

    return createElement('div', null, description, widget, input, saveCardCheckBox);
};


const Label = (props) => {
    const {PaymentMethodLabel} = props.components;
    return <PaymentMethodLabel text={label}/>;
};

const PaydokBankAccountBlock = {
    name: "paydock_bank_account_gateway",
    label: <Label/>,
    content: <Content/>,
    edit: <Content/>,
    placeOrderButtonLabel: placeOrderButtonLabel,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(PaydokBankAccountBlock);
