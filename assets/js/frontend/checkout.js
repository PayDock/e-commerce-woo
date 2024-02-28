const power_boardSettings = window.wc.wcSettings.getSetting('power_board_data', {});
const power_boardLabel = window.wp.htmlEntities.decodeEntities(power_boardSettings.title) || window.wp.i18n.__('PowerBoard', 'power_board_gateway');

const power_boardContent = () => {
    return wp.element.createElement('div', null,
        wp.element.createElement(
            "div",
            null,
            window.wp.htmlEntities.decodeEntities(power_boardSettings.description || '')
        ), wp.element.createElement(
            "div",
            {
                id: 'power_boardWidgetCard',
            }
        ), wp.element.createElement(
            "input",
            {
                type: 'hidden',
                name: 'payment_source_tokenzz'
            }
        ));
};

const Block_PowerBoard_Gateway = {
    name: 'power_board_gateway',
    label: power_boardLabel,
    content: Object(window.wp.element.createElement)(power_boardContent, null),
    edit: Object(window.wp.element.createElement)(power_boardContent, null),
    placeOrderButtonLabel: 'Place Order by PowerBoard',
    canMakePayment: () => true,
    ariaLabel: power_boardLabel,
    supports: {
        features: power_boardSettings.supports,
    },
};

window.wc.wcBlocksRegistry.registerPaymentMethod(Block_PowerBoard_Gateway);