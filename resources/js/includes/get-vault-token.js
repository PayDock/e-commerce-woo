import {getSetting} from '@woocommerce/settings';

export default async () => {
    const data = {...getSetting('power_board_data', {})}
    data.action = 'get_vault_token';
    data._wpnonce = PowerBoardAjax.wpnonce;
    data.tokens = '';
    data.styles = '';
    data.supports = '';

    return jQuery.post(PowerBoardAjax.url, data).then();
}
