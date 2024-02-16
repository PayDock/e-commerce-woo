import { SelectControl } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';

export default (selectTokenLabel = 'Saved bank accounts') => {
    const settings = getSetting('paydock_bank_account_block_data', {});

    if (!settings.bankAccountSaveAccount || !settings.isUserLoggedIn || settings.tokens.length === 0) {
        return '';
    }

    const options = [{
        label: '-',
        value: ''
    }];

    settings.tokens.forEach(token => {
        if (token.type !== 'bank_account') {
            return;
        }

        const scheme = token.account_name
        const accountNumber = token.account_number.slice(-4)
        const label = `${scheme} ${accountNumber}`
        options.push({
            label: label,
            value: token.vault_token
        })
    })

    return (
        <SelectControl
            label={selectTokenLabel}
            options={options}
            onChange={(value) => {
                settings.selectedToken = value

                window.widgetBankAccount.setFormValue('account_name', '')
                window.widgetBankAccount.setFormValue('account_number', '')
                window.widgetBankAccount.setFormValue('account_routing', '')
                document.getElementById('bank_account_save').disabled = false

                if (value !== '') {
                    const token = settings.tokens.find(token => token.vault_token === value)
                    if (token !== undefined) {
                        window.widgetBankAccount.setFormValue('account_name', token.account_name)
                        window.widgetBankAccount.setFormValue('account_number', token.account_number)
                        window.widgetBankAccount.setFormValue('account_routing', token.account_routing)

                        document.getElementById('bank_account_save').disabled = true
                    }
                }

                window.widgetBankAccount.reload()
            }}
        />
    )
}