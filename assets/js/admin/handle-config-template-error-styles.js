const configPluginPrefix           = window.widgetSettings.pluginPrefix;
const configPluginIdentifier       = configPluginPrefix + '_' + configPluginPrefix;
const checkoutTemplatesSelectBoxes = ['woocommerce_' + configPluginIdentifier + '_CHECKOUT_CONFIGURATION_ID', 'woocommerce_' + configPluginIdentifier + '_CHECKOUT_CUSTOMISATION_ID'];
// noinspection JSUnresolvedReference
jQuery( document ).ready(
	function () {
		checkoutTemplatesSelectBoxes.map(
			templateSelectBoxId => {
				const element            = document.getElementById( templateSelectBoxId );
				const descriptionElement = element.parentElement.querySelector( 'p.description' )

				if ( descriptionElement ) {
					if ( !element.classList.contains( 'is-optional' ) ) {
						descriptionElement.style.color = 'red';
					}

					element.addEventListener(
					'change',
					() => {
						if ( element.value ) {
							descriptionElement.style.display = 'none';
						}
						}
					);
				}
		}
			)
	}
);
