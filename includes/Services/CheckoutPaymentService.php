<?php

namespace Paydock\Services;

use Paydock\Enums\SettingsTabs;
use Paydock\Enums\WidgetSettings;
use WC_Payment_Gateway;
use Exception;
use Paydock\Repositories\LogRepository;

class CheckoutPaymentService extends WC_Payment_Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'paydock_gateway';
        $this->icon = apply_filters('woocommerce_paydock_gateway_icon', '');
        $this->has_fields = true;
        $this->supports = array(
            'products',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_amount_changes',
            'subscription_date_changes',
            'multiple_subscriptions',
            'default_credit_card_form'
        );

        $this->method_title = _x('Paydock payment', 'Paydock payment method', 'woocommerce-gateway-paydock');
        $this->method_description = __('Allows Paydock payments.', 'woocommerce-gateway-paydock');

        // Load the settings.
        $this->init_settings();

        // Define user set variables.
        $service = SettingsService::getInstance();
        $keyTitle = $service->getOptionName(SettingsTabs::WIDGET()->value, [WidgetSettings::PAYMENT_CARD_TITLE()->name]);
        $keyDescription = $service->getOptionName(SettingsTabs::WIDGET()->value, [WidgetSettings::PAYMENT_CARD_DESCRIPTION()->name]);

        $this->title = get_option('woocommerce_pay_dock_widget_pay_dock_widget_PAYMENT_CARD_TITLE');
        $this->description = get_option($keyDescription);
        // Actions.
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_scheduled_subscription_payment_paydock', array($this, 'process_subscription_payment'), 10, 2);

        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

        add_action('woocommerce_after_checkout_billing_form', array($this, 'woocommerce_before_checkout_form'), 10, 1);
    }

    /**
     * Check If The Gateway Is Available For Use
     *
     * @access public
     * @return bool
     */
    function is_available()
    {
        return true;
    }

    public function payment_scripts()
    {
        if (!is_checkout() || !$this->is_available()) {
            return '';
        }
        $sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.js';
//        $sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.min.js';
        $sdkUrl = preg_replace('{version}', SettingsService::getInstance()->getVersion(), $sdkUrl);

        wp_enqueue_script('paydock-form', PAY_DOCK_PLUGIN_URL . '/assets/js/frontend/form.js', array(), time(), true);
        $paydockCardWidgetSettings = array(
            'suportedCard' => 'Visa, Mastercard, Adex'
        );
        wp_localize_script('paydock-form', 'paydockCardWidgetSettings', $paydockCardWidgetSettings);
        wp_enqueue_style('paydock-widget-css', PAY_DOCK_PLUGIN_URL . '/assets/css/frontend/widget.css', array(), time());

        wp_enqueue_script('paydock-api', $sdkUrl, array(), time(), true);
        return "";
    }

    /**
     * Process the payment and return the result.
     *
     * @since 1.0.0
     */

    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        $order = wc_get_order($order_id);

        // $siteName = remove_accents(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
        // $description = sprintf(__('Order №%s from %s.', 'paydock-for-woocommerce'), $order->get_order_number(), $siteName);

        $loggerRepository = new LogRepository();

        try {
            $token = !empty($_POST['paymentsourcetoken']) ? $_POST['paymentsourcetoken'] : null;
            $gatewayId = !empty($_POST['gatewayid']) ? $_POST['gatewayid'] : null;
            $cardDirectCharge = !empty($_POST['carddirectcharge']) ? $_POST['carddirectcharge'] : false;
            $cardSaveCard = !empty($_POST['cardsavecard']) ? $_POST['cardsavecard'] : false;

            if (empty($token) || empty($gatewayId)) {
                throw new Exception(__('The token wasn\'t generated correctly.', PAY_DOCK_TEXT_DOMAIN));
            }

            $SDKAdapterService = SDKAdapterService::getInstance();

            $vaultTokenData = [
                'token' => $token
            ];

            if (!$cardSaveCard) {
                $vaultTokenData['vault_type'] = 'session';
            }

            $responce = $SDKAdapterService->createVaultToken($vaultTokenData);

            if (!empty($responce['error']) || empty($responce['resource']['data']['vault_token'])) {
                $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
                throw new Exception(__('Can\'t create Paydock vault token.' . $message, PAY_DOCK_TEXT_DOMAIN));
            }

            $vaultToken = $responce['resource']['data']['vault_token'];

            if ($cardDirectCharge) {
                $responce = $SDKAdapterService->createCharge([
                    'amount' => (float)$order->get_total(),
                    'currency' => strtoupper(get_woocommerce_currency()),
                    'customer' => [
                        'payment_source' => [
                            'vault_token' => $vaultToken,
                            'gateway_id' => $gatewayId
                        ]
                    ]
                ]);
            } else {
                $responce = $SDKAdapterService->createCustomer([
                    'payment_source' => [
                        'vault_token' => $vaultToken
                    ]
                ]);

                if (!empty($responce['error']) || empty($responce['resource']['data']['_id'])) {
                    $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
                    throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
                }

                $customerId = $responce['resource']['data']['_id'];
                $responce = $SDKAdapterService->createCharge([
                    'amount' => (float)$order->get_total(),
                    'currency' => strtoupper(get_woocommerce_currency()),
                    'customer_id' => $customerId,
                    'customer' => [
                        'payment_source' => [
                            'gateway_id' => $gatewayId
                        ]
                    ]
                ]);
            }

            $chargeId = !empty($responce['resource']['data']['_id']) ? $responce['resource']['data']['_id'] : '';

            if (!empty($responce['error'])) {
                $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
                throw new Exception(__('Can\'t create Paydock charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
            }

        } catch (Exception $e) {
            wc_add_notice(__('Error:', PAY_DOCK_TEXT_DOMAIN) . ' ' . $e->getMessage(), 'error');

            $loggerRepository->createLogRecord($chargeId ?? '', 'Charges', 'UnfulfilledCondition', $e->getMessage(), LogRepository::ERROR);

            return [];
        }

        $order->payment_complete();
        $order->update_status('Paid with Paydock');
        WC()->cart->empty_cart();

        if ($chargeId !== null) {
            $loggerRepository->createLogRecord($chargeId, 'Charges', 'Completed', '', LogRepository::SUCCESS);
        }

        return [
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
