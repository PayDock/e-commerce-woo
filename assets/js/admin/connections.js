jQuery(document).ready(function ($) {
    const types = {
        select: 'select',
        checkbox: 'checkbox',
    };
    const prefixes = [
        'woocommerce_pay_dock_pay_dock_',
        'woocommerce_pay_dock_sandbox_pay_dock_sandbox_',
        'woocommerce_pay_dock_widget_pay_dock_widget_'
    ];

    const conditions = [
        {
            element: 'VERSION',
            condition: 'custom',
            type: types.select,
            hide: [
                'CREDENTIALS_ACCESS_KEY'
            ],
            show: [
                'CUSTOM_VERSION',
            ],
        },
        {
            element: 'CREDENTIALS_TYPE',
            condition: 'CREDENTIALS',
            type: types.select,
            hide: [
                'CREDENTIALS_ACCESS_KEY'
            ],
            show: [
                'CREDENTIALS_PUBLIC_KEY',
                'CREDENTIALS_SECRET_KEY'
            ],
        },
        {
            element: 'CARD_DS',
            condition: 'DISABLE',
            type: types.select,
            hide: ['CARD_DS_SERVICE_ID'],
            show: [],
        },
        {
            element: 'CARD_FRAUD',
            condition: 'DISABLE',
            type: types.select,
            hide: ['CARD_FRAUD_SERVICE_ID'],
            show: [],
        },
        {
            element: 'CARD_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['CARD_SAVE_CARD_OPTION'],
        },
        {
            element: 'BANK_ACCOUNT_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['BANK_ACCOUNT_SAVE_CARD_OPTION'],
        },
        {
            element: 'WALLETS_AFTERPAY_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_AFTERPAY_SAVE_CARD_OPTION'],
        },
        {
            element: 'WALLETS_ZIPPAY_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_ZIPPAY_SAVE_CARD_OPTION'],
        },
        {
            element: 'WALLETS_PAY_PAL_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_PAY_PAL_SAVE_CARD_OPTION'],
        },
        {
            element: 'WALLETS_APPLE_PAY_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_APPLE_PAY_FRAUD_SERVICE_ID'],
        },
        {
            element: 'WALLETS_GOOGLE_PAY_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_GOOGLE_PAY_FRAUD_SERVICE_ID'],
        },
        {
            element: 'WALLETS_PAY_PAL_SMART_BUTTON_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_PAY_PAL_SMART_BUTTON_FRAUD_SERVICE_ID'],
        },
        {
            element: 'WALLETS_AFTERPAY_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['WALLETS_AFTERPAY_FRAUD_SERVICE_ID'],
        },
        {
            element: 'A_P_M_SPAY_PAL_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SPAY_PAL_SAVE_CARD_OPTION'],
        },
        {
            element: 'A_P_M_SPAY_PAL_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SPAY_PAL_FRAUD_SERVICE_ID'],
        },
        {
            element: 'A_P_M_SZIPPAY_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SZIPPAY_FRAUD_SERVICE_ID'],
        },
        {
            element: 'A_P_M_SZIPPAY_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SZIPPAY_SAVE_CARD_OPTION'],
        },
        {
            element: 'A_P_M_SAFTERPAY_SAVE_CARD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SAFTERPAY_SAVE_CARD_OPTION'],
        },
        {
            element: 'A_P_M_SAFTERPAY_FRAUD',
            condition: 'DISABLE',
            type: types.checkbox,
            hide: [],
            show: ['A_P_M_SAFTERPAY_FRAUD_SERVICE_ID'],
        },
    ];

    function processedElement(conditionValue, elementValue, types, prefix) {
        if (
            (types.select === conditionValue.type && elementValue.value === conditionValue.condition)
            || (types.checkbox === conditionValue.type && elementValue.checked)
        ) {
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

    prefixes.map((prefix) => {
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

