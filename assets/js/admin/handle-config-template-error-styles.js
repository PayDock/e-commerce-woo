// noinspection JSUnresolvedReference
jQuery( document ).ready(
	function () {
		const element            = document.getElementById( 'woocommerce_power_board_power_board_CHECKOUT_CONFIGURATION_ID' );
		const descriptionElement = element.parentElement.querySelector( 'p.description' )

		if ( descriptionElement ) {
			descriptionElement.style.color = 'red';

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
);
