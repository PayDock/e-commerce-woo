/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/includes/apms.js":
/*!***************************************!*\
  !*** ./resources/js/includes/apms.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wallets_validate_form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./wallets/validate-form */ "./resources/js/includes/wallets/validate-form.js");
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @woocommerce/blocks-registry */ "@woocommerce/blocks-registry");
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _woocommerce_block_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @woocommerce/block-data */ "@woocommerce/block-data");
/* harmony import */ var _woocommerce_block_data__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_block_data__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _canMakePayment__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./canMakePayment */ "./resources/js/includes/canMakePayment.js");









const textDomain = 'paydock';
const labels = {
  defaultLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Paydock Payments', textDomain),
  placeOrderButtonLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Place Order by Paydock', textDomain),
  validationError: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Please fill in the required fields of the form to display payment methods', textDomain),
  notAvailable: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The payment method is not available in your country.', textDomain)
};
let wasInit = false;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((id, defaultLabel, buttonId, dataFieldsRequired, countries) => {
  const settingKey = `paydock_${id}_a_p_m_s_block_data`;
  const paymentName = `paydock_${id}_a_p_m_s_gateway`;
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__.getSetting)(settingKey, {});
  const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__.decodeEntities)(settings.title) || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)(defaultLabel, textDomain);
  const cart = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_6__.select)(_woocommerce_block_data__WEBPACK_IMPORTED_MODULE_7__.CART_STORE_KEY);
  const Content = props => {
    const {
      eventRegistration,
      emitResponse
    } = props;
    const {
      onPaymentSetup,
      onCheckoutValidation,
      onShippingRateSelectSuccess
    } = eventRegistration;
    const billingAddress = cart.getCustomerData().billingAddress;
    const shippingAddress = cart.getCustomerData().shippingAddress;
    const shippingRates = cart.getShippingRates();
    const countriesError = jQuery('.paydock-country-available');
    const validationError = jQuery('.paydock-validation-error');
    const buttonElement = jQuery('#' + buttonId);
    const orderButton = jQuery('.wc-block-components-checkout-place-order-button');
    const paymentCompleteElement = jQuery('#paymentCompleted');
    let validationSuccess = (0,_wallets_validate_form__WEBPACK_IMPORTED_MODULE_4__["default"])(billingAddress, dataFieldsRequired);
    let isAvailableCountry = !!countries.find(element => element === billingAddress.country.toLowerCase());
    let button = null;
    let meta = {};
    let data = {
      ...settings
    };
    data.customers = '';
    data.styles = '';
    data.supports = '';
    data.pickupLocations = '';
    if (data.total_limitation) {
      delete data.total_limitation;
    }
    validationError.hide();
    countriesError.hide();
    buttonElement.hide();
    if (!validationSuccess) {
      wasInit = false;
      validationError.show();
    } else if (validationSuccess && !isAvailableCountry) {
      wasInit = false;
      countriesError.show();
    } else if (validationSuccess && isAvailableCountry) {
      buttonElement.show();
    }
    setTimeout(() => {
      if (validationSuccess && 'zip' === id && !wasInit) {
        wasInit = true;
        button = new window.paydock.ZipmoneyCheckoutButton('#' + buttonId, settings.publicKey, settings.gatewayId);
        data.gatewayType = 'zippay';
      } else if (validationSuccess && 'afterpay' === id && !wasInit) {
        wasInit = true;
        button = new window.paydock.AfterpayCheckoutButton('#' + buttonId, settings.publicKey, settings.gatewayId);
        meta = {
          amount: settings.amount,
          currency: settings.currency,
          email: billingAddress.email,
          first_name: billingAddress.first_name,
          last_name: billingAddress.last_name,
          address_line: billingAddress.address_1,
          address_line2: billingAddress.address_2,
          address_city: billingAddress.city,
          address_state: billingAddress.state,
          address_postcode: billingAddress.postcode,
          address_country: billingAddress.country,
          phone: billingAddress.phone
        };
        data.gatewayType = 'afterpay';
      }
      if (button) {
        button.onFinishInsert('input[name="payment_source_apm_token"]', 'paydock_payment_source_token');
        const shipping_address = {
          first_name: shippingAddress.first_name,
          last_name: shippingAddress.last_name,
          line1: shippingAddress.address_1,
          line2: shippingAddress.address_2,
          country: shippingAddress.country,
          postcode: shippingAddress.postcode,
          city: shippingAddress.city,
          state: shippingAddress.state
        };
        if (shippingRates.length && shippingRates[0].shipping_rates.length) {
          shippingRates[0].shipping_rates.forEach((rate, _key) => {
            if (!rate.selected) {
              return;
            }
            shipping_address.amount = Number((rate.price / 100).toFixed(3)).toFixed(2);
            shipping_address.currency = rate.currency_code;
            if (rate.method_id !== 'pickup_location') {
              return;
            }
            const rateId = rate.rate_id.split(':');
            const pickupLocation = settings.pickupLocations[rateId[1]];
            shipping_address.line1 = pickupLocation.address.address_1;
            shipping_address.line2 = '';
            shipping_address.country = pickupLocation.address.country;
            shipping_address.postcode = pickupLocation.address.postcode;
            shipping_address.city = pickupLocation.address.city;
            shipping_address.state = pickupLocation.address.state;
          });
        }
        meta.charge = {
          amount: settings.amount,
          currency: settings.currency,
          email: billingAddress.email,
          first_name: billingAddress.first_name,
          last_name: billingAddress.last_name,
          shipping_address: shipping_address,
          billing_address: {
            first_name: billingAddress.first_name,
            last_name: billingAddress.last_name,
            line1: billingAddress.address_1,
            line2: billingAddress.address_2,
            country: billingAddress.country,
            postcode: billingAddress.postcode,
            city: billingAddress.city,
            state: billingAddress.state
          },
          items: cart.getCartData().items.map(item => {
            const result = {
              name: item.name,
              amount: item.prices.price / 100,
              quantity: item.quantity,
              reference: item.short_description
            };
            if (item.images.length > 0) {
              result.image_uri = item.images[0].src;
            }
            return result;
          })
        };
        button.setEnv(settings.isSandbox ? 'sandbox' : 'production');
        button.setMeta(meta);
        button.on('finish', () => {
          if (settings.directCharge) {
            data.directCharge = true;
          }
          if (settings.fraud) {
            data.fraud = true;
            data.fraudServiceId = settings.fraudServiceId;
          }
          if (orderButton !== null) {
            orderButton.click();
          }
          paymentCompleteElement.show();
        });
      }
    }, 100);
    (0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
      const unsubscribeFromShippingEvent = onShippingRateSelectSuccess(async () => {
        const {
          total_price: currentTotalPrice
        } = cart.getCartTotals();
        const newAmount = Number(currentTotalPrice / 100).toFixed(2);
        const updateAmount = (currentAmount, newAmount) => currentAmount !== undefined ? {
          amount: newAmount
        } : {};
        button.setMeta({
          ...meta,
          ...updateAmount(meta.amount, newAmount),
          charge: {
            ...meta.charge,
            ...updateAmount(meta.charge.amount, newAmount)
          }
        });
      });
      const unsubscribeFromPaymentSetup = onPaymentSetup(async () => {
        const paymentSourceToken = document.querySelector('input[name="payment_source_apm_token"]');
        if (paymentSourceToken === null) {
          return;
        }
        data.paymentSourceToken = paymentSourceToken.value;
        if (data.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
          return {
            type: emitResponse.responseTypes.SUCCESS,
            meta: {
              paymentMethodData: data
            }
          };
        }
        return {
          type: emitResponse.responseTypes.ERROR,
          message: labels.fillDataError
        };
      });
      return () => {
        const unsubscribeFn = fn => typeof fn === 'function' ? fn() : null;
        unsubscribeFn(unsubscribeFromPaymentSetup);
        unsubscribeFn(unsubscribeFromShippingEvent);
        wasInit = false;
      };
    }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, onCheckoutValidation]);
    return (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('div', {
      id: 'paydockWidgetApm'
    }, (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('div', {
      id: 'paymentCompleted',
      style: {
        display: 'none',
        'background-color': settings.styles.background_color,
        'color': settings.styles.success_color,
        'font-size': settings.styles.font_size,
        'font-family': settings.styles.font_family
      }
    }, 'Payment Details Collected'), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('div', null, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__.decodeEntities)(settings.description || '')), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('div', {
      class: 'apms-button-wrapper'
    }, (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('button', {
      type: 'button',
      id: buttonId,
      class: `btn-apm btn-apm-${id}`,
      style: {
        display: 'none'
      }
    }, (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('img', {
      src: `/wp-content/plugins/paydock/assets/images/${id}.png`
    }))), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('div', {
      class: 'paydock-validation-error'
    }, labels.validationError), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)('input', {
      type: 'hidden',
      name: 'payment_source_apm_token'
    }), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
      class: 'paydock-country-available',
      style: {
        display: 'none'
      }
    }, labels.notAvailable));
  };
  const Label = props => {
    const {
      PaymentMethodLabel
    } = props.components;
    return (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)(PaymentMethodLabel, {
      text: label
    });
  };
  const PaydokApms = {
    name: paymentName,
    label: (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)(() => (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
      className: 'paydock-payment-method-label'
    }, (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)("img", {
      src: `/wp-content/plugins/paydock/assets/images/icons/${id}.png`,
      alt: label,
      className: `paydock-payment-method-label-icon ${id}`
    }), "  " + label)),
    content: (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)(Content, null),
    edit: (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)(Content, null),
    placeOrderButtonLabel: labels.placeOrderButtonLabel,
    canMakePayment: () => (0,_canMakePayment__WEBPACK_IMPORTED_MODULE_8__["default"])(settings.total_limitation, cart.getCartTotals()?.total_price),
    ariaLabel: label,
    supports: {
      features: settings.supports
    }
  };
  (0,_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_5__.registerPaymentMethod)(PaydokApms);
});

/***/ }),

/***/ "./resources/js/includes/canMakePayment.js":
/*!*************************************************!*\
  !*** ./resources/js/includes/canMakePayment.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* export default binding */ __WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__(total_limitation, total) {
  const isObject = value => value !== null && typeof value === 'object';
  if (!isObject(total_limitation)) return true;
  let min = 0;
  let max = 0;
  if (total_limitation.max) {
    max = total_limitation.max * 100;
  }
  if (total_limitation.min) {
    min = total_limitation.min * 100;
  }
  min = total >= min;
  max = max === 0 || total <= max;
  return min && max;
}

/***/ }),

/***/ "./resources/js/includes/wallets/validate-form.js":
/*!********************************************************!*\
  !*** ./resources/js/includes/wallets/validate-form.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((data, requiredFields) => {
  for (let i = 0; i < requiredFields.length; i++) {
    if (!data.hasOwnProperty(requiredFields[i]) || !data[requiredFields[i]]) {
      return false;
    }
  }
  return true;
});

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@woocommerce/block-data":
/*!**************************************!*\
  !*** external ["wc","wcBlocksData"] ***!
  \**************************************/
/***/ ((module) => {

module.exports = window["wc"]["wcBlocksData"];

/***/ }),

/***/ "@woocommerce/blocks-registry":
/*!******************************************!*\
  !*** external ["wc","wcBlocksRegistry"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wc"]["wcBlocksRegistry"];

/***/ }),

/***/ "@woocommerce/settings":
/*!************************************!*\
  !*** external ["wc","wcSettings"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wc"]["wcSettings"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/html-entities":
/*!**************************************!*\
  !*** external ["wp","htmlEntities"] ***!
  \**************************************/
/***/ ((module) => {

module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./resources/js/frontend/zip-a-p-m-s.js ***!
  \**********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _includes_apms__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../includes/apms */ "./resources/js/includes/apms.js");

(0,_includes_apms__WEBPACK_IMPORTED_MODULE_0__["default"])('zip', 'Paydock Zip', 'paydockAPMsZipButton', ['first_name', 'last_name', 'email', 'address_1', 'city', 'state', 'country', 'postcode'], ['au', 'nz', 'us', 'ca']);
/******/ })()
;
//# sourceMappingURL=zip-a-p-m-s.js.map