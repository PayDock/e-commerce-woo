jQuery(document).ready(function () {
    var body = jQuery('body');

    //maping data
    let requiredFilds = {
        'billing_first_name' : 'first_name',
        'billing_last_name'  : 'last_name',
        'billing_state'  : 'address_state',
        'billing_city': 'address_city',
        'billing_postcode': 'address_postcode',
        'billing_email': 'email',
        'billing_country': 'address_country'
    };

    if (power_board_object.gateways.creditCard === 'yes') {
        // PowerBoard Credit Card gateway
        var power_board_cc = new power_board.HtmlWidget('#power_board_cc', power_board_object.publicKey, power_board_object.creditGatewayId);

        if (power_board_object.sandbox == true) {
            power_board_cc.setEnv('sandbox');
        } else {
            power_board_cc.setEnv('production');
        }

        if (power_board_object.cc_email === true) {
            power_board_cc.setFormFields(['email']);
        }

        power_board_cc.interceptSubmitForm('#power_board_cc');

        power_board_cc.onFinishInsert('input[name="payment_source"]', 'payment_source');

        power_board_cc.setStyles({
            font_size: '12px',
            background_color: 'rgb(255, 255, 255)'
        });

        power_board_cc.on('finish', function (data) {
            jQuery('input[name="power_board_gateway"]').val('credit_card');
            jQuery('#place_order').submit();
        });

        power_board_cc.load();


        //set value "requiredFilds" in widget
        Object.keys(requiredFilds).forEach(function(key) {
            let _this = jQuery("#" + key);
            let data = [];

            data[requiredFilds[key]] =  _this.val();
            power_board_cc.setFormValues(data);

            switch (_this.prop("tagName"))  {
                case 'INPUT':
                    _this.keyup( function(){
                        let data = [];

                        data[requiredFilds[key]] =  jQuery(this).val() ;
                        power_board_cc.setFormValues(data);
                    });
                    break;
                case 'SELECT':
                    _this.change( function(){
                        let data = [];
                        data[requiredFilds[key]] =  jQuery(this).val() ;
                        power_board_cc.setFormValues(data);
                    });
                    break;
            }
        });
    }

    if (power_board_object.gateways.directDebit === 'yes') {
        // PowerBoard Direct Debit gateway
        var power_board_dd = new power_board.HtmlWidget('#power_board_dd', power_board_object.publicKey, power_board_object.debitGatewayId, 'bank_account');
        if (power_board_object.sandbox == true) {
            power_board_dd.setEnv('sandbox');
        } else {
            power_board_dd.setEnv('production');
        }

        power_board_dd.setStyles({
            font_size: '12px',
            background_color: 'rgb(255, 255, 255)'
        });

        power_board_dd.setFormFields(['account_bsb']);

        power_board_dd.interceptSubmitForm('#power_board_dd');

        power_board_dd.onFinishInsert('input[name="payment_source"]', 'payment_source');

        power_board_dd.on('finish', function (data) {
            jQuery('input[name="power_board_gateway"]').val('direct_debit');
            jQuery('#place_order').submit();
        });

        power_board_dd.load();
    }

    body.on('updated_checkout', function () {
        if (power_board_object.gateways.creditCard === 'yes') {
            power_board_cc.reload();
        }

        if (power_board_object.gateways.directDebit === 'yes') {
            power_board_dd.reload();
        }
    });

    body.on('click', '#place_order', function (e) {
        if (jQuery('.woocommerce-checkout').find('#payment_method_power_board').attr('checked') === 'checked') {
            e.preventDefault();

            jQuery('html, body').animate({
                scrollTop: jQuery(".power_board-tab-wrap").offset().top
            }, 500);

            var gateway = jQuery(".power_board-tab:checked").data('gateway');

            switch (gateway) {
                case 'credit_card':
                    power_board_cc.trigger('submit_form');
                    break;
                case 'direct_debit':
                    power_board_dd.trigger('submit_form');
                    break;
                case 'paypal_express':
                    jQuery('#power_board-paypal-express').trigger('click');
                    break;
                case 'zip_money':
                    jQuery('#zip-money-button').trigger('click');
                    break;
                case 'afterpay':
                    jQuery('#afterpay-button').trigger('click');
                    break;
                default:
                    return '';
            }
        }
    });

    body.on('click', '.zip-money-tab', function (e) {
        jQuery(this).find('#zip-money-button').get(0).click();
    });

    body.on('click', '.paypal-express-tab', function (e) {
        jQuery(this).find('#power_board-paypal-express').get(0).click();
    });

    body.on('click', '.afterpay-tab', function (e) {
        jQuery(this).find('#afterpay-button').get(0).click();
    });
});