let buttons = {};
export default (id, buttonId, data, isSandbox) => {
    const pluginWidgetName = window.widgetSettings.pluginWidgetName;
    const pluginProductionEnvironment = window.widgetSettings.pluginProductionEnvironment;
    const pluginSandboxEnvironment = window.widgetSettings.pluginSandboxEnvironment;
    const paymentSourceElement = jQuery('#paymentSourceWalletsToken');
    const paymentCompleted = jQuery('#paymentCompleted');
    const orderButton = jQuery('.wc-block-components-checkout-place-order-button');

    let config = {
        country: data.county,
    }

    if ('#pluginWalletApplePayButton' === buttonId) {
        config['wallets'] = ['apple'];
        config['amount_label'] = "Total";
    }

    if ('#pluginWalletPayPalButton' === buttonId) {
        config['pay_later'] = data.pay_later;

        config['style'] = {
            height: 55
        };
    }

    if ('#pluginWalletAfterpayButton' === buttonId) {
        jQuery('#pluginWalletAfterpayButton').each((index, element) => element.addEventListener("click", (event) => {
            data.payment = id.replace('-', '_')
            paymentSourceElement.val(JSON.stringify(data))
            orderButton.click();
        }, true))
    }

    if(buttons.current){
        delete buttons.current;
    }
    buttons.current = new window[pluginWidgetName].WalletButtons(buttonId, data.resource.data.token, config)

    buttons.current.setEnv(isSandbox ? pluginSandboxEnvironment : pluginProductionEnvironment)

    buttons.current.onPaymentSuccessful((result) => {
        result.payment = id.replace('-','_')
        paymentSourceElement.val(JSON.stringify(result))
        paymentCompleted.show();
        jQuery(buttonId).hide();
        orderButton.show();
        orderButton.click();
    })

    buttons.current.onPaymentError((data) => {
        orderButton.click();
    });

    buttons.current.onPaymentInReview((result) => {
        result.payment = id.replace('-','_')
        paymentSourceElement.val(JSON.stringify(result))
        paymentCompleted.show();
        jQuery(buttonId).hide();

        orderButton.show();
        orderButton.click();
    });

    buttons.current.load();
}
