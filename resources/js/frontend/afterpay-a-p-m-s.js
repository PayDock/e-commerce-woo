import walletsForm from "../includes/apms";

const pluginTextName = window.widgetSettings.pluginTextName;

walletsForm(
    'afterpay',
  pluginTextName + ' Afterpay',
    'pluginAPMsAfterpayButton',
    [
        'first_name',
        'last_name',
        'email',
        'address_1',
        'city',
        'state',
        'country',
        'postcode',
    ],
    ['au', 'nz', 'us', 'ca', 'uk', 'gb', 'fr', 'it', 'es', 'de']
);
