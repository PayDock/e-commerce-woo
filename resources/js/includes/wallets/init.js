export default (id, buttonId, data, isSandbox) => {
    const paymentSourceElement = jQuery('#paymentSourceWalletsToken');
    const paymentCompleted = jQuery('#paymentCompleted');
    const orderButton = jQuery('.wc-block-components-checkout-place-order-button');

    let config = {
        country: data.county,
    }

    if ('#powerBoardWalletApplePayButton' === buttonId) {
        config['wallets'] = ['apple'];
        config['amount_label'] = "Total";
    }

    if ('#powerBoardWalletPayPalButton' === buttonId) {
        config['pay_later'] = data.pay_later;

        config['style'] = {
            height: 55
        };
    }

    if ('#powerBoardkWalletAfterpayButton' === buttonId) {
        jQuery('#powerBoardWalletAfterpayButton').each((index, element) => element.addEventListener("click", (event) => {
            data.payment = id.replace('-', '_')
            paymentSourceElement.val(JSON.stringify(data))
            orderButton.click();
        }, true))
    }

    let button = new window.cba.WalletButtons(buttonId, data.resource.data.token, config)

    if (isSandbox) {
        button.setEnv('preproduction_cba')
    }else{
        button.setEnv('production_cba')
    }


    button.onPaymentSuccessful((result) => {
        result.payment = id.replace('-','_')
        paymentSourceElement.val(JSON.stringify(result))
        paymentCompleted.show();
        jQuery(buttonId).hide();
        orderButton.show();
        orderButton.click();
    })

    button.onPaymentError((data) => {
        orderButton.click();
    });

    button.onPaymentInReview((result) => {
        result.payment = id.replace('-','_')
        paymentSourceElement.val(JSON.stringify(result))
        paymentCompleted.show();
        jQuery(buttonId).hide();

        orderButton.show();
        orderButton.click();
    });

    button.load();
}
