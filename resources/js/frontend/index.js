import { __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';
import { useEffect } from 'react';
import { SelectControl } from '@wordpress/components';

const settings = getSetting('paydock_data', {});

const defaultLabel = __(
    'Paydock Payments',
    'paydock-for-woo'
);

const saveCardLabel = __(
    'Save card',
    'paydock-for-woo'
)

const selectTokenLabel = __(
    'Select card',
    'paydock-for-woo'
)

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

const getVaultToken = async (ottToken) => {
    return jQuery.post(PaydockAjax.url, {
        action: 'get_vault_token',
        paymentsourcetoken: ottToken,
        cardsavecard: true
    }).then();
}

const getStandalone3dsToken = async (vaultToken) => {
    return jQuery.post(PaydockAjax.url, {
        action: 'get_vault_token',
        type: 'standalone-3ds-token',
        vaulttoken: vaultToken,
        gatewayid: settings.gatewayId,
        amount: settings.cardTotal,
        curency: settings.curency,
        card3dsserviceid: settings.card3DSServiceId
    }).then();
}

const standalone3Ds = async (ottToken) => {
    settings.selectedToken = await getVaultToken(ottToken)
    const threeDsToken = await getStandalone3dsToken(settings.selectedToken)

    const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', threeDsToken);
    canvas.load();

    const chargeAuthSuccessEvent = await canvas.on('chargeAuthSuccess');

    return chargeAuthSuccessEvent.charge_3ds_id;
}

const inBuild3Ds = async (ottToken) => {
    const preAuthData = {
        amount: settings.cardTotal,
        currency: settings.currency
    };

    if (settings.card3DSFlow === 'PERMANENT_VAULT') {
        preAuthData.customer = {
            payment_source: {
                vault_token: await getVaultToken(ottToken),
                gateway_id: settings.gatewayId
            }
        }
    } else {
        preAuthData.token = ottToken
    }

    const envVal = settings.isSandbox ? 'sandbox' : 'production'
    const preAuthResp = await new window.paydock.Api(settings.publicKey)
        .setEnv(envVal)
        .charge()
        .preAuth(preAuthData);

    if (typeof preAuthResp._3ds.token === "undefined") {
        return false;
    }

    const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', preAuthResp._3ds.token);
    canvas.load();

    document.getElementById('paydockWidgetCard').setAttribute('style', 'display: none')

    const chargeAuthEvent = await canvas.on('chargeAuth');

    return chargeAuthEvent.charge_3ds_id;
}

window.formSubmittedAlready = false;

const selectSavedCardsComponent = () => {
    if (!settings.cardSaveCard || !settings.isUserLoggedIn) {
        return '';
    }

    const options = [{
        label: selectTokenLabel,
        value: ''
    }];

    settings.cardTokens.forEach(token => {
        let label = `${token.card_number_bin}****${token.card_number_last4}`;
        if (token.card_name !== undefined) {
            label = `${token.card_name} ${token.card_number_bin}****${token.card_number_last4}`;
        }
        options.push({
            label: label,
            value: token.vault_token
        })
    })

    return (
        <SelectControl
            options={options}
            onChange={(value) => {
                settings.selectedToken = value

                window.widget.setFormValue('card_name', '')
                window.widget.setFormValue('card_number', '')
                document.getElementById('card_save_card').disabled = false

                if (value !== '') {
                    const token = settings.cardTokens.find(token => token.vault_token === value)
                    if (token !== undefined) {
                        if (token.card_name !== undefined) {
                            window.widget.setFormValue('card_name', token.card_name)
                        }
                        window.widget.setFormValue('card_number', `${token.card_number_bin}`)

                        document.getElementById('card_save_card').disabled = true
                    }
                }

                window.widget.reload()
            }}
        />
    );
};

const Content = (props) => {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup, onCheckoutValidation } = eventRegistration;

    useEffect(() => {
        const validation = onCheckoutValidation(async () => {
            if (settings.selectedToken !== undefined && settings.selectedToken !== '') {
                return true;
            }

            if (window.formSubmittedAlready) {
                return true;
            }

            window.widget.trigger(window.paydock.TRIGGER.SUBMIT_FORM);

            let result = false;
            let ottToken;
            window.widget.on(window.paydock.EVENT.FINISH, (event) => {
                ottToken = event.payment_source
                result = true;
            })

            for (let second = 1; second <= 100; second++) {
                await sleep(100);

                if (result) {
                    window.formSubmittedAlready = true;
                    break;
                }
            }

            if (result) {
                if (settings.card3DS === 'IN_BUILD') {
                    const charge3dsId = await inBuild3Ds(ottToken)
                    settings.charge3dsId = charge3dsId;

                    if (charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: fillDataError,
                        }
                    }
                } else if (settings.card3DS === 'STANDALONE') {
                    const charge3dsId = await standalone3Ds(ottToken)
                    settings.charge3dsId = charge3dsId;

                    if (charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: fillDataError,
                        }
                    }
                }

                return true;
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: fillDataError,
            }
        });

        const unsubscribe = onPaymentSetup(async () => {
            const paymentSourceToken = document.querySelector('input[name="payment_source_token"]').value;
            const gatewayId = settings.gatewayId;
            const cardDirectCharge = settings.cardDirectCharge;
            const cardSaveCard = settings.cardSaveCard;
            const cardSaveCardOption = settings.cardSaveCardOption;
            const card3DS = settings.card3DS;
            const card3DSServiceId = settings.card3DSServiceId;
            const card3DSFlow = settings.card3DSFlow;
            const cardFraud = settings.cardFraud;
            const cardFraudServiceId = settings.cardFraudServiceId;

            let cardSaveCardChecked = false;
            if (cardSaveCard && document.getElementById('card_save_card') !== null) {
                cardSaveCardChecked = document.getElementById('card_save_card').checked;
            }

            let charge3dsId;
            if (typeof settings.charge3dsId !== "undefined") {
                charge3dsId = settings.charge3dsId;
            }

            let selectedToken;
            const selectedTokenNotEmpty = settings.selectedToken !== undefined && settings.selectedToken !== '';
            if (settings.selectedToken !== undefined) {
                selectedToken = settings.selectedToken
            }

            const customDataIsValid = !!paymentSourceToken.length || selectedTokenNotEmpty;
            if (customDataIsValid) {
                return {
                    type: emitResponse.responseTypes.SUCCESS,
                    meta: {
                        paymentMethodData: {
                            selectedToken,
                            paymentSourceToken,
                            gatewayId,
                            cardDirectCharge,
                            cardSaveCard,
                            cardSaveCardOption,
                            cardSaveCardChecked,
                            card3DS,
                            card3DSServiceId,
                            card3DSFlow,
                            charge3dsId,
                            cardFraud,
                            cardFraudServiceId
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
            validation() && unsubscribe();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentSetup,
        onCheckoutValidation,
    ]);

    let selectSavedCards = '';
    if (settings.cardTokens.length > 0) {
        selectSavedCards = selectSavedCardsComponent();
    }

    let saveCard = '';
    if (settings.isUserLoggedIn && settings.cardSaveCard) {
        saveCard = createElement("div",
            { class: 'wc-block-components-checkbox' },
            createElement("label",
                { for: 'card_save_card' },
                createElement("input",
                    {
                        class: 'wc-block-components-checkbox__input',
                        id: 'card_save_card',
                        type: 'checkbox',
                        name: 'card_save_card'
                    }
                ),
                createElement("svg",
                    {
                        class: 'wc-block-components-checkbox__mark',
                        "aria-hidden": true,
                        xmlns: 'http://www.w3.org/2000/svg',
                        "viewBox": '0 0 24 20'
                    },
                    createElement("path", { d: 'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z' })
                ),
                createElement("span",
                    { class: 'wc-block-components-checkbox__label' },
                    saveCardLabel
                )
            )
        );
    }

    return createElement('div',
        null,
        createElement(
            "div",
            null,
            decodeEntities(settings.description || '')
        ),
        selectSavedCards,
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
        saveCard);
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
    placeOrderButtonLabel: placeOrderButtonLabel,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(Paydok);