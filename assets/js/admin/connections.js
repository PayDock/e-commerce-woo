jQuery(document).ready(function ($) {
    const types = {
        select: 'select', checkbox: 'checkbox'
    };
    const prefixes = [
        'woocommerce_power_board_power_board_',
        'woocommerce_power_board_sandbox_power_board_sandbox_',
        'woocommerce_power_board_widget_power_board_widget_'
    ];

    const conditions = [{
        element: 'VERSION',
        condition: 'custom',
        type: types.select,
        hide: ['CREDENTIALS_ACCESS_KEY'],
        show: ['CUSTOM_VERSION',],
    }];

    function processedElement(conditionValue, element, prefix) {
        const selectCondition = types.select === conditionValue.type && element.value === conditionValue.condition;
        const checkboxCondition = types.checkbox === conditionValue.type && element.checked;
        if (selectCondition || checkboxCondition) {
            conditionValue.show.map((id) => {
                $('#' + prefix + id).closest('tr').show()
            })
            conditionValue.hide.map((id) => {
                $('#' + prefix + id).closest('tr').hide()
            })
        } else {
            conditionValue.show.map((id) => {
                $('#' + prefix + id).closest('tr').hide()
            })
            conditionValue.hide.map((id) => {
                $('#' + prefix + id).closest('tr').show()
            })
        }
    }

    prefixes.map((prefix) => {
        if (document.getElementById(prefix + 'CREDENTIALS_ACCESS_KEY') === null) {
            return;
        }

        conditions.map((conditionValue) => {
            const trackedElement = $('#' + prefix + conditionValue.element)
            if (trackedElement.length === 0) {
                return;
            }

            processedElement(conditionValue, trackedElement[0], prefix)

            trackedElement.on('change', (event) => {
                const target = event.target
                processedElement(conditionValue, target, prefix)
            });
        })
    })
});
