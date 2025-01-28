import {__} from '@wordpress/i18n';
// noinspection NpmUsedModulesInstalled
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {decodeEntities} from '@wordpress/html-entities';
import {getSetting} from '@woocommerce/settings';
import {createElement, useEffect} from 'react';
// noinspection NpmUsedModulesInstalled
import {select} from '@wordpress/data';
// noinspection NpmUsedModulesInstalled
import {CART_STORE_KEY,CHECKOUT_STORE_KEY} from '@woocommerce/block-data';
import canMakePayment from "../includes/canMakePayment";

const store    = select( CHECKOUT_STORE_KEY );
const cart     = select( CART_STORE_KEY );
const settings = getSetting( 'power_board_data', {} );

const textDomain   = 'power-board';
const defaultLabel = __( 'PowerBoard Payments', textDomain );

const label  = decodeEntities( settings.title ) || defaultLabel;
let cartData = null;

const toggleWidgetVisibility = ( hide ) => {
	let widget               = document.getElementById( 'standaloneWidget' );
	let widgetList           = document.getElementById( 'list' );
	let widgetSpinner        = document.getElementById( 'spinner' );

	if (hide) {
		if (widget) {
			widget.style.display = 'none';
		}
		if (widgetList) {
			widgetList.style.display = 'none';
		}
		if (widgetSpinner) {
			widgetSpinner.style.display = 'none';
		}
	} else {
		if (widget) {
			widget.style.display = 'flex';
		}
		if (widgetList) {
			widgetList.style.display = 'flex';
		}
		if (widgetSpinner) {
			widgetSpinner.style.display = 'flex';
		}
	}
};

const initMasterWidgetCheckout = () => {
	// noinspection JSUnresolvedReference
	if ( canMakePayment( settings.total_limitation, cart.getCartTotals()?.total_price ) ) {
		// noinspection JSUnresolvedReference
		const orderButton = jQuery( '.wc-block-components-checkout-place-order-button' );
		orderButton.hide();

		// noinspection JSUnresolvedReference
		jQuery.ajax(
			{
				url: '/?wc-ajax=power-board-create-charge-intent',
				type: 'POST',
				data: {
					_wpnonce: PowerBoardAjax.wpnonce,
					order_id: store.getOrderId(),
					total: cart.getCartTotals(),
					address: cart.getCustomerData().billingAddress,
				},
				success: ( response ) => {
					// noinspection JSUnresolvedReference
					cartData = JSON.stringify( cart.getCartData() );
					toggleWidgetVisibility( false );
					// noinspection JSUnresolvedReference
					window.widgetPowerBoard = new cba.Checkout( '#powerBoardCheckout_wrapper', response.data.resource.data.token );
					// noinspection JSUnresolvedReference
					window.widgetPowerBoard.setEnv( settings.environment )
					// noinspection JSUnresolvedReference
					const orderButton = jQuery( '.wc-block-components-checkout-place-order-button' );
					// noinspection JSUnresolvedReference
					const paymentSourceElement = jQuery( '#paymentSourceToken' );
					// noinspection JSUnresolvedReference
					window.widgetPowerBoard.onPaymentSuccessful(
						function ( data ) {
							// noinspection JSUnresolvedReference
							paymentSourceElement.val( JSON.stringify( data ) );
							orderButton.show();
							orderButton.click();

							window.widgetPowerBoard = null;
						}
					);
					// noinspection JSUnresolvedReference
					window.widgetPowerBoard.onPaymentFailure(
						function () {
							// noinspection JSUnresolvedReference
							paymentSourceElement.val(
								JSON.stringify(
									{
										errorMessage: 'Transaction failed. Please check your payment details or contact your bank',
									}
								)
							);
							orderButton.show();
							orderButton.click();

							window.widgetPowerBoard = null;
						}
					);
					// noinspection JSUnresolvedReference
					window.widgetPowerBoard.onPaymentExpired(
						function () {
							// noinspection JSUnresolvedReference
							paymentSourceElement.val(
								JSON.stringify(
									{
										errorMessage: 'Your payment session has expired. Please retry your payment',
									}
								)
							);
							orderButton.show();
							orderButton.click();

							window.widgetPowerBoard = null;
						}
					);
				}
			}
		);
	}
}

const handleFormChanged = () => {
	// noinspection JSUnresolvedReference
	let isFormValid = jQuery( '.wc-block-components-form' )[0].checkValidity();
	// noinspection JSUnresolvedReference
	let error = jQuery( '#fields-validation-error' )[0];
	// noinspection JSUnresolvedReference
	let loading = jQuery( '#loading' )[0];
	toggleWidgetVisibility( true );
	if ( isFormValid ) {
		if ( loading.classList.length > 0 ) {
			loading.classList.remove( 'hide' );
		}
		error.classList.add( 'hide' );
		initMasterWidgetCheckout();
	} else {
		loading.classList.add( 'hide' );
		if ( error.classList.length > 0 ) {
			error.classList.remove( 'hide' );
		}
	}
}

const handleWidgetError = () => {
	let loading         = document.getElementById( 'loading' );
	if ( loading.classList.length > 0 ) {
		loading.classList.remove( 'hide' );
	}
	toggleWidgetVisibility( true );
	initMasterWidgetCheckout();

	const checkoutContainer       = document.querySelectorAll( '.wc-block-checkout' )[0];
	const topNotices              = checkoutContainer.querySelectorAll( '.wc-block-components-notices' )[0];
	const paymentMethodsContainer = document.querySelectorAll( '.wc-block-checkout__payment-method' )[0];
	const checkoutPaymentStep     = paymentMethodsContainer?.querySelectorAll( '.wc-block-components-checkout-step__content' )?.[0];
	const checkoutPaymentNotices  = checkoutPaymentStep?.querySelectorAll( '.wc-block-components-notices' )?.[0];
	const removeNoticeInterval    = setInterval(
		() => {
			if ( checkoutPaymentNotices?.children.length > 0 || topNotices.children.length > 0 ) {
				clearInterval( removeNoticeInterval );

				const removeErrorTimeout = setTimeout(
					() => {
						// noinspection JSUnresolvedReference
						clearTimeout( removeErrorTimeout );
						const noticesToCheck = checkoutPaymentNotices?.children.length > 0 ? checkoutPaymentNotices : topNotices;
						for ( const notice of noticesToCheck.children ) {
							if ( notice.classList.contains( 'is-error' ) ) {
								notice.classList.add( 'hide' );
							}
						}
					},
					10000
				);
			}
		},
		200
	);
};

// eslint-disable-next-line no-unused-vars
const Content = ( props ) => {
	// noinspection JSUnresolvedReference
	jQuery( '.wc-block-components-checkout-place-order-button' ).show();
	const {eventRegistration, emitResponse} = props;
	const {onPaymentSetup}                  = eventRegistration;

	// noinspection JSUnresolvedReference
	useEffect(
		() => {
			if ( !window.unsubscribeFromFormChanges ) {
				// noinspection JSUnresolvedReference
				window.unsubscribeFromFormChanges = jQuery( '.wc-block-components-form' )[0].addEventListener( "change", handleFormChanged );
			}
			const unsubscribe               = onPaymentSetup(
				async() => {
					const paymentData       = document.getElementById( 'paymentSourceToken' )?.value
					const paymentDataParsed = JSON.parse( paymentData )
					if ( !!paymentData && !paymentDataParsed.errorMessage ) {
						// noinspection JSUnresolvedReference
						return {
							type: emitResponse.responseTypes.SUCCESS, meta: {
								paymentMethodData: {
									payment_response: paymentData,
									chargeId: paymentDataParsed['charge_id'],
									checkoutOrder: cartData,
									_wpnonce: settings._wpnonce
								}
							},
						};
					}

					handleWidgetError();
					// noinspection JSUnresolvedReference
					return {
						type: emitResponse.responseTypes.ERROR, message: __( paymentDataParsed.errorMessage, textDomain ),
					}
				}
			);
			return () => {
				// noinspection JSUnresolvedReference
				jQuery( '.wc-block-components-form' )[0].removeEventListener( "change", handleFormChanged );
				unsubscribe();
			};
		},
		[emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup]
	);

	const input = createElement(
		"input",
		{
			type: 'hidden', id: 'paymentSourceToken'
		}
	);

	return createElement(
		'div',
		{className: 'master-widget-wrapper'},
		createElement(
			"div",
			{id: 'loading'},
			createElement(
				"p",
				{className: 'loading-text'},
				'Loading...',
			),
		),
		createElement(
			"div",
			{id: 'fields-validation-error', className: 'hide'},
			createElement(
				"p",
				{className: 'power-board-validation-error'},
				'Please fill in the required fields of the form to display payment methods',
			),
		),
		createElement(
			"div",
			{id: 'powerBoardCheckout_wrapper'}
		),
		input
	);
};

// noinspection JSUnusedGlobalSymbols,JSUnresolvedReference,JSCheckFunctionSignatures
const Paydock = {
	name: "power_board_gateway",
	label: createElement(
		() =>
			createElement(
				"div",
				{
					className: 'power-board-payment-method-label'
				},
				label,
				createElement(
					"img",
					{
						src: `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/logo.png`,
						alt: label,
						className: 'power-board-payment-method-label-logo'
					}
				)
			)
	), content: <Content />, edit: <Content />, canMakePayment: () => true, ariaLabel: label, supports: { features: settings.supports }
};

registerPaymentMethod( Paydock );
