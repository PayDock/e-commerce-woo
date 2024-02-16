import { getSetting } from '@woocommerce/settings';
import getVaultToken from './get-vault-token';

export default async (forcePermanentVault = false) => {
    const settings = getSetting('paydock_data', {});

    if (settings.selectedToken.trim().length === 0 && settings.card3DSFlow === 'PERMANENT_VAULT') {
        settings.selectedToken = await getVaultToken()
    }

    const preAuthData = {
        amount: settings.amount,
        currency: settings.currency
    };

    if (settings.card3DSFlow === 'PERMANENT_VAULT' || forcePermanentVault) {
        preAuthData.customer = {
            payment_source: {
                vault_token: settings.selectedToken,
                gateway_id: settings.gatewayId
            }
        }
    } else {
        preAuthData.token = settings.paymentSourceToken
    }

    const envVal = settings.isSandbox ? 'sandbox' : 'production'
    const preAuthResp = await new window.paydock.Api(settings.publicKey)
        .setEnv(envVal)
        .charge()
        .preAuth(preAuthData);

    if (typeof preAuthResp._3ds.token === "undefined") {
        return false;
    }

    const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', preAuthResp._3ds.token);
    canvas.load();

    document.getElementById('paydockWidgetCard').setAttribute('style', 'display: none')

    const chargeAuthEvent = await canvas.on('chargeAuth');

    return chargeAuthEvent.charge_3ds_id;
}