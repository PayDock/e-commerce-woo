import walletsForm from "../includes/apms";

const pluginTextName = window.widgetSettings.pluginTextName;

walletsForm(
    'zip',
  pluginTextName + ' Zip',
    'pluginAPMsZipButton',
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
    ['au', 'nz', 'us', 'ca']
);
