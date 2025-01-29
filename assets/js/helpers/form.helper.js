window.getValidationResults = function (phoneInputs, validatePhone) {
	return Object.entries( phoneInputs ).reduce(
		(acc, [key, input]) => {
			acc[key] = validatePhone( input );
			return acc;
		},
		{}
	);
};

window.showWarning = function ($, message, type = 'error') {
	const classicCheckoutSelector = $( '.woocommerce-notices-wrapper:first' );
	const normalCheckoutSelector  = $( '.wc-block-components-notices:first' );
	const noticesWrapper          = classicCheckoutSelector.length > 0 ? classicCheckoutSelector : normalCheckoutSelector;
	noticesWrapper.html( '' );
	// noinspection JSUnresolvedReference
	jQuery.post(
		PowerBoardAjax.url,
		{
			_wpnonce: PowerBoardAjax.wpnonce_error,
			dataType: 'html',
			action: 'power_board_create_error_notice',
			message: message,
			type: type,
		}
	).then(
		( message ) => {
			const doc          = new DOMParser().parseFromString( message.toString(), 'text/html' );
			const noticeBanner = doc.querySelectorAll( 'div.wc-block-components-notice-banner' )[0];
			noticesWrapper.append( noticeBanner );
			$( 'html, body' ).animate(
				{
					scrollTop: noticesWrapper.offset().top - 100,
				},
				800
			);
		}
	);
}

window.toggleOrderButton = ( orderButton, hide ) => {
	if ( !orderButton) {
		return;
	}
	if (hide) {
		orderButton.classList.add( 'hide' );
	} else {
		orderButton.classList.remove( 'hide' );
	}
}
