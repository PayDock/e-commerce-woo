import { getSetting } from '@woocommerce/settings';
import getVaultToken from './get-vault-token';
import getStandalone3dsToken from './get-standalone-3ds-token';

export default async () => {
    const settings = getSetting('paydock_data', {});
    
    if (settings.selectedToken.trim().length === 0) {
        settings.selectedToken = await getVaultToken()
    }
    
    const threeDsToken = await getStandalone3dsToken(settings.selectedToken)

    const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', threeDsToken);
    canvas.load();

    const chargeAuthSuccessEvent = await canvas.on('chargeAuthSuccess');

    return chargeAuthSuccessEvent.charge_3ds_id;
}