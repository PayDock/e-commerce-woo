import {getSetting} from '@woocommerce/settings';
import getVaultToken from './get-vault-token';
import sleep from './sleep';

export default async (forcePermanentVault = false) => {
    const settings = getSetting('power_board_data', {});

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

    const envVal = settings.isSandbox ? 'preproduction_cba' : 'production_cba'
    const preAuthResp = await new window.cba.Api(settings.publicKey)
        .setEnv(envVal)
        .charge()
        .preAuth(preAuthData);

    if (typeof preAuthResp._3ds.token === "undefined") {
        return false;
    }

    const canvas = new window.cba.Canvas3ds('#powerBoardWidget3ds', preAuthResp._3ds.token);
    canvas.load();

    document.getElementById('powerBoardWidgetCard_wrapper').setAttribute('style', 'display: none')

    let result = false;
    canvas.on('chargeAuth', (chargeAuthEvent) => {
        result = chargeAuthEvent.charge_3ds_id
    })
    canvas.on('additionalDataCollectReject', (chargeAuthSuccessEvent) => {
        result = 'error';
    })
    canvas.on('chargeAuthReject', function (data) {
        result = 'error';
    });

    for (let second = 1; second <= 10000; second++) {
        await sleep(100);

        if (result !== false) {
            break;
        }
    }

    return result;
}
