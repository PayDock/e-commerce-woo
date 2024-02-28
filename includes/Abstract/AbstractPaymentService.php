<?php

namespace Paydock\Abstract;

use Paydock\Services\SettingsService;
use WC_Payment_Gateway;

abstract class AbstractPaymentService extends WC_Payment_Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->icon = apply_filters('woocommerce_paydock_gateway_icon', '');
        $this->has_fields = true;
        $this->supports = array(
            'products', 'subscriptions', 'subscription_cancellation', 'subscription_suspension',
            'subscription_reactivation', 'subscription_amount_changes', 'subscription_date_changes',
            'multiple_subscriptions', 'default_credit_card_form'
        );

        $this->method_title = _x('Paydock payment', 'Paydock payment method', 'woocommerce-gateway-paydock');
        $this->method_description = __('Allows Paydock payments.', 'woocommerce-gateway-paydock');

        $this->init_settings();
    }


    public function woocommerce_before_checkout_form($arg){
        
    }

    public function payment_scripts()
    {
        if (!is_checkout() || !$this->is_available()) {
            return '';
        }
        //$sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.js';
        $sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.min.js';
        $sdkUrl = preg_replace('{version}', SettingsService::getInstance()->getVersion(), $sdkUrl);

        wp_enqueue_script('paydock-form', PAY_DOCK_PLUGIN_URL.'/assets/js/frontend/form.js', array(), time(), true);
        wp_enqueue_style('paydock-widget-css', PAY_DOCK_PLUGIN_URL.'/assets/css/frontend/widget.css', array(), time());

        wp_enqueue_script('paydock-api', $sdkUrl, array(), time(), true);
    }
}
