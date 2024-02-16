/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/includes/components/checkbox-saved-apms.js":
/*!*****************************************************************!*\
  !*** ./resources/js/includes/components/checkbox-saved-apms.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((saveCardLabel = 'Save card') => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paydock_apms_data', {});
  if (!settings.isUserLoggedIn) {
    return '';
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    class: 'wc-block-components-checkbox amps-save-card',
    style: {
      display: settings.afterpaySaveCard && settings.zippaySaveCard ? 'block' : 'none'
    }
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    for: 'apms_save_card',
    onChange: e => {
      settings.apmSaveCardChecked = e.target.checked;
    }
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    class: 'wc-block-components-checkbox__input',
    id: 'apms_save_card',
    type: 'checkbox',
    name: 'apms_save_card'
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
});

/***/ }),

/***/ "./resources/js/includes/components/checkbox-saved-bank-account.js":
/*!*************************************************************************!*\
  !*** ./resources/js/includes/components/checkbox-saved-bank-account.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((saveBankAccountLabel = 'Save bank account') => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paydock_bank_account_block_data', {});
  if (!settings.bankAccountSaveAccount || !settings.isUserLoggedIn) {
    return '';
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    class: 'wc-block-components-checkbox'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    for: 'bank_account_save',
    onChange: e => {
      settings.bankAccountSaveChecked = e.target.checked;
    }
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    class: 'wc-block-components-checkbox__input',
    id: 'bank_account_save',
    type: 'checkbox',
    name: 'bank_account_save'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    class: 'wc-block-components-checkbox__mark',
    "aria-hidden": true,
    xmlns: 'http://www.w3.org/2000/svg',
    "viewBox": '0 0 24 20'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    d: 'M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z'
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    class: 'wc-block-components-checkbox__label'
  }, saveBankAccountLabel)));
});

/***/ }),

/***/ "./resources/js/includes/components/checkbox-saved-cards.js":
/*!******************************************************************!*\
  !*** ./resources/js/includes/components/checkbox-saved-cards.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__);


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((saveCardLabel = 'Save card') => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_1__.getSetting)('paydock_data', {});
  if (!settings.cardSaveCard || !settings.isUserLoggedIn) {
    return '';
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    class: 'wc-block-components-checkbox'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    for: 'card_save_card',
    onChange: e => {
      settings.cardSaveCardChecked = e.target.checked;
    }
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
});

/***/ }),

/***/ "./resources/js/includes/components/select-saved-bank-accounts.js":
/*!************************************************************************!*\
  !*** ./resources/js/includes/components/select-saved-bank-accounts.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((selectTokenLabel = 'Saved bank accounts') => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__.getSetting)('paydock_bank_account_block_data', {});
  if (!settings.bankAccountSaveAccount || !settings.isUserLoggedIn || settings.tokens.length === 0) {
    return '';
  }
  const options = [{
    label: '-',
    value: ''
  }];
  settings.tokens.forEach(token => {
    if (token.type !== 'bank_account') {
      return;
    }
    const scheme = token.account_name;
    const accountNumber = token.account_number.slice(-4);
    const label = `${scheme} ${accountNumber}`;
    options.push({
      label: label,
      value: token.vault_token
    });
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    label: selectTokenLabel,
    options: options,
    onChange: value => {
      settings.selectedToken = value;
      window.widgetBankAccount.setFormValue('account_name', '');
      window.widgetBankAccount.setFormValue('account_number', '');
      window.widgetBankAccount.setFormValue('account_routing', '');
      document.getElementById('bank_account_save').disabled = false;
      if (value !== '') {
        const token = settings.tokens.find(token => token.vault_token === value);
        if (token !== undefined) {
          window.widgetBankAccount.setFormValue('account_name', token.account_name);
          window.widgetBankAccount.setFormValue('account_number', token.account_number);
          window.widgetBankAccount.setFormValue('account_routing', token.account_routing);
          document.getElementById('bank_account_save').disabled = true;
        }
      }
      window.widgetBankAccount.reload();
    }
  });
});

/***/ }),

/***/ "./resources/js/includes/components/select-saved-cards.js":
/*!****************************************************************!*\
  !*** ./resources/js/includes/components/select-saved-cards.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((selectTokenLabel = 'Saved cards') => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_2__.getSetting)('paydock_data', {});
  if (!settings.cardSaveCard || !settings.isUserLoggedIn || settings.tokens.length === 0) {
    return '';
  }
  const options = [{
    label: '-',
    value: ''
  }];
  settings.tokens.forEach(token => {
    if (token.type !== 'card') {
      return;
    }
    const cardScheme = token.card_scheme.charAt(0).toUpperCase() + token.card_scheme.slice(1);
    const expireMonth = token.expire_month < 10 ? `0${token.expire_month}` : token.expire_month;
    const label = `${cardScheme} ${token.card_number_last4} ${expireMonth}/${token.expire_year}`;
    options.push({
      label: label,
      value: token.vault_token
    });
  });
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    label: selectTokenLabel,
    options: options,
    onChange: value => {
      settings.selectedToken = value;
      window.widget.setFormValue('card_name', '');
      window.widget.setFormValue('card_number', '');
      window.widget.setFormValue('expire_month', '');
      window.widget.setFormValue('expire_year', '');
      document.getElementById('card_save_card').disabled = false;
      if (value !== '') {
        const token = settings.tokens.find(token => token.vault_token === value);
        if (token !== undefined) {
          if (token.card_name !== undefined) {
            window.widget.setFormValue('card_name', token.card_name);
          }
          window.widget.setFormValue('card_number', `${token.card_number_last4}`);
          window.widget.setFormValue('expire_month', `${token.expire_month}`);
          window.widget.setFormValue('expire_year', `${token.expire_year}`);
          document.getElementById('card_save_card').disabled = true;
        }
      }
      window.widget.reload();
    }
  });
});

/***/ }),

/***/ "./resources/js/includes/get-standalone-3ds-token.js":
/*!***********************************************************!*\
  !*** ./resources/js/includes/get-standalone-3ds-token.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (async () => {
  const data = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__.getSetting)('paydock_data', {});
  data.action = 'get_vault_token';
  data.type = 'standalone-3ds-token';
  data.tokens = '';
  data.styles = '';
  data.supports = '';
  return jQuery.post(PaydockAjax.url, data).then();
});

/***/ }),

/***/ "./resources/js/includes/get-vault-token.js":
/*!**************************************************!*\
  !*** ./resources/js/includes/get-vault-token.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (async () => {
  const data = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__.getSetting)('paydock_data', {});
  data.action = 'get_vault_token';
  data.tokens = '';
  data.styles = '';
  data.supports = '';
  return jQuery.post(PaydockAjax.url, data).then();
});

/***/ }),

/***/ "./resources/js/includes/in-build-3ds.js":
/*!***********************************************!*\
  !*** ./resources/js/includes/in-build-3ds.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_vault_token__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-vault-token */ "./resources/js/includes/get-vault-token.js");


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (async (forcePermanentVault = false) => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__.getSetting)('paydock_data', {});
  if (settings.selectedToken.trim().length === 0 && settings.card3DSFlow === 'PERMANENT_VAULT') {
    settings.selectedToken = await (0,_get_vault_token__WEBPACK_IMPORTED_MODULE_1__["default"])();
  }
  const preAuthData = {
    amount: settings.amount,
    currency: settings.currency
  };
  if (settings.card3DSFlow === 'PERMANENT_VAULT' || forcePermanentVault) {
    preAuthData.customer = {
      payment_source: {
        vault_token: settings.selectedToken,
        gateway_id: settings.gatewayId
      }
    };
  } else {
    preAuthData.token = settings.paymentSourceToken;
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
});

/***/ }),

/***/ "./resources/js/includes/sleep.js":
/*!****************************************!*\
  !*** ./resources/js/includes/sleep.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
let sleepSetTimeout_ctrl;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ms => {
  clearInterval(sleepSetTimeout_ctrl);
  return new Promise(resolve => sleepSetTimeout_ctrl = setTimeout(resolve, ms));
});

/***/ }),

/***/ "./resources/js/includes/standalone-3ds.js":
/*!*************************************************!*\
  !*** ./resources/js/includes/standalone-3ds.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/settings */ "@woocommerce/settings");
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _get_vault_token__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-vault-token */ "./resources/js/includes/get-vault-token.js");
/* harmony import */ var _get_standalone_3ds_token__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-standalone-3ds-token */ "./resources/js/includes/get-standalone-3ds-token.js");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (async () => {
  const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__.getSetting)('paydock_data', {});
  if (settings.selectedToken.trim().length === 0) {
    settings.selectedToken = await (0,_get_vault_token__WEBPACK_IMPORTED_MODULE_1__["default"])();
  }
  const threeDsToken = await (0,_get_standalone_3ds_token__WEBPACK_IMPORTED_MODULE_2__["default"])(settings.selectedToken);
  const canvas = new window.paydock.Canvas3ds('#paydockWidget3ds', threeDsToken);
  canvas.load();
  const chargeAuthSuccessEvent = await canvas.on('chargeAuthSuccess');
  return chargeAuthSuccessEvent.charge_3ds_id;
});

/***/ }),

/***/ "./resources/js/includes/wc-paydock.js":
/*!*********************************************!*\
  !*** ./resources/js/includes/wc-paydock.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   checkboxSavedApmsComponent: () => (/* reexport safe */ _components_checkbox_saved_apms__WEBPACK_IMPORTED_MODULE_9__["default"]),
/* harmony export */   checkboxSavedBankAccountComponent: () => (/* reexport safe */ _components_checkbox_saved_bank_account__WEBPACK_IMPORTED_MODULE_8__["default"]),
/* harmony export */   checkboxSavedCardsComponent: () => (/* reexport safe */ _components_checkbox_saved_cards__WEBPACK_IMPORTED_MODULE_6__["default"]),
/* harmony export */   getStandalone3dsToken: () => (/* reexport safe */ _get_standalone_3ds_token__WEBPACK_IMPORTED_MODULE_2__["default"]),
/* harmony export */   getVaultToken: () => (/* reexport safe */ _get_vault_token__WEBPACK_IMPORTED_MODULE_1__["default"]),
/* harmony export */   inBuild3Ds: () => (/* reexport safe */ _in_build_3ds__WEBPACK_IMPORTED_MODULE_3__["default"]),
/* harmony export */   selectSavedBankAccountsComponent: () => (/* reexport safe */ _components_select_saved_bank_accounts__WEBPACK_IMPORTED_MODULE_7__["default"]),
/* harmony export */   selectSavedCardsComponent: () => (/* reexport safe */ _components_select_saved_cards__WEBPACK_IMPORTED_MODULE_5__["default"]),
/* harmony export */   sleep: () => (/* reexport safe */ _sleep__WEBPACK_IMPORTED_MODULE_0__["default"]),
/* harmony export */   standalone3Ds: () => (/* reexport safe */ _standalone_3ds__WEBPACK_IMPORTED_MODULE_4__["default"])
/* harmony export */ });
/* harmony import */ var _sleep__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./sleep */ "./resources/js/includes/sleep.js");
/* harmony import */ var _get_vault_token__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./get-vault-token */ "./resources/js/includes/get-vault-token.js");
/* harmony import */ var _get_standalone_3ds_token__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get-standalone-3ds-token */ "./resources/js/includes/get-standalone-3ds-token.js");
/* harmony import */ var _in_build_3ds__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./in-build-3ds */ "./resources/js/includes/in-build-3ds.js");
/* harmony import */ var _standalone_3ds__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./standalone-3ds */ "./resources/js/includes/standalone-3ds.js");
/* harmony import */ var _components_select_saved_cards__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/select-saved-cards */ "./resources/js/includes/components/select-saved-cards.js");
/* harmony import */ var _components_checkbox_saved_cards__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/checkbox-saved-cards */ "./resources/js/includes/components/checkbox-saved-cards.js");
/* harmony import */ var _components_select_saved_bank_accounts__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/select-saved-bank-accounts */ "./resources/js/includes/components/select-saved-bank-accounts.js");
/* harmony import */ var _components_checkbox_saved_bank_account__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/checkbox-saved-bank-account */ "./resources/js/includes/components/checkbox-saved-bank-account.js");
/* harmony import */ var _components_checkbox_saved_apms__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/checkbox-saved-apms */ "./resources/js/includes/components/checkbox-saved-apms.js");










// export { default as selectSavedBankAccountsComponent } from './components/select-saved-bank-accounts';


/***/ }),

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
/*!***************************************!*\
  !*** ./resources/js/frontend/apms.js ***!
  \***************************************/
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
/* harmony import */ var _includes_wc_paydock__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../includes/wc-paydock */ "./resources/js/includes/wc-paydock.js");






// import { sleep } from '../includes/wc-paydock';

const settings = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_4__.getSetting)('paydock_apms_data', {});
const textDomain = 'paydock-for-woo';
const labels = {
  defaultLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Paydock Payments', textDomain),
  placeOrderButtonLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Place Order by Paydock', textDomain)
};
const label = (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.title) || labels.defaultLabel;
let formSubmittedAlready = false;
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
      if (settings.selectedToken.trim().length > 0) {
        return true;
      }
      if (formSubmittedAlready) {
        return true;
      }
      let result = true;
      if (result) {
        return true;
      }
      return {
        type: emitResponse.responseTypes.ERROR,
        errorMessage: labels.fillDataError
      };
    });
    const unsubscribe = onPaymentSetup(async () => {
      const paymentSourceToken = document.querySelector('input[name="payment_source_apm_token"]');
      if (paymentSourceToken === null) {
        return;
      }
      settings.paymentSourceToken = paymentSourceToken.value;
      if (settings.paymentSourceToken.length > 0 || settings.selectedToken.length > 0) {
        const data = settings;
        data.customers = '';
        data.styles = '';
        data.supports = '';
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
      validation() && unsubscribe();
    };
  }, [emitResponse.responseTypes.ERROR, emitResponse.responseTypes.SUCCESS, onPaymentSetup, onCheckoutValidation]);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('div', null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('div', null, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.description || '')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    class: 'logo-comm-bank'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: '/wp-content/plugins/paydock/assets/images/commBank_logo.png'
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('div', {
    id: 'paydockWidgetApm'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('img', {
    src: '/wp-content/plugins/paydock/assets/images/zip_money.png',
    id: 'zippay',
    class: 'btn-apm-zippay'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('img', {
    src: '/wp-content/plugins/paydock/assets/images/afterpay_icon.png',
    id: 'afterpay',
    class: 'btn-apm-afterpay'
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)('input', {
    type: 'hidden',
    name: 'payment_source_apm_token'
  }), (0,_includes_wc_paydock__WEBPACK_IMPORTED_MODULE_5__.checkboxSavedApmsComponent)());
};
const Label = props => {
  const {
    PaymentMethodLabel
  } = props.components;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PaymentMethodLabel, {
    text: label
  });
};
const PaydokApms = {
  name: 'paydock_apms_gateway',
  label: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Label, null),
  content: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  edit: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  placeOrderButtonLabel: labels.placeOrderButtonLabel,
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports
  }
};
(0,_woocommerce_blocks_registry__WEBPACK_IMPORTED_MODULE_2__.registerPaymentMethod)(PaydokApms);
})();

/******/ })()
;
//# sourceMappingURL=apms.js.map