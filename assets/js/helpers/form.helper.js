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
		PowerBoardAjaxError.url,
		{
			_wpnonce: PowerBoardAjaxError.wpnonce_error,
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

window.checkEmailToCreateAccount = function ( emailEl ) {
	// noinspection JSUnresolvedReference
	jQuery.post(
		PowerBoardAjaxError.url,
		{
			_wpnonce: PowerBoardAjaxError.wpnonce_check_email,
			dataType: 'html',
			action: 'power_board_check_email',
			email: emailEl.value,
		}
	).then(
		( response ) => {
			const message = response.data.message;
			const event   = new CustomEvent( 'power_board_already_used_email', { detail: { message: message !== "valid_email" ? message : null } } );
			document.dispatchEvent( event );
		}
	);
}

window.clearEmailVerification = function () {
		const event = new CustomEvent( 'power_board_already_used_email', null );
		document.dispatchEvent( event );
}

window.reloadAfterExternalCartChanges = () => {
	clearInterval( window.cartChangeInterval );
	location.reload();
}
