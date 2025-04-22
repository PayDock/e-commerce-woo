const classicPluginWidgetName = window.widgetSettings.pluginWidgetName;
const classicPluginPrefix     = window.widgetSettings.pluginPrefix;

// noinspection JSUnresolvedReference
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
					phonePattern: /^\+[1-9]{1}[0-9]{3,14}$/,
					errorMessageHtml: `<div class ="classic-checkout-validation-error wc-block-components-validation-error" role="alert"><p>Please enter your phone number in international format, starting with "+"</p></div>`,
				};
				const getPhoneInputs              = () =>
					Object.entries( CONFIG.phoneInputIds )
					.reduce(
						( acc, [key, selector] ) => {
							const $input          = $( selector );
							if ( $input.length ) {
								acc[key] = $input;
							}
							return acc;
						},
						{}
					);
				// noinspection DuplicatedCode
				const validatePhone = ( $input ) => {
					// noinspection JSUnresolvedReference
					const phone = $input.val();
					$input.next( `.${CONFIG.errorMessageClassName}` ).remove();
					if (phone && !CONFIG.phonePattern.test( phone )) {
						$input.after( CONFIG.errorMessageHtml );
						// noinspection JSUnresolvedReference
						$input.addClass( 'woo-plugin-invalid-phone' );
						return false;
					}
					// noinspection JSUnresolvedReference
					$input.removeClass( 'woo-plugin-invalid-phone' );
					return true;
				};
				const initPhoneNumberValidation = () => {
					const phoneInputs           = getPhoneInputs();
					if ( !Object.keys( phoneInputs ).length ) {
						return;
					}
					Object.values( phoneInputs ).forEach( input => input.on( 'blur input', () => window.getValidationResults( phoneInputs, validatePhone ) ) );
					window.getValidationResults( phoneInputs, validatePhone );
				};
				const wooPluginHelper = {
					invalidPostcode: false,
					paymentMethodLoaded: null,
					selectedPaymentMethod: null,
					form: null,
					formChangedTimer: null,
					lastAddressVerified: null,
					totalChangesTimeout: null,
					totalChangesSecondTimeout: null,
					shippingChangedTimeout: null,
					lastMasterWidgetInit: null,
					currentSavedShipping: null,
					showErrorMessage( errorMessage ) {
						window.showWarning( errorMessage, 'error' );
					},
					reInitMasterWidget() {
						let loading   = $( '#loading' );
						this.toggleWidgetVisibility( true );
						loading.show();
						this.initMasterWidget();
					},
					handleWidgetError() {
						this.reInitMasterWidget();

						const noticesWrapper      = $( 'div.woocommerce-notices-wrapper' )[0];
						const removeErrorInterval = setInterval(
							() => {
								if ( noticesWrapper?.children.length > 0 ) {
									clearInterval( removeErrorInterval );
									const removeErrorTimeout = setTimeout(
										() => {
											clearTimeout( removeErrorTimeout );
											for ( let notice of noticesWrapper.children ) {
												if ( notice.classList.contains( 'is-error' ) ) {
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
					getFieldsList( ignoreCheckbox = false, completeList = false ) {
						const fieldsNames = [
							'first_name',
							'last_name',
							'country',
							'address_1',
							'address_2',
							'city',
							'state',
							'postcode',
							'email',
							'phone',
						];

						if (completeList) {
							fieldsNames.push( 'company' );
						}
						let result           = [];
						let shippingCheckbox = $( '[name="ship_to_different_address"]' )
						let prefixes         = ['billing_']
						if ( shippingCheckbox?.[0]?.checked || ignoreCheckbox ) {
							prefixes.push( 'shipping_' )
						}
						prefixes.map(
							( prefix ) => {
								fieldsNames.map(
									( field ) => {
										if ( !completeList ) {
											if ('shipping_' === prefix && ['email', 'phone'].includes( field )) {
												return;
											}
											if ('billing_' === prefix && ['phone'].includes( field )) {
												return;
											}
										}
										result.push( `${prefix}${field}` )
									}
								)
							}
						)
						return result;
					},
					isValidForm( paymentMethod ) {
						this.hideFormValidationError( paymentMethod );
						let fieldList                 = this.getFieldsList();
						let result                    = true
						const additionalTermsCheckbox = document.getElementById( '_woo_additional_terms' );
						if ( this.invalidPostcode || ( additionalTermsCheckbox && !additionalTermsCheckbox.checked ) ) {
							result = false;
						}
						fieldList.filter( field => !field.includes( 'address_2' ) ).forEach(
							( fieldName ) => {
								let element = document.querySelector( `[name="${fieldName}"]` );
								if (element) {
									if (element.value) {
										this.setFieldLikeValid( fieldName );
									} else {
										this.setFieldLikeInvalid( fieldName );
										this.showFormValidationError( paymentMethod );
										result = false;
									}
								} else {
									result = false;
								}
							}
						)

					if ( result ) {
						// noinspection JSUnresolvedReference
						let isPhoneNumberValid = this.isBillingPhoneValid();

						// noinspection JSUnresolvedReference
						let useSameBillingAndShipping = jQuery( '#ship-to-different-address-checkbox' ).checked;
						if ( !useSameBillingAndShipping ) {
							isPhoneNumberValid = isPhoneNumberValid && this.isShippingPhoneValid();
						}

						return isPhoneNumberValid;
					}

					return result;
					},
					isShippingPhoneValid() {
						return !document.getElementById( 'shipping_phone' )?.classList?.contains( 'woo-plugin-invalid-phone' );
					},
					isBillingPhoneValid() {
						return !document.getElementById( 'billing_phone' )?.classList?.contains( 'woo-plugin-invalid-phone' );
					},
					toggleWidgetVisibility( hide ) {
						let widget        = $( '#classic-wooPluginCheckout_wrapper #standaloneWidget' );
						let widgetList    = $( '#classic-wooPluginCheckout_wrapper #list' );
						let widgetSpinner = $( '#classic-wooPluginCheckout_wrapper #spinner' );
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
						if ( !forceInit && this.paymentMethodLoaded === methodName ) {
							return;
						}
						this.selectedPaymentMethod = methodName;
						let error                  = $( '#fields-validation-error' );
						let createIntentError      = $( '#intent-creation-error' );
						let loading                = $( '#loading' );
						loading.show();
						error.hide();
						createIntentError.hide();

						if ( !this.isValidForm( methodName ) && methodName === classicPluginPrefix ) {
							this.toggleWidgetVisibility( true );
							this.toggleOrderButton( true );
							loading.hide();
							error.show();
							return;
						}
						if (methodName !== classicPluginPrefix ) {
							this.toggleWidgetVisibility( true );
							loading.show();
							error.hide();
						}
						this.paymentMethodLoaded = methodName;
						switch (this.paymentMethodLoaded) {
							case classicPluginPrefix:
								this.toggleOrderButton( true );
								this.toggleWidgetVisibility( true );
								this.initMasterWidget();
								break;
							default:
								window.widgetWooPlugin = null;
								this.toggleOrderButton( false );
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
						const initTimestamp       = ( new Date() ).getTime();
						this.lastMasterWidgetInit = initTimestamp;
						setTimeout( () => this.toggleOrderButton( true ), 100 );
						let addressData     = this.getAddressData( false );
						let billingAddress  = addressData.address;
						let shippingAddress = billingAddress;
						if ( document.getElementById( 'ship-to-different-address-checkbox' ).checked ) {
							shippingAddress = addressData.shipping_address;
						}

						// noinspection JSUnresolvedReference
						const data = {
							_wpnonce: WooPluginAjaxCheckout.wpnonce_intent,
							address: billingAddress,
							shipping_address: shippingAddress,
						};
						// noinspection JSUnresolvedReference
						jQuery.ajax(
							{
								url: '/?wc-ajax=woo-plugin-create-charge-intent',
								type: 'POST',
								data: data,
								success: ( response ) => {
									if ( !this.isValidForm( classicPluginPrefix ) ) {
										let error   = $( '#fields-validation-error' );
										let loading = $( '#loading' );
										this.toggleWidgetVisibility( true );
										this.toggleOrderButton( true );
										loading.hide();
										error.show();
									} else {
										if (initTimestamp === this.lastMasterWidgetInit) {
											if (response.success) {
												// noinspection JSUnresolvedReference
												this.toggleWidgetVisibility( false );
												// noinspection JSUnresolvedReference
												window.widgetWooPlugin = new window[classicPluginWidgetName].Checkout( '#classic-wooPluginCheckout_wrapper', response.data.token );
												// noinspection JSUnresolvedReference
												window.widgetWooPlugin.setEnv( this.getConfigs().environment )
												const showError          = ( message ) => this.showErrorMessage( message );
												const handleWidgetError  = () => this.handleWidgetError();
												const reInitMasterWidget = () => this.reInitMasterWidget();
												const submitForm         = () => this.form.submit();
												const intentId           = response.data.intentId;
												// noinspection JSUnresolvedReference
												window.widgetWooPlugin.onPaymentSuccessful(
													function ( data ) {
														// noinspection JSUnresolvedReference
														jQuery.ajax(
															{
																url: '/?wc-ajax=woo-plugin-process-payment-result',
																method: 'POST',
																data: {
																	_wpnonce: WooPluginAjaxCheckout.wpnonce_process_payment,
																	payment_response: data,
																	create_account: document.getElementById( 'createaccount' )?.checked,
																},
																success: function (response) {
																	if (response.success) {
																		// noinspection JSUnresolvedReference
																		jQuery( '#chargeid' ).val( data['charge_id'] );
																		// noinspection JSUnresolvedReference
																		jQuery( '#intentid' ).val( intentId );
																		submitForm();

																		window.widgetWooPlugin = null;
																	} else {
																		showError( response.data.message );
																		reInitMasterWidget();
																	}
																}
															}
														);
													}
												);
												// noinspection JSUnresolvedReference
												window.widgetWooPlugin.onPaymentFailure(
													function ( data ) {
														// noinspection JSUnresolvedReference
														jQuery.ajax(
															{
																url: '/?wc-ajax=woo-plugin-process-payment-result',
																method: 'POST',
																data: {
																	_wpnonce: WooPluginAjaxCheckout.wpnonce_process_payment,
																	payment_response:
																		{
																			...data,
																			errorMessage: data.message || 'Transaction failed',
																	}
																},
																success: function () {
																	showError( 'Transaction failed. Please check your payment details or contact your bank' );
																	handleWidgetError();
																	window.widgetWooPlugin = null;
																}
															}
														);
													}
												);
												// noinspection JSUnresolvedReference
												window.widgetWooPlugin.onPaymentExpired(
													function () {
														showError( 'Your payment session has expired. Please retry your payment' );

														handleWidgetError();
														window.widgetWooPlugin = null;
													}
												);
											} else {
												// noinspection JSUnresolvedReference
												let error = jQuery( '#intent-creation-error' );
												// noinspection JSUnresolvedReference
												let loading = jQuery( '#loading' );
												this.toggleWidgetVisibility( true );
												this.toggleOrderButton( true );
												loading.hide();
												error.show();
											}
										}
									}
								}
							}
						);
					},
					getAddressData( returnJson = true ) {
						let fieldList    = this.getFieldsList( true, true );
						let result       = {
							shipping_address: {},
							address: {}
						};
						fieldList.forEach(
							( fieldName ) => {
								let type = 'input';
								if ( fieldName.includes( 'state' ) || fieldName.includes( 'country' ) ) {
									type = 'select'

									if ( fieldName.includes( 'country' ) && document.querySelectorAll( `select[name="${fieldName}"]` ).length === 0 ) {
										type = 'input'
									}
								}
								let elements   = document.querySelectorAll( `${type}[name="${fieldName}"]` );
								let value      = elements.length > 0 ? elements[0].value : null;
								let isShipping = fieldName.includes( 'shipping' );
								result[isShipping ? 'shipping_address' : 'address'][fieldName.replace( 'shipping_', '' ).replace( 'billing_', '' )] = value;
							}
						);

					if ( returnJson ) {
						return JSON.stringify( result );
					}
						return result;
					},
					getConfigs() {
						// noinspection JSUnresolvedReference
						let settings              = $( `#classic-${classicPluginPrefix}-settings` ).val()
						settings                  = JSON.parse( settings )
						return settings;
					},
					toggleOrderButton( hide ) {
						setTimeout(
							() => {
								const orderButton = document.querySelector( 'button#place_order' );
								if ( orderButton ) {
									orderButton.style.visibility = hide ? 'hidden' : 'visible';
								}
							},
							100
						);
					},
					handleCartTotalChanged( event ) {
						if (this.totalChangesTimeout) {
							clearTimeout( this.totalChangesTimeout );
						}
						if (this.totalChangesSecondTimeout) {
							clearTimeout( this.totalChangesSecondTimeout );
						}
						this.toggleWidgetVisibility( true );

						this.totalChangesTimeout = setTimeout(
							() => {
								const orderTotal = this.getUIOrderTotal();
								const cartTotal  = +event.detail.cartTotal;
								if (orderTotal) {
									const address = this.getAddressData( true );
									if (orderTotal !== cartTotal && this.lastAddressVerified === address) {
										if (this.totalChangesSecondTimeout) {
											clearTimeout( this.totalChangesSecondTimeout );
										}
										this.totalChangesSecondTimeout = setTimeout(
											() => {
												const orderTotal       = this.getUIOrderTotal();
												if (orderTotal) {
													if (orderTotal !== cartTotal) {
														if (this.currentSavedShipping === event.detail.shippingId) {
															window.reloadAfterExternalCartChanges();
														} else {
															// noinspection JSUnresolvedReference
															$( document.body ).trigger( 'update_checkout' );
														}
													}

													this.initMasterWidget();
												}
											},
											300
										)
									} else {
										this.initMasterWidget();
									}
								}
								this.currentSavedShipping = event.detail.shippingId;
						},
							300
						)
					},
					getUIOrderTotal() {
						const orderTotal = $( '.order-total' )[0];
						return orderTotal ? +orderTotal.innerText.replace( /[^0-9.,]*/, '' ) : null;
					},
					init() {
						const paymentMethodInterval = setInterval(
							() => {
								// noinspection JSUnresolvedReference
								const paymentMethod = $( 'input[name="payment_method"]:checked' ).val();
								if ( paymentMethod && ! this.paymentMethodLoaded ) {
									this.lastAddressVerified = this.getAddressData( true );
									clearInterval( paymentMethodInterval );
									this.checkIsValidPostCodeAndLoadPayment( false );
								}
							},
							100
						);

						this.form = $( 'form[name="checkout"]' );
						this.form.on(
							'change',
							( event ) => {
								try {
									if ( event.target.id.includes( 'chargeid' ) || event.target.id.includes( 'intentid' ) ) {
										return
									}
									this.handleShippingChanged( event.target.id );
									this.handleFormChanged( event.target );
								} catch ( e ) {
									console.error( e );
								}
							}
						);

						document.addEventListener( classicPluginPrefix + "_cart_total_changed", this.handleCartTotalChanged.bind( this ) );
					},
					handleShippingChanged( eventTargetId ) {
						if (this.shippingChangedTimeout) {
							clearTimeout( this.shippingChangedTimeout );
						}
						if ( eventTargetId.includes( 'shipping_method' ) ) {
							if (this.currentSavedShipping !== eventTargetId.value) {
								this.shippingChangedTimeout = setTimeout(
									() => {
										// noinspection JSUnresolvedReference
										jQuery.ajax(
											{
												url: '/?wc-ajax=woo-plugin-update-shipping',
												type: 'POST',
												data: {
													_wpnonce: WooPluginAjaxCheckout.wpnonce_update_shipping,
												}
											}
										);
									},
									500
								);
							}
						}
					},
					handleFormChanged( eventTarget ) {
						if (this.formChangedTimer) {
							clearTimeout( this.formChangedTimer );
						}
						this.formChangedTimer        = setTimeout(
							() => {
								const eventTargetId  = eventTarget.id;
								this.handleOrderCommentsChanges( eventTargetId, eventTarget.value );
								const currentAddress = this.getAddressData( true );
								if ( eventTargetId.includes( 'billing_postcode' ) || eventTargetId.includes( 'billing_country' ) || eventTargetId.includes( 'billing_state' ) ) {
									this.checkIsValidPostCodeAndLoadPayment();
								} else if (
									this.lastAddressVerified !== currentAddress
									|| eventTargetId.includes( 'payment_method' )
									|| eventTargetId.includes( '_woo_additional_terms' )
								) {

									this.lastAddressVerified = currentAddress;
									// noinspection JSUnresolvedReference
									this.setPaymentMethod(
										$( 'input[name="payment_method"]:checked' ).val(),
										true
									);
								}
							},
							500
						);
					},
					checkIsValidPostCodeAndLoadPayment( forcePaymentMethodInit = true ) {
						const postcode = document.getElementById( 'billing_postcode' ).value;
						const country  = document.getElementById( 'billing_country' ).value;
						// noinspection JSUnresolvedReference
						const selectedPaymentMethod = $( 'input[name="payment_method"]:checked' ).val();

						if ( postcode && country ) {
							// noinspection JSUnresolvedReference
							jQuery.ajax(
								{
									url: '/?wc-ajax=woo-plugin-check-postcode',
									type: 'POST',
									data: {
										_wpnonce: WooPluginAjaxCheckout.wpnonce_check_postcode,
										postcode: postcode,
										country: country,
									},
									success: ( response ) => {
										if ( response.success ) {
											const postcodeInput  = document.getElementById( 'billing_postcode' );
											this.invalidPostcode = false;
											document.querySelector( '.woo-plugin-postcode-error-message' )?.remove();
											postcodeInput.classList.remove( 'woo-plugin-invalid-postcode' );
										} else {
											this.invalidPostcode = true;
											const postcodeInput  = document.getElementById( 'billing_postcode' );
											document.querySelector( '.woo-plugin-postcode-error-message' )?.remove();
											const messageContainer = document.createElement( "div" );
											messageContainer.setAttribute( 'class', 'classic-checkout-validation-error wc-block-components-validation-error woo-plugin-postcode-error-message' );
											messageContainer.setAttribute( 'role', 'alert' );
											const	message   = document.createElement( "p" );
											message.innerText = response.data.message;
											messageContainer.appendChild( message );
											postcodeInput.after( messageContainer );
											postcodeInput.classList.add( 'woo-plugin-invalid-postcode' );
										}

										this.setPaymentMethod( selectedPaymentMethod, forcePaymentMethodInit );
									}
								}
							);
						} else {
							this.setPaymentMethod( selectedPaymentMethod, forcePaymentMethodInit );
						}
					},
					handleOrderCommentsChanges( eventTargetId, value ) {
						if ( eventTargetId.includes( 'order_comments' ) ) {
							// noinspection JSUnresolvedReference
							jQuery.ajax(
								{
									url: '/?wc-ajax=woo-plugin-update-order-notes',
									type: 'POST',
									data: {
										_wpnonce: WooPluginAjaxCheckout.wpnonce_update_order_notes,
										value: value,
									}
								}
							);
						}
					},
					addBeforeLeavePageListener() {
						window.onbeforeunload = function () {
							document.removeEventListener( classicPluginPrefix + "_cart_total_changed", this.handleCartTotalChanged )
						};
					}
				}

				wooPluginHelper.init();
				wooPluginHelper.addBeforeLeavePageListener();
				initPhoneNumberValidation();
			}
		);
	}
)
