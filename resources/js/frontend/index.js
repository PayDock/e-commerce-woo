import {__} from '@wordpress/i18n';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {createElement, useEffect} from 'react';
import {
    checkboxSavedCardsComponent,
    inBuild3Ds,
    selectSavedCardsComponent,
    sleep,
    standalone3Ds
} from '../includes/wc-power-board';

const settings = getSetting('power_board_data', {});

const textDomain = 'power-board';
const labels = {
    defaultLabel: __('PowerBoard Payments', textDomain),
    saveCardLabel: __('Save payment details', textDomain),
    selectTokenLabel: __('Saved payment details', textDomain),
    fillDataError: __('Please fill in the card data.', textDomain),
    requiredDataError: __('Please fill in the required fields of the form to display payment methods', textDomain),
    additionalDataRejected: __('Payment has been rejected by PowerBoard. Please try again in a few minutes', textDomain)
}

const label = decodeEntities(settings.title) || labels.defaultLabel;

let formSubmittedAlready = false;
const Content = (props) => {
    const {eventRegistration, emitResponse} = props;
    const {onPaymentSetup, onCheckoutValidation} = eventRegistration;

    jQuery('.wc-block-components-checkout-place-order-button').show();

    useEffect(() => {
        const validation = onCheckoutValidation(async () => {
            formSubmittedAlready = window.widgetReloaded ? false : formSubmittedAlready
            if (window.hasOwnProperty('powerBoardValidation')) {
                if (!powerBoardValidation.wcFormValidation()) {
                    return {
                        type: emitResponse.responseTypes.ERROR,
                        errorMessage: labels.requiredDataError
                    }
                }
            }

            if (!window.widgetReloaded && settings.selectedToken.length > 0) {
                const selectedToken = settings.tokens.find(item => item.vault_token === settings.selectedToken)
                if (!!selectedToken && selectedToken.hasOwnProperty('customer_id')) {
                    return true;
                } else {
                    if (['IN_BUILD', 'STANDALONE'].includes(settings.card3DS)) {
                        settings.charge3dsId = settings.card3DS === 'IN_BUILD' ? await inBuild3Ds(true) : await standalone3Ds()
                        if (settings.charge3dsId === false) {
                            return {
                                type: emitResponse.responseTypes.ERROR,
                                errorMessage: labels.fillDataError,
                            }
                        }

                        if ('error' === settings.charge3dsId) {
                            return {
                                type: emitResponse.responseTypes.ERROR,
                                errorMessage: labels.additionalDataRejected,
                            }
                        }

                    }
                }

                return true;
            }

            if (formSubmittedAlready) {
                return true;
            }

            let phoneValue = '';
            if (document.getElementById('shipping-phone') !== null) {
                phoneValue = document.getElementById('shipping-phone').value
            }

            if (document.getElementById('billing-phone') !== null) {
                phoneValue = document.getElementById('billing-phone').value
            }

            window.widgetPowerBoard.updateFormValues({
                email: document.getElementById('email').value,
                phone: phoneValue
            });
            window.widgetPowerBoard.trigger(window.cba.TRIGGER.SUBMIT_FORM);

            let result = false;
            window.widgetPowerBoard.on(window.cba.EVENT.FINISH, (event) => {
                result = true

                const savedCards = document.querySelector('.power-board-select-saved-cards')
                if (savedCards !== null) {
                    savedCards.style = 'display: none'
                }
            })

            const paymentSourceToken = document.querySelector('[name="payment_source_token"]')
            for (let second = 1; second <= 100; second++) {
                await sleep(100);
                if (paymentSourceToken !== null && paymentSourceToken.value.length) {
                    if (paymentSourceToken.value === 'error') {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.additionalDataRejected,
                        }
                    }
                    if (settings.paymentSourceToken.length === 0 || window.widgetReloaded) {
                        settings.paymentSourceToken = paymentSourceToken.value
                    }
                    result = true
                }

                if (result) {
                    formSubmittedAlready = true
                    break;
                }
            }

            if (result) {
                if (['IN_BUILD', 'STANDALONE'].includes(settings.card3DS)) {
                    settings.charge3dsId = settings.card3DS == 'IN_BUILD' ? await inBuild3Ds() : await standalone3Ds()

                    if (settings.charge3dsId === false) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.fillDataError + ' charge3dsId',
                        }
                    }

                    if ('error' === settings.charge3dsId) {
                        return {
                            type: emitResponse.responseTypes.ERROR,
                            errorMessage: labels.additionalDataRejected,
                        }
                    }
                }

                return true;
            }

            return {
                type: emitResponse.responseTypes.ERROR,
                errorMessage: labels.fillDataError,
            }
        });

        const unsubscribe = onPaymentSetup(async () => {
            const paymentSourceToken = document.querySelector('[name="payment_source_token"]')
            if (paymentSourceToken === null) {
                return;
            }

            settings.paymentSourceToken = paymentSourceToken.value;
            if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
                const data = {...settings}
                data.tokens = '';
                data.styles = '';
                data.supports = '';

                if(data.total_limitation){
                    delete data.total_limitation;
                }

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
            "div",
            null,
            decodeEntities(settings.description || '')
        ),
        selectSavedCardsComponent(labels.selectTokenLabel),
        createElement(
            "div",
            {id: 'powerBoardWidgetCard_wrapper'}
        ),
        createElement(
            "div",
            {id: 'powerBoardWidget3ds'}
        ),
        createElement(
            "input",
            {
                type: 'hidden',
                name: 'payment_source_token'
            }
        ),
        checkboxSavedCardsComponent(labels.saveCardLabel)
    );
};

const Paydok = {
    name: "power_board_gateway",
    label: createElement(() =>
        createElement(
            "div",
            {
                className: 'power-board-payment-method-label'
            },
            createElement("img", {
                src: `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/card.png`,
                alt: label,
                className: 'power-board-payment-method-label-icon card'
            }),
            "  " + label,
            createElement("img", {
                src: `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/commBank_logo.png`,
                alt: label,
                className: 'power-board-payment-method-label-logo'
            })
        )
    ),
    content: <Content/>,
    edit: <Content/>,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

registerPaymentMethod(Paydok);
