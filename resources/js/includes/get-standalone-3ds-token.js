import { getSetting } from '@woocommerce/settings';

export default async() => {
    const data = getSetting('paydock_data', {});
    data.action = 'get_vault_token';
    data.type = 'standalone-3ds-token';
    data.tokens = '';
    data.styles = '';
    data.supports = '';

    return jQuery.post(PaydockAjax.url, data).then();
}