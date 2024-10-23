const pluginPrefix = window.widgetSettings.pluginPrefix;
const pluginTextName = window.widgetSettings.pluginTextName;
const pluginSettings = window.wc.wcSettings.getSetting(pluginPrefix + '_data', {});
const pluginLabel = window.wp.htmlEntities.decodeEntities(pluginSettings.title)
    || window.wp.i18n.__(pluginTextName, pluginPrefix + '_gateway');

const pluginContent = () => {
    return wp.element.createElement(
        'div',
        null,
        wp.element.createElement(
            "div",
            null,
            window.wp.htmlEntities.decodeEntities(pluginSettings.description || '')),
        wp.element.createElement(
            "div", {
                id: 'pluginWidgetCard',
            }
        ), wp.element.createElement(
            "input", {
                type: 'hidden',
                name: 'payment_source_token'
            }
        )
    );
};

const Block_Plugin_Gateway = {
    name: 'plugin_gateway',
    label: pluginLabel,
    content: Object(window.wp.element.createElement)(pluginContent, null),
    edit: Object(window.wp.element.createElement)(pluginContent, null),
    placeOrderButtonLabel: 'Place Order by ' + pluginTextName,
    canMakePayment: () => true,
    ariaLabel: pluginLabel,
    supports: {
        features: pluginSettings.supports,
    },
};

window.wc.wcBlocksRegistry.registerPaymentMethod(Block_Plugin_Gateway);
