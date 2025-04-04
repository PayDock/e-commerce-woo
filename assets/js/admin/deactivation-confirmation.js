const pluginTextDomain = window.widgetSettings.pluginTextDomain;

// noinspection PhpCSValidationInspection
// noinspection JSUnresolvedReference

jQuery(
	function ($) {
		$( document ).ready(
			function () {
				const waitForDeactivationButton = () => {
					let attempts                = 0;
					const maxAttempts           = 10;

					const interval                   = setInterval(
						() => {
							const deactivationButton = $( '#deactivate-' + pluginTextDomain + '-for-woocommerce' );
							if (deactivationButton.length) {
								clearInterval( interval );
								addDeactivationConfirmation( deactivationButton )
							}
							if (attempts >= maxAttempts) {
								clearInterval( interval );
							}
							attempts++;
						},
						500
					);
				};

				const addDeactivationConfirmation = ( deactivationButton ) => {
					deactivationButton.on(
						'click',
						function (e) {
							e.preventDefault();

							// noinspection JSUnresolvedReference
							let urlRedirect = jQuery( this ).attr( 'href' );
							// noinspection JSUnresolvedReference
							let label = jQuery( this ).attr( 'aria-label' );

							if (confirm( 'Are you sure ' + label + ' ?' )) {
								window.location.href = urlRedirect;
							}
						}
					);
				};

				waitForDeactivationButton();
			}
		);
	}
)
