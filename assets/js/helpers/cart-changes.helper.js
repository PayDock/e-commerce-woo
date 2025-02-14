// noinspection JSUnresolvedReference
jQuery(
		function () {
			const cookies             = getCookies();
			let localCartTotal        = cookies['power_board_cart_total'];
			window.cartChangeInterval = setInterval(
			() => {
				localCartTotal        = checkCartTotal( localCartTotal, getCookies() );
			},
			300
			);

			beforeLeavePageListener();

			function checkCartTotal(localCartTotal, cookies) {
				let cookieCartTotal = cookies['power_board_cart_total'];
				if ( localCartTotal !== cookieCartTotal && cookieCartTotal !== '0') {
					const event = new CustomEvent( 'power_board_cart_total_changed', { detail: {cartTotal: cookieCartTotal, shippingId: cookies['power_board_selected_shipping'] } } );
					document.dispatchEvent( event );
				} else if (cookieCartTotal === '0') {
					clearInterval( window.cartChangeInterval );
				}
				return cookieCartTotal;
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

			function beforeLeavePageListener() {
				window.onbeforeunload = function () {
					clearInterval( window.cartChangeInterval );
				};
			}
		}
	);
