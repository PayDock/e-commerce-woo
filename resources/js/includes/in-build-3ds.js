import {getSetting} from '@woocommerce/settings';
import getVaultToken from './get-vault-token';
import sleep from './sleep';
import {select} from '@wordpress/data';
import {CART_STORE_KEY} from '@woocommerce/block-data';

export default async (forcePermanentVault = false, newAmount = null) => {
    const settings = getSetting('paydock_data', {});

    if (settings.selectedToken.trim().length === 0 && settings.card3DSFlow === 'PERMANENT_VAULT') {
        settings.selectedToken = await getVaultToken()
    }

    const cart = select(CART_STORE_KEY);
    const billingAddress = cart.getCustomerData().billingAddress;
    const shippingAddress = cart.getCustomerData().shippingAddress;

    const preAuthData = {
        amount: newAmount || settings.amount,
        currency: settings.currency,
        customer: {
            first_name: billingAddress.first_name,
            last_name: billingAddress.last_name,
            email: billingAddress.email,
            payment_source: {
                address_country: billingAddress.country,
                address_state: billingAddress.state,
                address_city: billingAddress.city,
                address_postcode: billingAddress.postcode,
                address_line1: billingAddress.address_1,
            }
        },
        shipping: {
            address_country: shippingAddress.country,
            address_state: shippingAddress.state,
            address_city: shippingAddress.city,
            address_postcode: shippingAddress.postcode,
            address_line1: shippingAddress.address_1,
            contact: {
                first_name: shippingAddress.first_name,
                last_name: shippingAddress.last_name,
                email: shippingAddress.email ?? billingAddress.email,
            }
        }
    };
    if (billingAddress.address_2) {
        preAuthData.customer.payment_source.address_line2 = billingAddress.address_2;
    }
    if (shippingAddress.address_2) {
        preAuthData.shipping.address_line2 = shippingAddress.address_2;
    }
    if (billingAddress.phone) {
        preAuthData.shipping.contact.phone = billingAddress.phone;
        preAuthData.customer.phone = billingAddress.phone;
    }
    if (shippingAddress.phone) {
        preAuthData.shipping.contact.phone = shippingAddress.phone;
    }

    if (settings.card3DSFlow === 'PERMANENT_VAULT' || forcePermanentVault) {
        preAuthData.customer.payment_source.vault_token = settings.selectedToken;
        preAuthData.customer.payment_source.gateway_id = settings.gatewayId;
    } else {
        preAuthData.token = settings.paymentSourceToken
    }

    const envVal = settings.isSandbox ? 'sandbox' : 'production'
    const preAuthResp = await new window.paydock.Api(settings.publicKey)
        .setEnv(envVal)
        .charge()
        .preAuth(preAuthData);

    if (typeof preAuthResp._3ds.token === "undefined") {
        reloadWidget();
        const paymentSourceToken = document.querySelector('[name="paydock_payment_source_token"]');
        paymentSourceToken.value = null;
        return false;
    }

    document.getElementById('paydockWidget3ds').innerHTML = '';
    document.getElementById('paydockWidget3ds').setAttribute('style', '')

    const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', preAuthResp._3ds.token);
    canvas.load();

    document.getElementById('paydockWidgetCard_wrapper').setAttribute('style', 'display: none')

    let result = false;
    canvas.on('chargeAuthSuccess', (chargeAuthEvent) => {
        result = chargeAuthEvent.charge_3ds_id
    })
    canvas.on('additionalDataCollectReject', () => {
        result = 'error';
    })
    canvas.on('chargeAuthReject', function (data) {
        if (data.status === 'not_authenticated') {
            showCardWidget();
        }
        result = data.charge_3ds_id
    });

    for (let second = 1; second <= 10000; second++) {
        await sleep(100);

        if (result !== false) {
            break;
        }
    }

    if (result === 'error') {
        showCardWidget();
        reloadWidget();
    } else {
        listenForWidgetErrors();
    }

    return result;
}
function reloadWidget() {
    const savedCards = document.querySelector('.paydock-select-saved-cards');
    if (savedCards !== null) {
        savedCards.style = 'display: block';
    }
    const settings = window.wc.wcSettings.getSetting('paydock_data', {});
    settings.selectedToken = '';
    const paymentSourceToken = document.querySelector('[name="payment_source_token"]');
    paymentSourceToken.value = null;
    window.widgetPaydock.reload();
    window.widgetReloaded = true;
}

function showCardWidget() {
    document.getElementById('paydockWidgetCard_wrapper').setAttribute('style', '');
    const canvas3dsWrapper = document.getElementById('paydockWidget3ds');
    canvas3dsWrapper.innerHTML = '';
    canvas3dsWrapper.setAttribute('style', 'display: none');
}

function listenForWidgetErrors() {
    if (!!window.widgetErrorInterval) clearInterval(window.widgetErrorInterval);
    let counter = 0;
    window.widgetErrorInterval = setInterval(() => {
        const errorBanner = document.querySelectorAll('.wc-block-components-notice-banner.is-error')[0];
        const bannerContent = errorBanner?.querySelectorAll('.wc-block-components-notice-banner__content')[0];
        if (bannerContent?.innerText.indexOf('widget_error') > -1) {
            showCardWidget();
            reloadWidget();
            bannerContent.innerText = bannerContent?.innerText.replace('widget_error', '')
            clearInterval(window.widgetErrorInterval);
        } else if (counter === 300) {
            clearInterval(window.widgetErrorInterval);
        } else {
            counter++;
        }
    }, 100)
}
