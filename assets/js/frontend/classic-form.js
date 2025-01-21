const isOrderPayPage = window.location.href.includes( 'order-pay' );

jQuery(
	function ($) {
		$( document ).ready(
			() => {
				const CONFIG                      = {
					phoneInputIds: {
						shipping: '#shipping_phone',
						billing: '#billing_phone',
					},
					baseCheckboxIdName: 'payment_method',
					errorMessageClassName: 'wc-block-components-validation-error',
					paymentOptionsNames: [
					'power_board_gateway',
					],
					phonePattern: /^\+[1-9]{1}[0-9]{3,14}$/,
					errorMessageHtml: `<div class ="wc-block-components-validation-error" role="alert"><p>Please enter your phone number in international format, starting with "+"</p></div>`,
				};
				const $shippingWrapper            = $( '#shipping-fields .wc-block-components-address-address-wrapper' );
				const getPhoneInputs              = () =>
				Object.entries( CONFIG.phoneInputIds )
				.reduce(
					(acc, [key, selector]) => {
						const $input              = $( selector );
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
				.map( name => $( `#${CONFIG.baseCheckboxIdName}_${name}` ).parents().eq( 1 ) )
				.filter( $component => $component.length );
			const validatePhone               = ($input) => {
				const phone                   = $input.val();
				$input.next( `.${CONFIG.errorMessageClassName}` ).remove();
				if (phone && !CONFIG.phonePattern.test( phone )) {
					$input.after( CONFIG.errorMessageHtml );

						return false;
				}
					return true;
				};
				const updateVisibility      = (phoneInputs) => {
					const validationResults = window.getValidationResults( phoneInputs, validatePhone );
					const allValid          = Object.values( validationResults ).every( Boolean );
					const shippingValid     = validationResults.shipping;
					if (!shippingValid) {
						// noinspection JSUnresolvedReference
						$shippingWrapper.addClass( 'is-editing' );
					}
					$( 'button#place_order' ).styles = 'visibility:' + (allValid ? 'visible' : 'hidden');
					getPaymentOptionsComponents().forEach(
						$component => {
							$component.css(
								{
									opacity: allValid ? 1 : 0.5,
									pointerEvents: allValid ? 'auto' : 'none',
							}
								)
							}
					);
				};
				const initPhoneNumberValidation = () => {
					const phoneInputs           = getPhoneInputs();
					if (!Object.keys( phoneInputs ).length) {
						return;
					}
					Object.values( phoneInputs ).forEach( input => input.on( 'blur input', () => updateVisibility( phoneInputs ) ) );
					updateVisibility( phoneInputs );
				};
				initPhoneNumberValidation();
				const powerBoardHelper         = {
					paymentMethod: null,
					form: null,
					formChangedTimer: null,
					lastAddressVerified: null,
					showErrorMessage( errorMessage ) {
						$( '.woocommerce-notices-wrapper:first' ).html( "" );
						$( '.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout' ).html( "" );
						jQuery.post(
						PowerBoardAjax.url,
						{
							_wpnonce: PowerBoardAjax.wpnonce_error,
							dataType: 'html',
							action: 'power_board_create_error_notice',
							error: errorMessage,
						}
						).then(
						message => {
							const doc          = new DOMParser().parseFromString( message.toString(), "text/html" );
							const noticeBanner = doc.querySelectorAll( "div.wc-block-components-notice-banner" )[0];
							$( '.woocommerce-notices-wrapper:first' ).append( noticeBanner )
							$( 'html, body' ).animate(
							{
								scrollTop: ($( 'div.woocommerce-notices-wrapper' ).offset().top - 100)
							},
							800
							)
						}
						);
					},
					handleWidgetError() {
						let loading = $( '#loading' );
						this.toggleWidgetVisibility( true );
						loading.show();
						this.initMasterWidget();

						const noticesWrapper      = $( 'div.woocommerce-notices-wrapper' )[0];
						const removeErrorInterval = setInterval(
						() => {
							if (noticesWrapper?.children.length > 0) {
								clearInterval( removeErrorInterval );
								const removeErrorTimeout = setTimeout(
								() => {
									clearTimeout( removeErrorTimeout );
									for (let notice of noticesWrapper.children) {
										if (notice.classList.contains( 'is-error' )) {
											notice.remove();
										}
									}
								},
									10000
									);
							}
						},
							200
							);
					},
					setFieldLikeInvalid( fieldName ) {
						let element = document.getElementById( `${fieldName}_field` );

						if (element) {
							element.classList.add( "woocommerce-invalid" );
							element.classList.add( "woocommerce-invalid-required-field" )
						}
					},
					setFieldLikeValid( fieldName ) {
						let element = document.getElementById( `${fieldName}_field` );

						if (element) {
							element.classList.remove( "woocommerce-invalid" );
							element.classList.remove( "woocommerce-invalid-required-field" )
						}
					},
					getFieldsList( ignoreCheckbox = false ) {
						const fieldsNames    = [
						'first_name',
						'last_name',
						'country',
						'address_1',
						'address_2',
						'city',
						'state',
						'postcode',
						'email',
						'phone'
						];
						let result           = [];
						let shippingCheckbox = $( '[name="ship_to_different_address"]' )
						let prefixes         = ['billing_']

						if (shippingCheckbox?.[0]?.checked || ignoreCheckbox) {
							prefixes.push( 'shipping_' )
						}

						prefixes.map(
							(prefix) => {
								fieldsNames.map(
								(field) => {
									if ('shipping_' === prefix && ['email', 'phone'].includes( field )) {
										return;
									}
									if ('billing_' === prefix && ['phone'].includes( field )) {
										return;
									}
									result.push( `${prefix}${field}` )
								}
								)
							}
						)

						return result;
					},
					isValidForm( paymentMethod ) {
						if (isOrderPayPage) {
							return true;
						}
						this.hideFormValidationError( paymentMethod );
						let fieldList = this.getFieldsList();
						let result    = true

						fieldList.forEach(
							(fieldName) => {
								let element = document.querySelector( `[name="${fieldName}"]` );
								if (element) {
									if (element.value) {
										this.setFieldLikeValid( fieldName );
									} else {
										this.setFieldLikeInvalid( fieldName );
										this.showFormValidationError( paymentMethod );
										result = false;
									}
								} else if (typeof orderData !== 'undefined' && orderData[fieldName]) {
									this.setFieldLikeValid( fieldName );
								} else {
									result = false;
								}
							}
						)

						return result;
					},
					toggleWidgetVisibility( hide ) {
						let widget        = $( '#classic-powerBoardCheckout_wrapper #standaloneWidget' );
						let widgetList    = $( '#classic-powerBoardCheckout_wrapper #list' );
						let widgetSpinner = $( '#classic-powerBoardCheckout_wrapper #spinner' );

						if (hide) {
							widget?.hide();
							widgetList?.hide();
							widgetSpinner?.hide();
						} else {
							widget?.show();
							widgetList?.show();
							widgetSpinner?.show();
						}
					},
					setPaymentMethod( methodName, forceInit = false ) {
						if (!forceInit && this.paymentMethod === methodName) {
							return;
						}

						let error   = $( '#fields-validation-error' );
						let loading = $( '#loading' );
						loading.show();
						error.hide();

						if (!this.isValidForm( methodName )) {
							this.toggleWidgetVisibility( true );
							loading.hide();
							error.show();
							return;
						}

						if (methodName !== 'power_board_gateway' ) {
							this.toggleWidgetVisibility( true );
							loading.show();
							error.hide();
						}

						let orderButton = $( 'button#place_order' )

						orderButton.hide();
						this.paymentMethod = methodName;

						switch (this.paymentMethod) {
							case 'power_board_gateway':
								this.initMasterWidget();
								orderButton.hide();
								break;
							default:
								window.widgetPowerBoard = null;
								orderButton.show();
						}
					},
					showFormValidationError( paymentMethod ) {
						if (paymentMethod) {
							$( `#classic-${paymentMethod}` ).hide();
							$( `#classic-${paymentMethod}-error` ).show();
						}
					},
					hideFormValidationError( paymentMethod ) {
						if (paymentMethod) {
							$( `#classic-${paymentMethod}` ).show();
							$( `#classic-${paymentMethod}-error` ).hide();
						}
					},
					initMasterWidget() {
						const data = {
							_wpnonce: PowerBoardAjax.wpnonce,
							address: this.getAddressData( false ).address,
						};

						if (isOrderPayPage) {
							data.order_id = orderData.order_id;
							data.total    = {
								total_price: orderData.total_price,
								total_tax: orderData.total_tax,
								currency_code: orderData.currency_code,
								currency_symbol: orderData.currency_symbol,
							};
						}

						jQuery.ajax(
							{
								url: '/?wc-ajax=power-board-create-charge-intent',
								type: 'POST',
								data: data,
								success: (response) => {
									this.toggleWidgetVisibility( false );
									// noinspection JSUnresolvedReference
									window.widgetPowerBoard = new cba.Checkout( '#classic-powerBoardCheckout_wrapper', response.data.resource.data.token );
									// noinspection JSUnresolvedReference
									window.widgetPowerBoard.setEnv( this.getConfigs().environment )
									const showError         = (message) => this.showErrorMessage( message );
									const handleWidgetError = () => this.handleWidgetError();
									const submitForm        = () => this.form.submit();

									window.widgetPowerBoard.onPaymentSuccessful(
										function (data) {
											// noinspection JSUnresolvedReference
											jQuery( '#chargeid' ).val( data['charge_id'] );
											submitForm();

											window.widgetPowerBoard = null;
										}
									);

									window.widgetPowerBoard.onPaymentFailure(
										function () {
											showError( 'Transaction failed. Please check your payment details or contact your bank' );

											handleWidgetError();
											window.widgetPowerBoard = null;
										}
									);

									window.widgetPowerBoard.onPaymentExpired(
										function () {
											showError( 'Your payment session has expired. Please retry your payment' );

											handleWidgetError();
											window.widgetPowerBoard = null;
										}
									);
								}
							}
						);
					},
					getAddressData( returnJson = true ) {
						if (isOrderPayPage && typeof orderData !== 'undefined') {
							// noinspection JSUnresolvedReference
							let result = {
								shipping_address: {
									first_name: orderData.shipping_first_name,
									last_name: orderData.shipping_last_name,
									address_1: orderData.shipping_address_1,
									address_2: orderData.shipping_address_2,
									city: orderData.shipping_city,
									state: orderData.shipping_state,
									postcode: orderData.shipping_postcode,
									country: orderData.shipping_country,
								},
								address: {
									first_name: orderData.billing_first_name,
									last_name: orderData.billing_last_name,
									address_1: orderData.billing_address_1,
									address_2: orderData.billing_address_2,
									city: orderData.billing_city,
									state: orderData.billing_state,
									postcode: orderData.billing_postcode,
									country: orderData.billing_country,
									email: orderData.billing_email,
									phone: orderData.billing_phone,
								}
							};
							if (returnJson) {
								return JSON.stringify( result );
							}
							return result;
						} else {
							let fieldList = this.getFieldsList( true );

							let result = {
								shipping_address: {},
								address: {}
							};

							fieldList.forEach(
							(fieldName) => {
								let type = 'input';
								if (fieldName.includes( 'state' ) || fieldName.includes( 'country' )) {
									type = 'select'
								}
								let elements   = document.querySelectorAll( `${type}[name="${fieldName}"]` );
								let value      = elements.length > 0 ? elements[0].value : null;
								let isShipping = fieldName.includes( 'shipping' );
								if (!value && typeof orderData !== 'undefined') {
									value = orderData[fieldName] || null;
								}
								result[isShipping ? 'shipping_address' : 'address'][fieldName.replace( 'shipping_', '' ).replace( 'billing_', '' )] = value;
								}
							)

							return result;
						}
					},
					getConfigs() {
						let settings = $( `#classic-power_board_gateway-settings` ).val()
						settings     = JSON.parse( settings )

						return settings;
					},
					init() {
						const paymentMethodInterval = setInterval(
						() => {
							let paymentMethod       = $( 'input[name="payment_method"]:checked' ).val();
							if (paymentMethod && !this.paymentMethod) {
								clearInterval( paymentMethodInterval )
								this.setPaymentMethod( paymentMethod )
							}
						},
							100
							);

						this.form = $( 'form[name="checkout"]' );

						this.form.on(
							'change',
							(event) => {
								try {
									clearTimeout( this.formChangedTimer );
									this.formChangedTimer    = setTimeout(
									() => {
										const currentAddress = JSON.stringify( this.getAddressData( false ).address );
										if (this.lastAddressVerified !== currentAddress || event.target.classList.contains( 'shipping_method' )|| event.target.id.includes( 'payment_method' )) {
											this.lastAddressVerified = currentAddress;
											this.setPaymentMethod( $( 'input[name="payment_method"]:checked' ).val(), true )
										}
									},
										1000
										);
								} catch (e) {
									console.error( e )
								}
							}
						)
					},
				}

				powerBoardHelper.init()
			}
		);
	}
)
