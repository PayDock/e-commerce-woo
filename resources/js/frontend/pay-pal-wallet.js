import walletsForm from "../includes/wallets-form";

const pluginTextName = window.widgetSettings.pluginTextName;

walletsForm(
    'pay-pal',
  pluginTextName + ' PayPal',
    'pluginWalletPayPalButton',
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

