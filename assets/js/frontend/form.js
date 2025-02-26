// noinspection PhpCSValidationInspection
// noinspection JSUnresolvedReference
jQuery(
	function ($) {
		$( document ).ready(
		function () {
			const CONFIG                      = {
				phoneInputIds: {
					shipping: '#shipping-phone',
					billing: '#billing-phone',
				},
				baseCheckboxIdName: 'radio-control-wc-payment-method-options',
				errorMessageClassName: 'wc-block-components-validation-error',
				paymentOptionsNames: [
				'power_board',
				],
				phonePattern: /^\+[1-9]{1}[0-9]{3,14}$/,
				errorMessageHtml: `<div class ="wc-block-components-validation-error" role="alert"><p>Please enter your phone number in international format, starting with "+"</p></div>`,
			};

			const $shippingWrapper = $( '#shipping-fields .wc-block-components-address-address-wrapper' );

			const getPhoneInputs     = () =>
			Object.entries( CONFIG.phoneInputIds )
				.reduce(
					(acc, [key, selector]) => {
						const $input = $( selector );
						if ($input.length) {
							acc[key] = $input;
						}
						return acc;
				},
					{}
					);

			// noinspection JSUnresolvedReference
			const getPaymentOptionsComponents = () =>
			CONFIG.paymentOptionsNames
				.map( name => $( `#${CONFIG.baseCheckboxIdName}-${name}` ).parents().eq( 1 ) )
				.filter( $component => $component.length );

			// noinspection DuplicatedCode
			const validatePhone = ($input) => {
				const phone     = $input.val();
				$input.next( `.${CONFIG.errorMessageClassName}` ).remove();
				if (phone && !CONFIG.phonePattern.test( phone )) {
					$input.after( CONFIG.errorMessageHtml );

					return false;
				}

				return true;
			};

			// noinspection DuplicatedCode
			const updateVisibility      = (phoneInputs) => {
				const validationResults = window.getValidationResults( phoneInputs, validatePhone );

				const allValid      = Object.values( validationResults ).every( Boolean );
				const shippingValid = validationResults.shipping;

				if ( !shippingValid ) {
					// noinspection JSUnresolvedReference
					$shippingWrapper.addClass( 'is-editing' );
				}

				toggleBlurPowerBoardPaymentMethod( allValid );
			};

			const initPhoneNumbersValidation = () => {
				const phoneInputs            = getPhoneInputs();
				if ( !Object.keys( phoneInputs )?.length ) {
					return;
				}

				Object.values( phoneInputs ).forEach(
				$input =>
				$input.on( 'blur input', () => updateVisibility( phoneInputs ) )
			);

			updateVisibility( phoneInputs );
			};

			const waitForShippingPhoneRender = () => {
				let attempts                 = 0;
				const maxAttempts            = 10;

				const interval                      = setInterval(
					() => {
						const $shippingPhoneElement = $( CONFIG.phoneInputIds.shipping );
						if ($shippingPhoneElement.length) {
							clearInterval( interval );
							initPhoneNumbersValidation();
						}
						attempts++;
						if (attempts >= maxAttempts) {
							clearInterval( interval );
						}
				},
					500
					);
			};

			const setInvalidEmailToCreateAccountWatcher = () => {
				document.addEventListener(
					"power_board_already_used_email",
					( event ) => {
						if (emailOrCreateAccountErrorTimeout) {
							clearTimeout( emailOrCreateAccountErrorTimeout );
						}
						emailOrCreateAccountErrorTimeout = setTimeout(
							() => {
								// noinspection JSUnresolvedReference
								const createAccountCheckbox = jQuery( '.wc-block-components-checkbox.wc-block-checkout__create-account' );
								const errorMessageEl        = document.querySelector( '.power-board-account-error-message' );
								if (createAccountCheckbox[0]) {
									if ( errorMessageEl ) {
										errorMessageEl.remove();
									}
									const message = event.detail?.message;
									if (message) {
										const powerBoardMessage = document.createElement( "p" );
										powerBoardMessage.classList.add( "power-board-account-error-message" );
										powerBoardMessage.id        = "power-board-account-error-message";
										powerBoardMessage.innerText = message;
										createAccountCheckbox.after( powerBoardMessage );
										toggleBlurPowerBoardPaymentMethod( false );
									} else {
										toggleBlurPowerBoardPaymentMethod( true );
									}
								}
							},
							300
						)
					}
				);
			}

			const toggleBlurPowerBoardPaymentMethod = ( show ) => {
				const $submitButton                 = $( 'button.wc-block-components-checkout-place-order-button' );
				// noinspection JSUnresolvedReference
				$submitButton.toggleClass( 'hidden', !show && paymentMethod !== 'power_board' );

				getPaymentOptionsComponents().forEach(
					component =>
						component.css(
							{
								opacity: show ? 1 : 0.5,
								pointerEvents: show ? 'auto' : 'none',
							}
						)
				);
			}

			waitForShippingPhoneRender();
			setInvalidEmailToCreateAccountWatcher();

			$( '.wc-block-checkout__use-address-for-billing input[type="checkbox"]' ).on( "change", initPhoneNumbersValidation );
		}
		);

		let paymentMethod                      = null;
		let emailOrCreateAccountChangedTimeout = null;
		let emailOrCreateAccountErrorTimeout   = null;
		let emailVerified                      = null;

		function watchForEmailChange() {
			// noinspection JSUnresolvedReference
			jQuery( '.wc-block-components-form' ).on(
				'change',
				event => {
					const target = event.target;
					if (
					target.id === 'email'
					|| target.parentElement.parentElement.classList.contains( 'wc-block-checkout__create-account' ) ) {
					const createAccountCheckbox = document.querySelector( '.wc-block-components-checkbox.wc-block-checkout__create-account' )?.querySelector( 'input' ).checked;
					if (emailOrCreateAccountChangedTimeout) {
						clearTimeout( emailOrCreateAccountChangedTimeout );
					}
					emailOrCreateAccountChangedTimeout = setTimeout(
						() => {
							if ( createAccountCheckbox === true ) {
								const emailEl    = document.getElementById( 'email' );
								const emailValue = emailEl.value;
								if (emailValue) {
									emailVerified = emailValue;
									window.checkEmailToCreateAccount( emailEl );
								} else if (emailVerified !== null) {
									window.clearEmailVerification();
								}
							} else if (emailVerified !== null) {
								window.clearEmailVerification();
							}
							},
						500
						);
					}
			}
				)
		}
		function setPaymentMethodWatcher() {
			const radioButtons = $( '.wc-block-components-radio-control__input' ).filter(
				function () {
					// noinspection JSUnresolvedReference
					return $( this ).attr( 'id' ).includes( 'payment-method' );
				}
				)

			radioButtons.on( 'change', (event) => setPaymentMethod( event.target.value ) );
		}
		function setPaymentMethod(method) {
			if (method !== 'power_board') {
					window.widgetPowerBoard = null;
					toggleOrderButton( false );
			} else {
				toggleOrderButton( true );
				window.initMasterWidgetCheckout();
			}

			paymentMethod = method;
		}

		function toggleOrderButton( hide ) {
			const orderButton = document.querySelector( '.wc-block-components-checkout-place-order-button' );
			if ( !orderButton ) {
				return;
			}
			orderButton.style.visibility = hide ? 'hidden' : 'visible';
		}

		function triggerFirstPaymentMethodChanges() {
			toggleOrderButton( true );
			const firstPaymentInterval        = setInterval(
				() => {
					const $checkedInput       = $( '.wc-block-components-radio-control__input:checked' );
					const $checkedInputs      = Object.values( $checkedInput );
					const $paymentMethodInput = $checkedInputs.filter(
						inputEl => inputEl.id?.includes( 'payment-method' )
						);
				if ($paymentMethodInput.length > 0) {
					clearInterval( firstPaymentInterval );
					setPaymentMethod( $paymentMethodInput[0].value );
					// noinspection JSUnresolvedReference
					jQuery( '.wc-block-components-form' )[0].dispatchEvent( new Event( "change" ) );
				}
				},
				200
			)
		}

		const firstInitInterval             = setInterval(
		() => {
			const $radioSelectPaymentMethod = $( '.wc-block-components-radio-control__input' );
			if ($radioSelectPaymentMethod) {
				clearInterval( firstInitInterval );
				triggerFirstPaymentMethodChanges();
				setPaymentMethodWatcher();
				watchForEmailChange();
			}
		},
		200
		);
	}
)
