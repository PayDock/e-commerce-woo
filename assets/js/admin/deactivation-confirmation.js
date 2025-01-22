// noinspection PhpCSValidationInspection
// noinspection JSUnresolvedReference

jQuery(
	function ($) {
		$( document ).ready(
			function () {
				$( '#deactivate-power-board-for-woocommerce' ).on(
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
			}
		);
	}
)
