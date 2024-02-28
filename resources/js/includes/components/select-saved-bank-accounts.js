import Select from 'react-select'
import { getSetting } from '@woocommerce/settings';

export default (selectTokenLabel = 'Saved bank accounts') => {
    const settings = getSetting('paydock_bank_account_block_data', {});

    if (!settings.hasOwnProperty('tokens') || typeof settings.tokens !== "object") {
        return '';
    }

    const tokens = settings.tokens.filter(token => token.type === 'bank_account')
    if (!settings.bankAccountSaveAccount || !settings.isUserLoggedIn || tokens.length === 0) {
        return '';
    }

    const options = [{
        label: '-',
        value: ''
    }];

    tokens.forEach(token => {
        const scheme = token.account_name
        const accountNumber = token.account_number.slice(-4)
        const label = `${scheme} ${accountNumber}`
        options.push({
            label: label,
            value: token.vault_token
        })
    })

    return (
        <div>
            <label style={{
                fontSize: '1rem',
                fontWeight: 'bold',
                lineHeight: 2,
            }} htmlFor="select-saved-cards">
                {selectTokenLabel}
            </label>
            <Select
                inputId="select-saved-cards"
                label={selectTokenLabel}
                styles={
                    {
                        control: styles => ({ ...styles, marginBottom: '20px' })
                    }
                }
                options={options}
                onChange={(option) => {
                    const value = option.value
                    settings.selectedToken = value

                    window.widgetBankAccount.updateFormValues({
                        account_name: '',
                        account_number: '',
                        account_routing: ''
                    });

                    document.getElementById('bank_account_save').disabled = false
    
                    if (value !== '') {
                        const token = settings.tokens.find(token => token.vault_token === value)
                        if (token !== undefined) {
                            window.widgetBankAccount.updateFormValues({
                                account_name: token.account_name,
                                account_number: token.account_number,
                                account_routing: token.account_routing
                            });
    
                            document.getElementById('bank_account_save').disabled = true
                        }
                    }
                }}
            />
        </div>
    )
}