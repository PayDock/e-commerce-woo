import { SelectControl } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';

export default (selectTokenLabel = 'Saved cards') => {
    const settings = getSetting('paydock_data', {});

    if (!settings.cardSaveCard || !settings.isUserLoggedIn || settings.tokens.length === 0) {
        return '';
    }

    const options = [{
        label: '-',
        value: ''
    }];

    settings.tokens.forEach(token => {
        if (token.type !== 'card') {
            return;
        }
        
        const cardScheme = token.card_scheme.charAt(0).toUpperCase() + token.card_scheme.slice(1)
        const expireMonth = token.expire_month < 10 ? `0${token.expire_month}` : token.expire_month;
        const label = `${cardScheme} ${token.card_number_last4} ${expireMonth}/${token.expire_year}`;
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

                window.widget.setFormValue('card_name', '')
                window.widget.setFormValue('card_number', '')
                window.widget.setFormValue('expire_month', '')
                window.widget.setFormValue('expire_year', '')
                document.getElementById('card_save_card').disabled = false

                if (value !== '') {
                    const token = settings.tokens.find(token => token.vault_token === value)
                    if (token !== undefined) {
                        if (token.card_name !== undefined) {
                            window.widget.setFormValue('card_name', token.card_name)
                        }
                        window.widget.setFormValue('card_number', `${token.card_number_last4}`)
                        window.widget.setFormValue('expire_month', `${token.expire_month}`)
                        window.widget.setFormValue('expire_year', `${token.expire_year}`)

                        document.getElementById('card_save_card').disabled = true
                    }
                }

                window.widget.reload()
            }}
        />
    )
}