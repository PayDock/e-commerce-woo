import { getSetting } from '@woocommerce/settings';
import getVaultToken from './get-vault-token';
import getStandalone3dsToken from './get-standalone-3ds-token';
import sleep from './sleep';

export default async () => {
    const settings = getSetting('power_board_data', {});

    if (settings.selectedToken.trim().length === 0) {
        settings.selectedToken = await getVaultToken()
    }

    const threeDsToken = await getStandalone3dsToken(settings.selectedToken)

    const canvas = new window.power_board.Canvas3ds('#power_boardWidget3ds', threeDsToken);
    canvas.load();

    let result = false;
    canvas.on('chargeAuthSuccess', (chargeAuthSuccessEvent) => {
        result = chargeAuthSuccessEvent.charge_3ds_id
    })

    for (let second = 1; second <= 100; second++) {
        await sleep(100);

        if (result !== false) {
            break;
        }
    }

    return result;
}