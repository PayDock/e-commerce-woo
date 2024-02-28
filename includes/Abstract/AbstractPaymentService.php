<?php

namespace PowerBoard\Abstract;

use PowerBoard\Services\SettingsService;
use WC_Payment_Gateway;

abstract class AbstractPaymentService extends WC_Payment_Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->icon = apply_filters('woocommerce_power_board_gateway_icon', '');
        $this->has_fields = true;
        $this->supports = array(
            'products', 'subscriptions', 'subscription_cancellation', 'subscription_suspension',
            'subscription_reactivation', 'subscription_amount_changes', 'subscription_date_changes',
            'multiple_subscriptions', 'default_credit_card_form'
        );

        $this->method_title = _x('PowerBoard payment', 'PowerBoard payment method', 'woocommerce-gateway-power_board');
        $this->method_description = __('Allows PowerBoard payments.', 'woocommerce-gateway-power_board');

        $this->init_settings();
    }


    public function woocommerce_before_checkout_form($arg){
        
    }

    public function payment_scripts()
    {
        if (!is_checkout() || !$this->is_available()) {
            return '';
        }
        $sdkUrl =SettingsService::getInstance()->getWidgetScriptUrl();

        wp_enqueue_script('power_board-form', POWER_BOARD_PLUGIN_URL.'/assets/js/frontend/form.js', array(), time(), true);
        wp_enqueue_style('power_board-widget-css', POWER_BOARD_PLUGIN_URL.'/assets/css/frontend/widget.css', array(), time());

        wp_enqueue_script('power_board-api', $sdkUrl, array(), time(), true);
    }
}
