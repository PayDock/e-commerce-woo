import walletsForm from "../includes/wallets-form";

const pluginTextName = window.widgetSettings.pluginTextName;

walletsForm(
    'apple-pay',
    pluginTextName + ' Apple Pay',
    'pluginWalletApplePayButton',
    [
        'first_name',
        'last_name',
        'email',
        'address_1',
        'city',
        'state',
        'country',
        'postcode',
    ]
);
