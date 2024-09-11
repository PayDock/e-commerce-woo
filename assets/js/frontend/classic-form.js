jQuery(function ($) {
    $(document).ready(() => {
        const powerBoardHelper = {
            paymentMethod: null,
            currentForm: {
                card: null,
                wallets: {
                    apple: null,
                    google: null,
                    afterpay: null,
                    pay_pal: null,
                },
                apms: {
                    afterpay: null,
                    zip: null,
                }
            },
            defaultFormTriger: false,
            sleepSetTimeout_ctrl: null,
            form: null,
            showErrorMessage(errorMessage) {
                $('.woocommerce-notices-wrapper:first').html("");
                jQuery.post(PowerBoardAjax.url, {
                    _wpnonce: PowerBoardAjax.wpnonce_error,
                    dataType: 'html',
                    action: 'power_board_create_error_notice',
                    error: errorMessage,
                }).then(message => {
                    var doc = new DOMParser().parseFromString(message, "text/html");
                    var noticeBanner = doc.querySelectorAll("div.wc-block-components-notice-banner");
                    $('.woocommerce-notices-wrapper:first').append(noticeBanner)
                    $('html, body').animate({
                        scrollTop: ($('div.woocommerce-notices-wrapper').offset().top - 100)
                    }, 800)
                });
            },
            setFieldLikeInvalid(fieldName) {
                let element = document.getElementById(`${fieldName}_field`);

                if (element) {
                    element.classList.add("woocommerce-invalid");
                    element.classList.add("woocommerce-invalid-required-field")
                }
            },
            setFieldLikeValid(fieldName) {
                let element = document.getElementById(`${fieldName}_field`);

                if (element) {
                    element.classList.remove("woocommerce-invalid");
                    element.classList.remove("woocommerce-invalid-required-field")
                }
            },
            getFieldsList(ignoreCheckbox = false) {
                const fieldsNames = [
                    'first_name',
                    'last_name',
                    'country',
                    'address_1',
                    'city',
                    'state',
                    'postcode',
                    'email',
                    'phone'
                ];
                let result = [];
                let shippingCheckbox = $('[name="ship_to_different_address"]')
                let prefixes = [
                    'billing_',
                ]

                if (shippingCheckbox?.[0]?.checked || ignoreCheckbox) {
                    prefixes.push('shipping_')
                }

                prefixes.map((prefix) => {
                    fieldsNames.map((field) => {
                        if ('shipping_' === prefix && ['email', 'phone'].includes(field)) {
                            return;
                        }
                        result.push(`${prefix}${field}`)
                    })
                })

                return result;
            },
            isValidForm(paymentMethod) {
                this.hideFormValidationError(paymentMethod);
                let fieldList = this.getFieldsList();
                let result = true

                fieldList.map((fieldName) => {
                    let element = document.querySelector(`[name="${fieldName}"]`);

                    if (element.value) {
                        this.setFieldLikeValid(fieldName)
                    } else {
                        this.setFieldLikeInvalid(fieldName)
                        this.showFormValidationError(paymentMethod);
                        result = false;
                    }
                })
                return result;
            },
            setPaymentMethod(methodName) {
                if ('current' === methodName) {
                    this.setPaymentMethod(this.paymentMethod);
                    return;
                }
                if (!this.isValidForm(methodName)) {
                    return;
                }

                let orderButton = $('button[name="woocommerce_checkout_place_order"]')

                if (this.paymentMethod === methodName) {
                    setTimeout(() => {
                        this.paymentMethod = null
                    }, 100)
                    return;
                }
                orderButton.hide();

                this.paymentMethod = methodName;

                $(`#classic-${methodName}-error-countries`).hide();
                switch (methodName) {
                    case 'power_board_gateway':
                        this.initCardForm();
                        orderButton.show();
                        break;
                    case 'power_board_apple-pay_wallets_gateway':
                        this.initAppleForm();
                        break;
                    case 'power_board_google-pay_wallets_gateway':
                        this.initGoogleForm({});
                        break;
                    case 'power_board_afterpay_wallets_gateway':
                        this.initAvterpayV2Form();
                        break;
                    case 'power_board_pay-pal_wallets_gateway':
                        this.initPayPalForm();
                        break;
                    case 'power_board_afterpay_a_p_m_s_gateway':
                        this.initAftrepayV1Form();
                        break;
                    case 'power_board_zip_a_p_m_s_gateway':
                        this.initZipForm();
                        break;
                    default:
                        orderButton.show();
                        this.paymentMethod = null;
                }
            },
            showFormValidationError(paymentMethod) {
                if (paymentMethod) {
                    $(`#classic-${paymentMethod}`).hide();
                    $(`#classic-${paymentMethod}-error`).show();
                }
            },
            hideFormValidationError(paymentMethod) {
                if (paymentMethod) {
                    $(`#classic-${paymentMethod}`).show();
                    $(`#classic-${paymentMethod}-error`).hide();
                }
            },
            customSubmitForm(event) {
                $('.woocommerce-notices-wrapper:first').html('')
                if (('power_board_gateway' === this.paymentMethod) && !this.defaultFormTriger) {
                    event.preventDefault();
                    let config = this.getConfigs();
                    if (!((Array.isArray(config.tokens) && config.tokens.length > 0 && config.selectedToken !== "") || this.currentForm.card.isValidForm())) {
                        var invalid_fields = [];
                        this.currentForm.card.getValidationState().invalid_fields?.forEach(field => {
                            switch(field) {
                                case "card_name":
                                    invalid_fields.push("Card Name");
                                    break;
                                case "card_number":
                                    invalid_fields.push("Card Number");
                                    break;
                                case "expiry_date":
                                    invalid_fields.push("Expiry Date");
                                    break;
                                case "card_ccv":
                                    invalid_fields.push("Card CCV");
                                    break;
                            }
                        });
                        this.showErrorMessage('Please fill in the required credit card form fields' + (invalid_fields.length ? `: ${invalid_fields.join(", ")}` : ""));

                    } else {
                        this.currentForm.card.trigger(window.cba.TRIGGER.SUBMIT_FORM);
                    }
                }
            },
            initCardForm() {
                $("#power-board-3ds-container").hide()
                const config = this.getConfigs();

                let isPermanent = config.hasOwnProperty('card3DSFlow')
                    && ("SESSION_VAULT" === config.card3DSFlow) && (
                        config.hasOwnProperty('card3DS')
                        && 'DISABLE' !== config.card3DS
                    )
                let gatewayId = isPermanent ? config.gatewayId : 'not_configured';

                this.currentForm.card = new cba.HtmlWidget('#classic-power_board_gateway', config.publicKey, gatewayId, "card", "card_payment_source_with_cvv");
                this.currentForm.card.setFormPlaceholders({
                    card_name: 'Card holders name *',
                    card_number: 'Credit card number *',
                    expire_month: 'MM/YY *',
                    card_ccv: 'CCV *',
                })

                if (config.hasOwnProperty('styles')) {
                    this.currentForm.card.setStyles(config.styles);
                }

                if (config.hasOwnProperty('styles') && typeof config.styles.custom_elements !== "undefined") {
                    $.each(config.styles.custom_elements, function (element, styles) {
                        this.currentForm.card.setElementStyle(element, styles);
                    });
                }

                if (config.hasOwnProperty('styles') && config.cardSupportedCardTypes !== '') {
                    let supportedCard = config.cardSupportedCardTypes.replaceAll(' ', '').split(',')
                    this.currentForm.card.setSupportedCardIcons(supportedCard, true);
                }

                this.currentForm.card.setEnv(config.isSandbox ? 'preproduction_cba' : 'production_cba');
                this.currentForm.card.setFormFields(["card_name*","card_number*", "card_ccv*"]);
                this.currentForm.card.onFinishInsert('#classic-power_board_gateway-token', 'payment_source');
                this.currentForm.card.interceptSubmitForm('#widget');

                this.currentForm.card.load();

                this.currentForm.card.on(window.cba.EVENT.AFTER_LOAD, () => {
                    this.currentForm.card.hideElements(['submit_button']);
                })
                this.currentForm.card.on(window.cba.EVENT.FINISH, () => {
                    switch (config.card3DS) {
                        case 'IN_BUILD':
                            this.init3DSInBuilt(config)
                            break;
                        case 'STANDALONE':
                            this.init3DSStandalone(config)
                            break;
                        default:
                            this.form.submit()
                    }
                })

                $('#select-saved-cards').on('change', (event) => {
                    let value = event.target.value
                    let widgetform = $('#classic-power_board_gateway-wrapper')
                    let checkbox = $('#card_save_card').parent()

                    widgetform.hide()
                    if (!value) {
                        widgetform.show()
                        checkbox.show()
                        this.defaultFormTriger = false;
                    } else {
                        widgetform.hide()
                        checkbox.hide()
                        this.defaultFormTriger = true;
                    }
                    $('#power-board-selected-token').val(value)
                })
            },
            async init3DSInBuilt(config) {
                if (config.selectedToken.trim().length === 0 && config.card3DSFlow === 'PERMANENT_VAULT') {
                    config.selectedToken = await this.getVaultToken(config);
                }

                $('#power-board-selected-token').val(config.selectedToken)

                let address = this.getAddressData(false);
                const preAuthData = {
                    amount: config.amount,
                    currency: config.currency,
                    customer: {
                        first_name: address.address.first_name,
                        last_name: address.address.last_name,
                        email: address.address.email,
                        payment_source: {
                            address_country: address.address.country,
                            address_state: address.address.state,
                            address_city: address.address.city,
                            address_postcode: address.address.postcode,
                            address_line1: address.address.address_1,
                        }
                    },
                    shipping: {
                        address_country: address.shipping_address.country,
                        address_state: address.shipping_address.state,
                        address_city: address.shipping_address.city,
                        address_postcode: address.shipping_address.postcode,
                        address_line1: address.shipping_address.address_1,
                        contact: {
                            first_name: address.shipping_address.first_name,
                            last_name: address.shipping_address.last_name,
                            email: address.shipping_address.email ?? address.address.email,
                        }
                    }
                };

                if (config.card3DSFlow === 'PERMANENT_VAULT') {
                    preAuthData.customer.payment_source.vault_token = config.selectedToken;
                    preAuthData.customer.payment_source.gateway_id = config.gatewayId;
                } else {
                    preAuthData.token = $('#classic-power_board_gateway-token').val()
                }
                const envVal = config.isSandbox ? 'preproduction_cba' : 'production_cba'
                const preAuthResp = await new window.cba.Api(config.publicKey)
                    .setEnv(envVal)
                    .charge()
                    .preAuth(preAuthData);

                if (typeof preAuthResp._3ds.token === "undefined") {
                    return false;
                }

                $("#power-board-3ds-container").show()
                $('#classic-power_board_gateway').hide()

                const canvas = new window.cba.Canvas3ds("#power-board-3ds-container", preAuthResp._3ds.token);
                canvas.load();

                canvas.on('chargeAuth', (chargeAuthSuccessEvent) => {
                    $('#charge3dsid').val(chargeAuthSuccessEvent.charge_3ds_id)
                    this.form.submit()
                })
            },
            async getVaultToken(config) {
                data = {...config}
                data.paymentSourceToken = $('#classic-power_board_gateway-token').val()
                data.action = 'power_board_get_vault_token';
                data._wpnonce = PowerBoardAjax.wpnonce_3ds;
                data.tokens = '';
                data.styles = '';
                data.supports = '';

                let checkbox = document.getElementById("card_save_card");

                if (checkbox.checked) {
                    data.cardsavecardchecked = 'true'
                }

                return jQuery.post(PowerBoardAjax.url, data).then();
            },

            async sleep(ms) {
                clearInterval(this.sleepSetTimeout_ctrl);
                return new Promise(resolve => this.sleepSetTimeout_ctrl = setTimeout(resolve, ms));
            },
            async getStandalone3dsToken(config) {
                const data = {...config};
                data.action = 'power_board_get_vault_token';
                data.paymentSourceToken = $('#classic-power_board_gateway-token').val()
                data.type = 'standalone-3ds-token';
                data._wpnonce = PowerBoardAjax.wpnonce_3ds;
                let address = this.getAddressData(false);
                if (address.address.first_name !== null) {
                    data.first_name = address.address.first_name
                }
                if (address.address.last_name !== null) {
                    data.last_name = address.address.first_name
                }
                if (address.address.phone !== null) {
                    data.phone = address.address.phone
                }
                if (address.address.email !== null) {
                    data.email = address.address.email
                }

                data.tokens = '';
                data.styles = '';
                data.supports = '';

                return jQuery.post(PowerBoardAjax.url, data).then();
            },
            async init3DSStandalone(config) {
                if (config.selectedToken.trim().length === 0) {
                    config.selectedToken = await this.getVaultToken(config)
                }

                $('#power-board-selected-token').val(config.selectedToken)

                const threeDsToken = await this.getStandalone3dsToken(config)


                const canvas = new window.cba.Canvas3ds("#power-board-3ds-container", threeDsToken);
                canvas.setEnv(config.isSandbox ? 'preproduction_cba' : 'production_cba');

                canvas.on('chargeAuthSuccess', (data) => {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                })
                canvas3ds.on('chargeAuthReject', function (data) {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                });
                canvas3ds.on('chargeAuthCancelled', function (data) {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                });
                canvas3ds.on('additionalDataCollectSuccess', function (data) {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                });
                canvas3ds.on('additionalDataCollectReject', function (data) {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                });
                canvas3ds.on('chargeAuth', function (data) {
                    $('#charge3dsid').val(data.charge_3ds_id)
                    this.form.submit()
                });

                canvas.load()
            },
            initGoogleForm() {
                const type = 'google-pay';
                if ('power_board_google-pay_wallets_gateway' !== this.paymentMethod) {
                    delete this.currentForm.wallets.google;
                    this.currentForm.wallets.google = null;
                }
                const config = this.getConfigs(`${type}_wallets`)
                this.initWallet(type, config.isSandbox, {
                    amount_label: 'Total'
                })
            },
            initAppleForm() {
                const type = 'apple-pay';
                if ('power_board_apple-pay_wallets_gateway' !== this.paymentMethod) {
                    delete this.currentForm.wallets.apple;
                    this.currentForm.wallets.apple = null;
                }
                const config = this.getConfigs(`${type}_wallets`)
                this.initWallet(type, config.isSandbox, {
                    amount_label: 'Total',
                    wallets: ['apple']
                })
            },
            initAvterpayV2Form() {
                const type = 'afterpay';
                if ('power_board_afterpay_wallets_gateway' !== this.paymentMethod) {
                    delete this.currentForm.wallets.afterpay;
                    this.currentForm.wallets.afterpay = null;
                }
                const config = this.getConfigs(`${type}_wallets`)
                this.initWallet(type, config.isSandbox, {})
            },
            initPayPalForm() {
                const type = 'pay-pal';
                if ('power_board_pay-pal_wallets_gateway' !== this.paymentMethod) {
                    delete this.currentForm.wallets.pay_pal;
                    this.currentForm.wallets.pay_pal = null;
                }
                const config = this.getConfigs(`${type}_wallets`)
                this.initWallet(type, config.isSandbox, {
                    pay_later: config?.pay_pal_smart_button?.payLater?.toLowerCase() === 'yes'
                })
            },
            initAftrepayV1Form() {
                const countries = ['au', 'nz', 'us', 'ca', 'uk', 'gb', 'fr', 'it', 'es', 'de'];

                let countriesErrorElement = $('#classic-power_board_afterpay_a_p_m_s_gateway-error-countries');
                let button = $('#classic-power_board_afterpay_a_p_m_s_gateway')

                if (!countries.includes(this.getAddressData(false)?.address?.country?.toLowerCase())) {
                    countriesErrorElement.show();
                    button.hide()
                    return;
                }

                countriesErrorElement.hide();
                button.show()

                let settings = this.getConfigs('afterpay_a_p_m_s')
                this.currentForm.apms.afterpay = new window.cba.AfterpayCheckoutButton(
                    '#classic-power_board_afterpay_a_p_m_s_gateway',
                    settings.publicKey,
                    settings.gatewayId
                )

                this.initAPM('afterpay', settings)
            },
            initZipForm() {
                const countries = ['au', 'nz', 'us', 'ca'];
                let countriesErrorElement = $('#classic-power_board_zip_a_p_m_s_gateway-error-countries');
                let button = $('#classic-power_board_zip_a_p_m_s_gateway')

                if (!countries.includes(this.getAddressData(false)?.address?.country?.toLowerCase())) {
                    countriesErrorElement.show();
                    button.hide()
                    return;
                }

                countriesErrorElement.hide();
                button.show()

                let settings = this.getConfigs('zip_a_p_m_s')
                this.currentForm.apms.zip = new window.cba.ZipmoneyCheckoutButton(
                    '#classic-power_board_zip_a_p_m_s_gateway',
                    settings.publicKey,
                    settings.gatewayId
                )
                this.initAPM('zip', settings)
            },
            getAddressData(returnJson = true) {
                let fieldList = this.getFieldsList(true);

                let result = {
                    shipping_address: {},
                    address: {}
                };

                fieldList.map((fieldName) => {
                    let type = 'input';
                    if (fieldName.includes('state') || fieldName.includes('country')) {
                        type = 'select'
                    }

                    let value = $(`${type}[name="${fieldName}"]`)[0]?.value

                    let isShipping = fieldName.includes('shipping');
                    if (isShipping && !value) {
                        let billingFieldName = fieldName.replace('shipping', 'billing')
                        value = $(`input[name="${billingFieldName}"]`)[0]?.value
                    }

                    result[isShipping ? 'shipping_address' : 'address'][fieldName.replace('shipping_', '').replace('billing_', '')] = value;
                })
                if (returnJson) {
                    return JSON.stringify(result);
                }
                return result;
            },
            getConfigs(type = null) {
                if (type) {
                    type += '_'
                } else {
                    type = ''
                }
                let settings = $(`#classic-power_board_${type}gateway-settings`).val()
                settings = JSON.parse(settings)

                return settings;
            },
            initWallet(type, isSandbox, config = {}) {
                jQuery.ajax({
                    url: '/?wc-ajax=power-board-create-wallet-charge',
                    type: 'POST',
                    data: {
                        _wpnonce: PowerBoardAjax.wpnonce,
                        type: type,
                        address: this.getAddressData()
                    },
                    success: (response) => {
                        config.country = response?.data?.data?.county
                        const index = type.replace('pay', '');
                        document.getElementById(`classic-power_board_${type}_wallets_gateway`).innerHTML = '';

                        this.currentForm.wallets[index] = new window.cba.WalletButtons(
                            `#classic-power_board_${type}_wallets_gateway`,
                            response?.data?.data?.resource?.data?.token,
                            config
                        );

                        this.currentForm.wallets[index].setEnv(isSandbox ? 'preproduction_cba' : 'production_cba')

                        this.currentForm.wallets[index].onPaymentError((data) => {
                            this.form.submit()
                        });

                        let paymentSourceElement = $(`#classic-power_board_${type}_wallets_gateway-token`);

                        this.currentForm.wallets[index].onPaymentSuccessful((result) => {
                            paymentSourceElement.val(JSON.stringify(result))
                            this.form.submit()
                        })

                        this.currentForm.wallets[index].onPaymentInReview((result) => {
                            paymentSourceElement.val(JSON.stringify(result))

                            this.form.submit()
                        });

                        this.currentForm.wallets[index].load();
                    }
                });
            },
            initAPM(type, settings) {
                let meta = {}
                let address = this.getAddressData(false);
                if ('afterpay' === type) {
                    meta = {
                        amount: settings.amount,
                        currency: settings.currency,
                        email: address.address.email,
                        first_name: address.address.first_name,
                        last_name: address.address.last_name,
                        address_line: address.address.address_1,
                        address_line2: address.address.address_2,
                        address_city: address.address.city,
                        address_state: address.address.state,
                        address_postcode: address.address.postcode,
                        address_country: address.address.country,
                        phone: address.address.phone
                    }
                }
                meta.charge = {
                    amount: settings.amount,
                    currency: settings.currency,
                    email: address.address.email,
                    first_name: address.address.first_name,
                    last_name: address.address.last_name,
                    shipping_address: {
                        first_name: address.shipping_address.first_name,
                        last_name: address.shipping_address.last_name,
                        line1: address.shipping_address.address_1,
                        line2: address.shipping_address.address_2,
                        country: address.shipping_address.country,
                        postcode: address.shipping_address.postcode,
                        city: address.shipping_address.city,
                        state: address.shipping_address.state
                    },
                    billing_address: {
                        first_name: address.address.first_name,
                        last_name: address.address.last_name,
                        line1: address.address.address_1,
                        line2: address.address.address_2,
                        country: address.address.country,
                        postcode: address.address.postcode,
                        city: address.address.city,
                        state: address.address.state
                    },
                    items: settings.items
                }

                this.currentForm.apms[type].setEnv(settings.isSandbox ? 'preproduction_cba' : 'production_cba')
                this.currentForm.apms[type].setMeta(meta)
                this.currentForm.apms[type].onFinishInsert(`#classic-power_board_${type}_a_p_m_s_gateway-token`, 'payment_source_token');
                this.currentForm.apms[type].on('finish', () => {
                    this.form.submit()
                })
            },
            init() {
                const orderBtnInterval = setInterval(() => {
                    let orderButton = document.getElementById('place_order');
                    if (orderButton) {
                        clearInterval(orderBtnInterval)
                        orderButton.addEventListener('click', (event) => {
                            this.customSubmitForm(event)
                        })
                    }
                }, 100)

                const paymentMethodInterval = setInterval(() => {
                    let paymentMethod = $('input[name="payment_method"]:checked').val();
                    if (paymentMethod && !this.paymentMethod) {
                        clearInterval(paymentMethodInterval)
                        this.setPaymentMethod(paymentMethod)
                    }
                }, 100)

                this.form = $('form[name="checkout"]');

                this.form.on('change', () => {
                    try {
                        this.setPaymentMethod($('input[name="payment_method"]:checked').val())
                    } catch (e) {
                        console.error(e)
                    }
                })

                this.form.submit((event) => {
                    this.customSubmitForm(event)
                });

                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            },
        }
        setTimeout(() => {
            powerBoardHelper.init()
        }, 2000)
    });
})
