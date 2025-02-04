// noinspection JSUnresolvedReference
jQuery(
		function () {
			let localCartTotal       = getCartTotalFromCookies();
			const cartChangeInterval = setInterval(
			() => {
				let cookieCartTotal  = getCartTotalFromCookies();
				if (localCartTotal !== cookieCartTotal) {
					clearInterval( cartChangeInterval );
					location.reload()
				}
				localCartTotal = cookieCartTotal
			},
			500
			);

			beforeLeavePageListener();

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
