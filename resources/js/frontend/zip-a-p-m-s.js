import walletsForm from "../includes/apms";

walletsForm(
    'zip',
    'Power Board Zip',
    'powerBoardAPMsZipButton',
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
