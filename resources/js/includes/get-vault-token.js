import {getSetting} from '@woocommerce/settings';
import {select} from '@wordpress/data';
import {CART_STORE_KEY} from '@woocommerce/block-data';

export default async () => {
    const data = {...getSetting('power_board_data', {})}
    const cart = select(CART_STORE_KEY);
    data.action = 'power_board_get_vault_token';
    data._wpnonce = PowerBoardAjax.wpnonce;
    data.tokens = '';
    data.styles = '';
    data.supports = '';
    data.amount = Number((cart.getCartTotals().total_price / 100).toFixed(3)).toFixed(2);

    return jQuery.post(PowerBoardAjax.url, data).then();
}
