// noinspection JSUnresolvedReference
jQuery(
		function ($) {
			let localCartTotal       = getCartTotalFromCookies();
			const cartChangeInterval = setInterval(
			() => {
				let cookieCartTotal  = getCartTotalFromCookies();
				if (localCartTotal !== cookieCartTotal) {
					clearInterval( cartChangeInterval );
					window.showWarning( $, 'Your cart has been modified. The page will reload shortly to reflect the changes.', 'notice' );
					reloadPage();
				}
				localCartTotal = cookieCartTotal
			},
			5000
			);

			beforeLeavePageListener();

			function reloadPage() {
				setTimeout(
					() => {
							location.reload()
				},
					5000
				);
			}

			function getCartTotalFromCookies() {
					let cookieArr = document.cookie.split( '; ' );

					let cookies                  = {};
					cookieArr.forEach(
				el => {
						const elementSplit       = el.split( '=' );
						cookies[elementSplit[0]] = elementSplit[1];
					}
						)

					return cookies['cart_total'];
			}

			function beforeLeavePageListener() {
				window.onbeforeunload = function () {
					clearInterval( cartChangeInterval );
				};
			}
		}
	);
