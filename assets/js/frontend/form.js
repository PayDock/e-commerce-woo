setTimeout(
	() => jQuery(
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
				'power_board_gateway',
				],
				phonePattern: /^\+[1-9]{1}[0-9]{3,14}$/,
				errorMessageHtml: `<div class ="wc-block-components-validation-error" role="alert"><p>Please enter your phone number in international format, starting with "+"</p></div>`,
			};

			const $submitButton    = $( 'button.wc-block-components-checkout-place-order-button' );
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

			const getPaymentOptionsComponents = () =>
			CONFIG.paymentOptionsNames
				.map( name => $( `#${CONFIG.baseCheckboxIdName}-${name}` ).parents().eq( 1 ) )
				.filter( $component => $component.length );

			const validatePhone = ($input) => {
				const phone     = $input.val();
				$input.next( `.${CONFIG.errorMessageClassName}` ).remove();
				if (phone && !CONFIG.phonePattern.test( phone )) {
					$input.after( CONFIG.errorMessageHtml );

					return false;
				}

				return true;
			};

			const updateVisibility      = (phoneInputs) => {
				const validationResults = Object.entries( phoneInputs ).reduce(
				(acc, [key, $input]) => {
					acc[key]            = validatePhone( $input );
					return acc;
				},
				{}
				);

			const allValid      = Object.values( validationResults ).every( Boolean );
			const shippingValid = validationResults.shipping;

			if (!shippingValid) {
				$shippingWrapper.addClass( 'is-editing' );
			}

			$submitButton.css( 'visibility', allValid ? 'visible' : 'hidden' );

			getPaymentOptionsComponents().forEach(
				$component =>
				$component.css(
					{
						opacity: allValid ? 1 : 0.5,
						pointerEvents: allValid ? 'auto' : 'none',
					}
					)
			);
			};

			const initPhoneNumbersValidation = () => {
				const phoneInputs            = getPhoneInputs();
				if (!Object.keys( phoneInputs )?.length) {
					return;
				}

				Object.values( phoneInputs ).forEach(
				$input =>
				$input.on( 'blur input', () => updateVisibility( phoneInputs ) )
			);

			updateVisibility( phoneInputs );
			};

			initPhoneNumbersValidation();

			$( '.wc-block-checkout__use-address-for-billing input[type="checkbox"]' ).on( "change", initPhoneNumbersValidation );
		}
		);
		function setPaymentMethodWatcher() {
			$( '.wc-block-components-radio-control__input' ).on( 'change', (event) => setPaymentMethod( event.target.value ) );
		}
		function setPaymentMethod(method) {
			const $orderButton = $( '.wc-block-components-checkout-place-order-button' );
			switch (method) {
				case 'power_board_gateway':
					$orderButton.hide();
					break;
				default:
					window.widgetPowerBoard = null;
					$orderButton.show();
			}
		}
		function triggerFirstPaymentMethodChanges() {
			const $checkedInputs      = Object.values( $( '.wc-block-components-radio-control__input:checked' ) );
			const $paymentMethodInput = $checkedInputs.filter(
			inputEl => {
				return inputEl.id?.includes( 'payment-method' )
			}
				);
			if ($paymentMethodInput.length > 0) {
				setPaymentMethod( $paymentMethodInput[0].value );
				jQuery( '.wc-block-components-form' )[0].dispatchEvent( new Event( "change" ) );
			}
		}
		const firstInitInterval             = setInterval(
		() => {
			const $radioSelectPaymentMethod = $( '.wc-block-components-radio-control__input' );
			if ($radioSelectPaymentMethod !== null ) {
				clearInterval( firstInitInterval );
				triggerFirstPaymentMethodChanges();
				setPaymentMethodWatcher();
			}
		},
		200
		)
	}
	),
	1000
	)
