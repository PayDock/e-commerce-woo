const powerBoardSettings = window.wc.wcSettings.getSetting('power_board_data', {});
const powerBoardLabel = window.wp.htmlEntities.decodeEntities(powerBoardSettings.title)
    || window.wp.i18n.__('Power Board', 'power_board_gateway');

const powerBoardContent = () => {
    return wp.element.createElement(
        'div',
        null,
        wp.element.createElement(
            "div",
            null,
            window.wp.htmlEntities.decodeEntities(powerBoardSettings.description || '')),
        wp.element.createElement(
            "div", {
                id: 'powerBoardWidgetCard',
            }
        ), wp.element.createElement(
            "input", {
                type: 'hidden',
                name: 'payment_source_tokenzz'
            }
        )
    );
};

const Block_PowerBoard_Gateway = {
    name: 'power_board_gateway',
    label: powerBoardLabel,
    content: Object(window.wp.element.createElement)(powerBoardContent, null),
    edit: Object(window.wp.element.createElement)(powerBoardContent, null),
    placeOrderButtonLabel: 'Place Order by Power Board',
    canMakePayment: () => true,
    ariaLabel: powerBoardLabel,
    supports: {
        features: powerBoardSettings.supports,
    },
};

window.wc.wcBlocksRegistry.registerPaymentMethod(Block_PowerBoard_Gateway);
