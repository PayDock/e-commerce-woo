jQuery(document).ready(function ($) {
    const types = {
        select: 'select',
        checkbox: 'checkbox',
    };
    const prefixes = [
        'woocommerce_pay_dock_pay_dock_',
        'woocommerce_pay_dock_sandbox_pay_dock_sandbox_',
    ];

    const conditions = [
        {
            element: 'Credentials_Type',
            condition: 'Credentials',
            type: types.select,
            hide: [
                'Credentials_AccessKey'
            ],
            show: [
                'Credentials_PublicKey',
                'Credentials_SecretKey'
            ],
        },
        {
            element: 'Card_DS',
            condition: 'Disable',
            type: types.select,
            hide: ['Card_DSServiceId'],
            show: [],
        },
        {
            element: 'Card_Fraud',
            condition: 'Disable',
            type: types.select,
            hide: ['Card_FraudServiceId'],
            show: [],
        },
        {
            element: 'Card_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Card_SaveCardOption'],
        },
        {
            element: 'BankAccount_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['BankAccount_SaveCardOption'],
        },
        {
            element: 'Wallets_Afterpay_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_Afterpay_SaveCardOption'],
        },
        {
            element: 'Wallets_ZipMoney_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_ZipMoney_SaveCardOption'],
        },
        {
            element: 'Wallets_PayPal_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_PayPal_SaveCardOption'],
        },
        {
            element: 'Wallets_ApplePay_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_ApplePay_FraudServiceId'],
        },
        {
            element: 'Wallets_GooglePay_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_GooglePay_FraudServiceId'],
        },
        {
            element: 'Wallets_PayPalSmartButton_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_PayPalSmartButton_FraudServiceId'],
        },
        {
            element: 'Wallets_Afterpay_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_Afterpay_FraudServiceId'],
        },
        {
            element: 'Wallets_PayPal_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_PayPal_FraudServiceId'],
        },
        {
            element: 'Wallets_Zippay_Fraud',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_Zippay_FraudServiceId'],
        },
        {
            element: 'Wallets_Zippay_SaveCard',
            condition: 'Disable',
            type: types.checkbox,
            hide: [],
            show: ['Wallets_Zippay_SaveCardOption'],
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
            console.log('#' + prefix + conditionValue.element)
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

