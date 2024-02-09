/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

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

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!****************************************!*\
  !*** ./resources/js/frontend/index.js ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/blocks-registry */ "@woocommerce/blocks-registry");
/* harmony import */ var _woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);







const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_4__.getSetting)('paydock_data', {});
const defaultLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Paydock Payments', 'paydock-for-woo');
const saveCardLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Save card', 'paydock-for-woo');
const selectTokenLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select card', 'paydock-for-woo');
const placeOrderButtonLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Place Order by Paydock', 'paydock-for-woo');
const fillDataError = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Please fill card data', 'paydock-for-woo');
function getPromiseFromEvent(item, event) {
  return new Promise(resolve => {
    const listener = () => {
      item.removeEventListener(event, listener);
      resolve();
    };
    item.addEventListener(event, listener);
  });
}
const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.title) || defaultLabel;
let sleepSetTimeout_ctrl;
function sleep(ms) {
  clearInterval(sleepSetTimeout_ctrl);
  return new Promise(resolve => sleepSetTimeout_ctrl = setTimeout(resolve, ms));
}
const getVaultToken = async ottToken => {
  return jQuery.post(PaydockAjax.url, {
    action: 'get_vault_token',
    paymentsourcetoken: ottToken,
    cardsavecard: true
  }).then();
};
const getStandalone3dsToken = async vaultToken => {
  return jQuery.post(PaydockAjax.url, {
    action: 'get_vault_token',
    type: 'standalone-3ds-token',
    vaulttoken: vaultToken,
    gatewayid: settings.gatewayId,
    amount: settings.cardTotal,
    curency: settings.curency,
    card3dsserviceid: settings.card3DSServiceId
  }).then();
};
const standalone3Ds = async ottToken => {
  settings.selectedToken = await getVaultToken(ottToken);
  const threeDsToken = await getStandalone3dsToken(settings.selectedToken);
  const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', threeDsToken);
  canvas.load();
  const chargeAuthSuccessEvent = await canvas.on('chargeAuthSuccess');
  return chargeAuthSuccessEvent.charge_3ds_id;
};
const inBuild3Ds = async ottToken => {
  const preAuthData = {
    amount: settings.cardTotal,
    currency: settings.currency
  };
  if (settings.card3DSFlow === 'PERMANENT_VAULT') {
    preAuthData.customer = {
      payment_source: {
        vault_token: await getVaultToken(ottToken),
        gateway_id: settings.gatewayId
      }
    };
  } else {
    preAuthData.token = ottToken;
  }
  const envVal = settings.isSandbox ? 'sandbox' : 'production';
  const preAuthResp = await new window.paydock.Api(settings.publicKey).setEnv(envVal).charge().preAuth(preAuthData);
  if (typeof preAuthResp._3ds.token === "undefined") {
    return false;
  }
  const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', preAuthResp._3ds.token);
  canvas.load();
  document.getElementById('paydockWidgetCard').setAttribute('style', 'display: none');
  const chargeAuthEvent = await canvas.on('chargeAuth');
  return chargeAuthEvent.charge_3ds_id;
};
window.formSubmittedAlready = false;
const selectSavedCardsComponent = () => {
  if (!settings.cardSaveCard || !settings.isUserLoggedIn) {
    return '';
  }
  const options = [{
    label: selectTokenLabel,
    value: ''
  }];
  settings.cardTokens.forEach(token => {
    let label = `${token.card_number_bin}****${token.card_number_last4}`;
    if (token.card_name !== undefined) {
      label = `${token.card_name} ${token.card_number_bin}****${token.card_number_last4}`;
    }
    options.push({
      label: label,
      value: token.vault_token
    });
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    options: options,
    onChange: value => {
      settings.selectedToken = value;
      window.widget.setFormValue('card_name', '');
      window.widget.setFormValue('card_number', '');
      document.getElementById('card_save_card').disabled = false;
      if (value !== '') {
        const token = settings.cardTokens.find(token => token.vault_token === value);
        if (token !== undefined) {
          if (token.card_name !== undefined) {
            window.widget.setFormValue('card_name', token.card_name);
          }
          window.widget.setFormValue('card_number', `${token.card_number_bin}`);
          document.getElementById('card_save_card').disabled = true;
        }
      }
      window.widget.reload();
    }
  });
};
const Content = props => {
  const {
    eventRegistration,
    emitResponse
  } = props;
  const {
    onPaymentSetup,
    onCheckoutValidation
  } = eventRegistration;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const validation = onCheckoutValidation(async () => {
      if (settings.selectedToken !== undefined && settings.selectedToken !== '') {
        return true;
      }
      if (window.formSubmittedAlready) {
        return true;
      }
      window.widget.trigger(window.paydock.TRIGGER.SUBMIT_FORM);
      let result = false;
      let ottToken;
      window.widget.on(window.paydock.EVENT.FINISH, event => {
        ottToken = event.payment_source;
        result = true;
      });
      for (let second = 1; second <= 100; second++) {
        await sleep(100);
        if (result) {
          window.formSubmittedAlready = true;
          break;
        }
      }
      if (result) {
        if (settings.card3DS === 'IN_BUILD') {
          const charge3dsId = await inBuild3Ds(ottToken);
          settings.charge3dsId = charge3dsId;
          if (charge3dsId === false) {
            return {
              type: emitResponse.responseTypes.ERROR,
              errorMessage: fillDataError
            };
          }
        } else if (settings.card3DS === 'STANDALONE') {
          const charge3dsId = await standalone3Ds(ottToken);
          settings.charge3dsId = charge3dsId;
          if (charge3dsId === false) {
            return {
              type: emitResponse.responseTypes.ERROR,
              errorMessage: fillDataError
            };
          }
        }
        return true;
      }
      return {
        type: emitResponse.responseTypes.ERROR,
        errorMessage: fillDataError
      };
    });
    const unsubscribe = onPaymentSetup(async () => {
      const paymentSourceToken = document.querySelector('input[name="payment_source_token"]').value;
      const gatewayId = settings.gatewayId;
      const cardDirectCharge = settings.cardDirectCharge;
      const cardSaveCard = settings.cardSaveCard;
      const cardSaveCardOption = settings.cardSaveCardOption;
      const card3DS = settings.card3DS;
      const card3DSServiceId = settings.card3DSServiceId;
      const card3DSFlow = settings.card3DSFlow;
      const cardFraud = settings.cardFraud;
      const cardFraudServiceId = settings.cardFraudServiceId;
      let cardSaveCardChecked = false;
      if (cardSaveCard && document.getElementById('card_save_card') !== null) {
        cardSaveCardChecked = document.getElementById('card_save_card').checked;
      }
      let charge3dsId;
      if (typeof settings.charge3dsId !== "undefined") {
        charge3dsId = settings.charge3dsId;
      }
      let selectedToken;
      const selectedTokenNotEmpty = settings.selectedToken !== undefined && settings.selectedToken !== '';
      if (settings.selectedToken !== undefined) {
        selectedToken = settings.selectedToken;
      }
      const customDataIsValid = !!paymentSourceToken.length || selectedTokenNotEmpty;
      if (customDataIsValid) {
        return {
          type: emitResponse.responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              selectedToken,
              paymentSourceToken,
              gatewayId,
              cardDirectCharge,
              cardSaveCard,
              cardSaveCardOption,
              cardSaveCardChecked,
              card3DS,
              card3DSServiceId,
              card3DSFlow,
              charge3dsId,
              cardFraud,
              cardFraudServiceId
            }
          }
        };
      }
      return {
        type: emitResponse.responseTypes.ERROR,
        message: fillDataError
      };
    });
    return () => {
      validation() && unsubscribe();
    };
  }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, onCheckoutValidation]);
  let selectSavedCards = '';
  if (settings.cardTokens.length > 0) {
    selectSavedCards = selectSavedCardsComponent();
  }
  let saveCard = '';
  if (settings.isUserLoggedIn && settings.cardSaveCard) {
    saveCard = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      class: 'wc-block-components-checkbox'
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      for: 'card_save_card'
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      class: 'wc-block-components-checkbox__input',
      id: 'card_save_card',
      type: 'checkbox',
      name: 'card_save_card'
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
      class: 'wc-block-components-checkbox__mark',
      "aria-hidden": true,
      xmlns: 'http://www.w3.org/2000/svg',
      "viewBox": '0 0 24 20'
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
      d: 'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z'
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
      class: 'wc-block-components-checkbox__label'
    }, saveCardLabel)));
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('div', null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.description || '')), selectSavedCards, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    id: 'paydockWidgetCard'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    id: 'paydockWidget3ds'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: 'hidden',
    name: 'payment_source_token'
  }), saveCard);
};
const Label = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PaymentMethodLabel, {
    text: label
  });
};
const Paydok = {
  name: "paydock_gateway",
  label: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Label, null),
  content: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  edit: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  placeOrderButtonLabel: placeOrderButtonLabel,
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
};
(0,_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_2__.registerPaymentMethod)(Paydok);
})();

/******/ })()
;
//# sourceMappingURL=blocks.js.map