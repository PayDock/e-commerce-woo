import {getSetting} from '@woocommerce/settings';

const pluginPrefix = window.widgetSettings.pluginPrefix;

export default async () => {
    const data = {...getSetting(pluginPrefix + '_data', {})}
    data.action = 'get_vault_token';
    data._wpnonce = PluginAjax.wpnonce;
    data.tokens = '';
    data.styles = '';
    data.supports = '';

    return jQuery.post(PluginAjax.url, data).then();
}
