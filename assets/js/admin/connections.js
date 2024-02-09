jQuery(document).ready(function ($) {
    const types = {
        select: 'select', checkbox: 'checkbox',
    };
    const prefixes = [
        'woocommerce_pay_dock_pay_dock_',
        'woocommerce_pay_dock_sandbox_pay_dock_sandbox_',
        'woocommerce_pay_dock_widget_pay_dock_widget_'];

    const conditions = [{
        element: 'VERSION',
        condition: 'custom',
        type: types.select,
        hide: ['CREDENTIALS_ACCESS_KEY'],
        show: ['CUSTOM_VERSION',],
    }, {
        element: 'CREDENTIALS_TYPE',
        condition: 'CREDENTIALS',
        type: types.select,
        hide: ['CREDENTIALS_ACCESS_KEY'],
        show: ['CREDENTIALS_PUBLIC_KEY', 'CREDENTIALS_SECRET_KEY'],
    }, {
        element: 'CARD_DS',
        condition: 'DISABLE',
        type: types.select,
        hide: ['CARD_DS_SERVICE_ID', 'CARD_TYPE_EXCHANGE_OTT'],
        show: [],
    }, {
        element: 'CARD_FRAUD',
        condition: 'DISABLE',
        type: types.select,
        hide: ['CARD_FRAUD_SERVICE_ID'],
        show: [],
    }, {
        element: 'CARD_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['CARD_SAVE_CARD_OPTION'],
    }, {
        element: 'BANK_ACCOUNT_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['BANK_ACCOUNT_SAVE_CARD_OPTION'],
    }, {
        element: 'WALLETS_AFTERPAY_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_AFTERPAY_SAVE_CARD_OPTION'],
    }, {
        element: 'WALLETS_ZIPPAY_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_ZIPPAY_SAVE_CARD_OPTION'],
    }, {
        element: 'WALLETS_PAY_PAL_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_PAY_PAL_SAVE_CARD_OPTION'],
    }, {
        element: 'WALLETS_APPLE_PAY_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_APPLE_PAY_FRAUD_SERVICE_ID'],
    }, {
        element: 'WALLETS_GOOGLE_PAY_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_GOOGLE_PAY_FRAUD_SERVICE_ID'],
    }, {
        element: 'WALLETS_PAY_PAL_SMART_BUTTON_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_PAY_PAL_SMART_BUTTON_FRAUD_SERVICE_ID'],
    }, {
        element: 'WALLETS_AFTERPAY_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['WALLETS_AFTERPAY_FRAUD_SERVICE_ID'],
    }, {
        element: 'A_P_M_S_PAY_PAL_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_PAY_PAL_SAVE_CARD_OPTION'],
    }, {
        element: 'A_P_M_S_PAY_PAL_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_PAY_PAL_FRAUD_SERVICE_ID'],
    }, {
        element: 'A_P_M_S_ZIPPAY_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_ZIPPAY_FRAUD_SERVICE_ID'],
    }, {
        element: 'A_P_M_S_ZIPPAY_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_ZIPPAY_SAVE_CARD_OPTION'],
    }, {
        element: 'A_P_M_S_AFTERPAY_SAVE_CARD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_AFTERPAY_SAVE_CARD_OPTION'],
    }, {
        element: 'A_P_M_S_AFTERPAY_FRAUD',
        condition: 'DISABLE',
        type: types.checkbox,
        hide: [],
        show: ['A_P_M_S_AFTERPAY_FRAUD_SERVICE_ID'],
    },];

    function processedElement(conditionValue, elementValue, types, prefix) {
        if ((types.select === conditionValue.type && elementValue.value === conditionValue.condition) || (types.checkbox === conditionValue.type && elementValue.checked)) {
            conditionValue.show.map((showElementValue) => {
                $('#' + prefix + showElementValue).parent().parent().parent().show()
            })
            conditionValue.hide.map((showElementValue) => {
                $('#' + prefix + showElementValue).parent().parent().parent().hide()
            })
        } else {
            conditionValue.show.map((showElementValue) => {
                $('#' + prefix + showElementValue).parent().parent().parent().hide()
            })
            conditionValue.hide.map((showElementValue) => {
                $('#' + prefix + showElementValue).parent().parent().parent().show()
            })
        }
    }

    const disableSelect = 'DISABLE';

    function saveCardProcess(saveCard, _3DSFlow, _3DS) {
        if ('SESSION_VAULT' === _3DSFlow.val()) {
            saveCard.prop('checked', false).change()
            saveCard.prop('disabled', true);
        } else {
            saveCard.prop('disabled', false);
        }
    }

    function saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge) {
        if (
            (
                (disableSelect !== _3DS.val() || disableSelect !== fraud.val())
                && saveCard.prop('checked')
            )
            || (
                (disableSelect === _3DS.val() || disableSelect === fraud.val())
                && saveCard.prop('checked')
                && directCharge.prop('checked')
            )
        ) {
            saveCardOption.val('VAULT').change();
            saveCardOption.prop("disabled", true);
        } else {
            saveCardOption.prop("disabled", false);
        }
    }

    function directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge) {
        if (
            disableSelect === _3DS.val()
            && disableSelect === fraud.val()
            && saveCard.prop('checked')
            && 'VAULT' === saveCardOption.val()
        ) {
            directCharge.prop("checked", true);
            directCharge.prop("disabled", true);
        } else if (
            disableSelect === _3DS.val()
            && disableSelect === fraud.val()
            && !saveCard.prop('checked')
        ) {
            directCharge.prop("disabled", false);
        } else {
            directCharge.prop("checked", false);
            directCharge.prop("disabled", true);
        }
    }

    function _3DSFlowProcess(_3DS, _3DSFlow) {
        if ('STANDALONE' === _3DS.val()) {
            _3DSFlow.val('PERMANENT_VAULT')
            _3DSFlow.prop("disabled", true);
        } else if ('IN_BUILD' === _3DS.val()) {
            _3DSFlow.prop("disabled", false);
        }
    }

    prefixes.map((prefix) => {
        //checkoboxes
        let directCharge = $('#' + prefix + 'CARD_DIRECT_CHARGE');
        let saveCard = $('#' + prefix + 'CARD_SAVE_CARD');
        //selects
        let _3DS = $('#' + prefix + 'CARD_DS');
        let _3DSFlow = $('#' + prefix + 'CARD_TYPE_EXCHANGE_OTT');
        let fraud = $('#' + prefix + 'CARD_FRAUD');
        let saveCardOption = $('#' + prefix + 'CARD_SAVE_CARD_OPTION');

        _3DSFlowProcess(_3DS, _3DSFlow);
        saveCardProcess(saveCard, _3DSFlow, _3DS);
        directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);
        saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);

        _3DS.on('change', () => {
            saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);
            directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge)
            _3DSFlowProcess(_3DS, _3DSFlow)
        })
        _3DSFlow.on('change', () => {
            saveCardProcess(saveCard, _3DSFlow, _3DS);
        })
        fraud.on('change', () => {
            saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);
            directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge)
        })
        saveCard.on('change', () => {
            saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);
            directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge)
        })
        saveCardOption.on('change', () => {
            directChargeProcess(_3DS, fraud, saveCard, saveCardOption, directCharge)
        })
        directCharge.on('change', () => {
            saveCardOptionProcess(_3DS, fraud, saveCard, saveCardOption, directCharge);
        })

        conditions.map((conditionValue) => {
            let trackedElement = $('#' + prefix + conditionValue.element);

            trackedElement.on('change', (event) => {
                processedElement(conditionValue, event.target, types, prefix)
            });

            trackedElement.map((elementIndex, elementValue) => {
                processedElement(conditionValue, elementValue, types, prefix)
            })
        })
    })
});
