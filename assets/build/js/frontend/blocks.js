/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

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
  if (!isObject(total_limitation)) {
    return true;
  }
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
/*!****************************************!*\
  !*** ./resources/js/frontend/index.js ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/blocks-registry */ "@woocommerce/blocks-registry");
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _woocommerce_block_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @woocommerce/block-data */ "@woocommerce/block-data");
/* harmony import */ var _woocommerce_block_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_block_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _includes_canMakePayment__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../includes/canMakePayment */ "./resources/js/includes/canMakePayment.js");

// noinspection NpmUsedModulesInstalled




// noinspection NpmUsedModulesInstalled

// noinspection NpmUsedModulesInstalled


const store = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.select)(_woocommerce_block_data__WEBPACK_IMPORTED_MODULE_6__.CHECKOUT_STORE_KEY);
const cart = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.select)(_woocommerce_block_data__WEBPACK_IMPORTED_MODULE_6__.CART_STORE_KEY);
const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_3__.getSetting)('power_board_data', {});
const textDomain = 'power-board';
const defaultLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('PowerBoard Payments', textDomain);
const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_2__.decodeEntities)(settings.title) || defaultLabel;
let totalChangesTimeout = null;
let totalChangesSecondTimeout = null;
let address = null;
let lastMasterWidgetInit = null;
const toggleWidgetVisibility = hide => {
  let widget = document.getElementById('standaloneWidget');
  let widgetList = document.getElementById('list');
  let widgetSpinner = document.getElementById('spinner');
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
const toggleOrderButton = hide => {
  let orderButton = document.querySelectorAll('.wc-block-components-checkout-place-order-button')[0];
  window.toggleOrderButton(orderButton, hide);
};
const getSelectedShippingValue = () => {
  // noinspection JSUnresolvedReference
  const selectedShipping = jQuery('.wc-block-components-radio-control__input:checked').filter(function () {
    // noinspection JSUnresolvedReference
    const id = jQuery(this).attr('id');
    return id.includes('rate') || id.includes('shipping');
  });
  return selectedShipping[0]?.value;
};
const initMasterWidgetCheckout = () => {
  // noinspection JSUnresolvedReference
  if ((0,_includes_canMakePayment__WEBPACK_IMPORTED_MODULE_7__["default"])(settings.total_limitation, cart.getCartTotals()?.total_price)) {
    const initTimestamp = new Date().getTime();
    lastMasterWidgetInit = initTimestamp;
    setTimeout(() => toggleOrderButton(true), 100);

    // noinspection JSUnresolvedReference
    jQuery.ajax({
      url: '/?wc-ajax=power-board-create-charge-intent',
      type: 'POST',
      data: {
        _wpnonce: PowerBoardAjaxCheckout.wpnonce_intent,
        order_id: store.getOrderId(),
        total: cart.getCartTotals(),
        address: cart.getCustomerData().billingAddress,
        selected_shipping_id: getSelectedShippingValue()
      },
      success: response => {
        if (initTimestamp === lastMasterWidgetInit) {
          toggleWidgetVisibility(false);
          const widgetSelector = '#powerBoardCheckout_wrapper';
          // noinspection JSUnresolvedReference
          if (!jQuery(widgetSelector)[0]) {
            return;
          }
          // noinspection JSUnresolvedReference
          window.widgetPowerBoard = new cba.Checkout(widgetSelector, response.data.resource.data.token);
          // noinspection JSUnresolvedReference
          window.widgetPowerBoard.setEnv(settings.environment);
          // noinspection JSUnresolvedReference
          const orderButton = jQuery('.wc-block-components-checkout-place-order-button')[0];
          // noinspection JSUnresolvedReference
          const paymentSourceElement = jQuery('#paymentSourceToken');

          // noinspection JSUnresolvedReference
          window.widgetPowerBoard.onPaymentSuccessful(function (data) {
            // noinspection JSUnresolvedReference
            const orderId = store.getOrderId();
            // noinspection JSUnresolvedReference
            jQuery.ajax({
              url: '/?wc-ajax=power-board-process-payment-result',
              method: 'POST',
              data: {
                _wpnonce: PowerBoardAjaxCheckout.wpnonce_process_payment,
                order_id: orderId,
                payment_response: data
              },
              success: function () {
                // noinspection JSUnresolvedReference
                paymentSourceElement.val(JSON.stringify({
                  ...data,
                  orderId: orderId
                }));
                orderButton.click();
                window.widgetPowerBoard = null;
              }
            });
          });
          // noinspection JSUnresolvedReference
          window.widgetPowerBoard.onPaymentFailure(function (data) {
            // noinspection JSUnresolvedReference
            paymentSourceElement.val(JSON.stringify({
              errorMessage: 'Transaction failed. Please check your payment details or contact your bank'
            }));
            // noinspection JSUnresolvedReference
            jQuery.ajax({
              url: '/?wc-ajax=power-board-process-payment-result',
              method: 'POST',
              data: {
                _wpnonce: PowerBoardAjaxCheckout.wpnonce_process_payment,
                order_id: store.getOrderId(),
                payment_response: {
                  ...data,
                  errorMessage: data.message || 'Transaction failed'
                }
              },
              success: function () {
                orderButton.click();
                window.widgetPowerBoard = null;
              }
            });
          });

          // noinspection JSUnresolvedReference
          window.widgetPowerBoard.onPaymentExpired(function () {
            // noinspection JSUnresolvedReference
            paymentSourceElement.val(JSON.stringify({
              errorMessage: 'Your payment session has expired. Please retry your payment'
            }));
            orderButton.click();
            window.widgetPowerBoard = null;
          });
        }
      }
    });
  }
};

// noinspection DuplicatedCode
window.initMasterWidgetCheckout = initMasterWidgetCheckout;
const handleWidgetDisplay = () => {
  // noinspection JSUnresolvedReference
  let isFormValid = jQuery('.wc-block-components-form')[0].checkValidity();
  // noinspection JSUnresolvedReference
  let error = jQuery('#fields-validation-error')[0];
  // noinspection JSUnresolvedReference
  let loading = jQuery('#loading')[0];
  toggleWidgetVisibility(true);
  if (isFormValid) {
    if (loading.classList.length > 0) {
      loading.classList.remove('hide');
    }
    error.classList.add('hide');
  } else {
    loading.classList.add('hide');
    if (error.classList.length > 0) {
      error.classList.remove('hide');
    }
  }
  if (isFormValid) {
    clearTimeout(window.initWidgetTimer);
    window.initWidgetTimer = setTimeout(() => {
      initMasterWidgetCheckout();
    }, 500);
  }
};
const handleCartTotalChanged = event => {
  if (totalChangesTimeout) {
    clearTimeout(totalChangesTimeout);
  }
  toggleWidgetVisibility(true);
  totalChangesTimeout = setTimeout(() => {
    const spanTotal = getUIOrderTotal();
    const cartTotal = +event.detail.cartTotal;
    if (spanTotal !== cartTotal) {
      if (totalChangesSecondTimeout) {
        clearTimeout(totalChangesSecondTimeout);
      }
      totalChangesSecondTimeout = setTimeout(() => {
        const spanTotal = getUIOrderTotal();
        if (spanTotal !== cartTotal) {
          window.reloadAfterExternalCartChanges();
        } else {
          handleWidgetDisplay();
        }
      }, 300);
    } else {
      handleWidgetDisplay();
    }
  }, 300);
};
const getUIOrderTotal = () => {
  // noinspection JSUnresolvedReference
  const orderTotalElement = jQuery('.wc-block-components-totals-footer-item-tax-value')[0];
  return orderTotalElement ? +orderTotalElement?.innerText.replace(/[^0-9.,]*/, '') : null;
};
const handleFormChanged = () => {
  // noinspection JSUnresolvedReference
  const billingAddress = cart.getCustomerData().billingAddress;
  if (billingAddress !== address) {
    address = billingAddress;
    handleWidgetDisplay();
  }
};
const handleWidgetError = () => {
  let loading = document.getElementById('loading');
  if (loading.classList.length > 0) {
    loading.classList.remove('hide');
  }
  toggleWidgetVisibility(true);
  initMasterWidgetCheckout();
  const checkoutContainer = document.querySelectorAll('.wc-block-checkout')[0];
  const topNotices = checkoutContainer.querySelectorAll('.wc-block-components-notices')[0];
  const paymentMethodsContainer = document.querySelectorAll('.wc-block-checkout__payment-method')[0];
  const checkoutPaymentStep = paymentMethodsContainer?.querySelectorAll('.wc-block-components-checkout-step__content')?.[0];
  const checkoutPaymentNotices = checkoutPaymentStep?.querySelectorAll('.wc-block-components-notices')?.[0];
  const removeNoticeInterval = setInterval(() => {
    if (checkoutPaymentNotices?.children.length > 0 || topNotices.children.length > 0) {
      clearInterval(removeNoticeInterval);
      const removeErrorTimeout = setTimeout(() => {
        // noinspection JSUnresolvedReference
        clearTimeout(removeErrorTimeout);
        const noticesToCheck = checkoutPaymentNotices?.children.length > 0 ? checkoutPaymentNotices : topNotices;
        for (const notice of noticesToCheck.children) {
          if (notice.classList.contains('is-error')) {
            notice.classList.add('hide');
          }
        }
      }, 10000);
    }
  }, 200);
};

// eslint-disable-next-line no-unused-vars
const Content = props => {
  const {
    eventRegistration,
    emitResponse
  } = props;
  const {
    onPaymentSetup
  } = eventRegistration;

  // noinspection JSUnresolvedReference
  (0,react__WEBPACK_IMPORTED_MODULE_4__.useEffect)(() => {
    if (!window.unsubscribeFromFormChanges) {
      // noinspection JSUnresolvedReference
      window.unsubscribeFromFormChanges = jQuery('.wc-block-components-form')[0].addEventListener("change", handleFormChanged);
    }
    if (!window.cartChangesEventListenerSetup) {
      document.addEventListener("power_board_cart_total_changed", handleCartTotalChanged);
      window.cartChangesEventListenerSetup = true;
    }
    const unsubscribe = onPaymentSetup(async () => {
      const paymentData = document.getElementById('paymentSourceToken')?.value;
      const paymentDataParsed = JSON.parse(paymentData);
      if (!!paymentData && !paymentDataParsed.errorMessage) {
        // noinspection JSUnresolvedReference
        return {
          type: emitResponse.responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              payment_response: paymentData,
              chargeId: paymentDataParsed['charge_id'],
              orderId: paymentDataParsed['order_id'],
              _wpnonce: settings._wpnonce
            }
          }
        };
      }
      handleWidgetError();
      // noinspection JSUnresolvedReference
      return {
        type: emitResponse.responseTypes.ERROR,
        message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)(paymentDataParsed.errorMessage, textDomain)
      };
    });
    return () => {
      // noinspection JSUnresolvedReference
      const form = jQuery('.wc-block-components-form')[0];
      if (form) {
        form.removeEventListener("change", handleFormChanged);
      }
      document.removeEventListener("power_board_cart_total_changed", handleCartTotalChanged);
      window.cartChangesEventListenerSetup = false;
      unsubscribe();
    };
  }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup]);
  const input = (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("input", {
    type: 'hidden',
    id: 'paymentSourceToken'
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)('div', {
    className: 'master-widget-wrapper'
  }, (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("div", {
    id: 'loading'
  }, (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("p", {
    className: 'loading-text'
  }, 'Loading...')), (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("div", {
    id: 'fields-validation-error',
    className: 'hide'
  }, (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("p", {
    className: 'power-board-validation-error'
  }, 'Please fill in the required fields of the form to display payment methods')), (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("div", {
    id: 'powerBoardCheckout_wrapper'
  }), input);
};

// noinspection JSUnusedGlobalSymbols,JSUnresolvedReference,JSCheckFunctionSignatures
const Paydock = {
  name: "power_board",
  label: (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)(() => (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("div", {
    className: 'power-board-payment-method-label'
  }, label, (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)("img", {
    src: `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/logo.png`,
    alt: label,
    className: 'power-board-payment-method-label-logo'
  }))),
  content: (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)(Content, null),
  edit: (0,react__WEBPACK_IMPORTED_MODULE_4__.createElement)(Content, null),
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
};
(0,_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_1__.registerPaymentMethod)(Paydock);
/******/ })()
;
//# sourceMappingURL=blocks.js.map