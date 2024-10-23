import {getSetting} from '@woocommerce/settings';
import {select} from '@wordpress/data';
import {CART_STORE_KEY} from '@woocommerce/block-data';

const pluginPrefix = window.widgetSettings.pluginPrefix;

export default async () => {
    const data = {...getSetting(pluginPrefix + '_data', {})}
    const cart = select(CART_STORE_KEY);
    data.action = 'get_vault_token';
    data._wpnonce = PluginAjax.wpnonce;
    data.tokens = '';
    data.styles = '';
    data.supports = '';
    data.amount = Number((cart.getCartTotals().total_price / 100).toFixed(3)).toFixed(2);

    return jQuery.post(PluginAjax.url, data).then();
}
