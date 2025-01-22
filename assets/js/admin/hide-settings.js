// noinspection PhpCSValidationInspection
// noinspection JSUnresolvedReference

jQuery( document ).ready(
	function ($) {
		const prefixes = ['woocommerce_power_board_power_board_'];

		const elementsToHide = ['CHECKOUT_VERSION'];

		prefixes.map(
			(prefix) => {
				elementsToHide.map(
					(elementToHide) => {
						const element = $( '#' + prefix + elementToHide )
						if (element.length === 0) {
							return;
						}

						element.closest( 'tr' ).hide()
					}
				)
			}
		)
	}
);
