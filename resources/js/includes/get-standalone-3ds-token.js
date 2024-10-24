import {getSetting} from '@woocommerce/settings';
import {select} from '@wordpress/data';
import {CART_STORE_KEY} from '@woocommerce/block-data';


const pluginPrefix = window.widgetSettings.pluginPrefix;

export default async () => {
    const cart = select(CART_STORE_KEY);
    const data = {...getSetting(pluginPrefix + '_data', {})};
    data.action = 'get_vault_token';
    data.type = 'standalone-3ds-token';
    data._wpnonce = PluginAjax.wpnonce;
    data.amount = Number((cart.getCartTotals().total_price / 100).toFixed(3)).toFixed(2);

    if (document.querySelector('#shipping-first_name') !== null) {
        data.first_name = document.querySelector('#shipping-first_name').value
    }
    if (document.querySelector('#billing-first_name') !== null) {
        data.first_name = document.querySelector('#billing-first_name').value
    }

    if (document.querySelector('#shipping-last_name') !== null) {
        data.last_name = document.querySelector('#shipping-last_name').value
    }
    if (document.querySelector('#billing-last_name') !== null) {
        data.last_name = document.querySelector('#billing-last_name').value
    }

    if (document.querySelector('#shipping-phone') !== null) {
        data.phone = document.querySelector('#shipping-phone').value
    }
    if (document.querySelector('#billing-phone') !== null) {
        data.phone = document.querySelector('#billing-phone').value
    }

    if (document.querySelector('#email') !== null) {
        data.email = document.querySelector('#email').value;
    }

    data.tokens = '';
    data.styles = '';
    data.supports = '';

    return jQuery.post(PluginAjax.url, data).then();
}
