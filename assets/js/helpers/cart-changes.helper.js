// noinspection JSUnresolvedReference
jQuery(
		function () {
			let localCartTotal       = getCookies()['cart_total'];
			const cartChangeInterval = setInterval(
			() => {
				let cookieCartTotal  = getCookies()['cart_total'];
				let localShippingId  = getSelectedShippingValue();
				let cookieShippingId = decodeURIComponent( getCookies()['selected_shipping'] );
				if (localCartTotal !== cookieCartTotal) {
					reloadPage();
				} else if ( localShippingId !== cookieShippingId ) {
					setTimeout(
						() => {
							localShippingId  = getSelectedShippingValue();
							cookieShippingId = decodeURIComponent( getCookies()['selected_shipping'] );
							if ( !!localShippingId && localShippingId !== cookieShippingId ) {
								reloadPage();
							}
					},
						300
						)
				}
				localCartTotal = cookieCartTotal
			},
			300
			);

			beforeLeavePageListener();

			function reloadPage() {
				clearInterval( cartChangeInterval );
				location.reload()
			}

			function getCookies() {
					let cookieArr = document.cookie.split( '; ' );

					let cookies                  = {};
					cookieArr.forEach(
				el => {
						const elementSplit       = el.split( '=' );
						cookies[elementSplit[0]] = elementSplit[1];
					}
						)

					return cookies;
			}

			function getSelectedShippingValue() {
				// noinspection JSUnresolvedReference
				const selectedShipping = jQuery( '.wc-block-components-radio-control__input:checked' ).filter(
					function () {
						// noinspection JSUnresolvedReference
						const id = jQuery( this ).attr( 'id' )
						return id.includes( 'rate' ) || id.includes( 'shipping' );
					}
					)

				return selectedShipping[0]?.value;
			}

			function beforeLeavePageListener() {
				window.onbeforeunload = function () {
					clearInterval( cartChangeInterval );
				};
			}
		}
	);
