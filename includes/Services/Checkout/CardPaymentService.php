<?php

namespace Paydock\Services\Checkout;

use Exception;
use Paydock\Enums\SettingsTabs;
use Paydock\Enums\WidgetSettings;
use Paydock\Repositories\LogRepository;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\ProcessPayment\CardProcessor;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;
use WC_Payment_Gateway;

class CardPaymentService extends WC_Payment_Gateway
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

        $this->title = get_option($keyTitle);
        $this->description = get_option($keyDescription);
        // Actions.
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_scheduled_subscription_payment_paydock', array($this, 'process_subscription_payment'), 10, 2);

        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

        add_action('wp_ajax_get_vault_token', array($this, 'get_vault_token'));

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
        return SettingsService::getInstance()->isCardEnabled();
    }

    public function payment_scripts()
    {
        if (!is_checkout() || !$this->is_available()) {
            return '';
        }

        $version = SettingsService::getInstance()->getVersion();
        $sdkUrl = "https://widget.paydock.com/sdk/{$version}/widget.umd.min.js";

        wp_enqueue_script('paydock-form', PAY_DOCK_PLUGIN_URL . '/assets/js/frontend/form.js', array(), time(), true);
        $paydockCardWidgetSettings = array(
            'suportedCard' => 'Visa, Mastercard, Adex'
        );
        wp_localize_script('paydock-form', 'paydockCardWidgetSettings', $paydockCardWidgetSettings);
        wp_enqueue_style('paydock-widget-css', PAY_DOCK_PLUGIN_URL . '/assets/css/frontend/widget.css', array(), time());

        wp_enqueue_script('paydock-api', $sdkUrl, array(), time(), true);

        wp_localize_script('paydock-api', 'PaydockAjax', [
            'url' => admin_url('admin-ajax.php')
        ]);

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

        $siteName = remove_accents(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
        $description = sprintf(__('Order №%s from %s.', 'paydock-for-woocommerce'), $order->get_order_number(), $siteName);

        $loggerRepository = new LogRepository();
        $chargeId = '';

        try {
            $cardProcessor = new CardProcessor(array_merge([
                'amount' => (float) $order->get_total(),
                'description' => $description
            ], $_POST));

            $response = $cardProcessor->run();

            if (!empty($response['error'])) {
                $message = SDKAdapterService::getInstance()->errorMessageToString($response);
                throw new Exception(__('Can\'t charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
            }

            $chargeId = !empty($response['resource']['data']['_id']) ? $response['resource']['data']['_id'] : '';
        } catch (Exception $e) {
            $loggerRepository->createLogRecord($chargeId ?? '', 'Charges', 'UnfulfilledCondition', $e->getMessage(), LogRepository::ERROR);
            wc_add_notice(__('Error:', PAY_DOCK_TEXT_DOMAIN) . ' ' . $e->getMessage(), 'error');
            exit;
        }

        try {
            $cardProcessor->createCustomer();
        } catch (Exception $e) {
            $loggerRepository->createLogRecord($chargeId ?? '', 'Create customer after charge', 'UnfulfilledCondition', $e->getMessage(), LogRepository::ERROR);
            wc_add_notice(__('Error:', PAY_DOCK_TEXT_DOMAIN) . ' ' . $e->getMessage(), 'error');
            exit;
        }

        $status = ucfirst(strtolower($response['resource']['data']['transactions'][0]['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['type'] ?? 'undefined'));

        $isCompleted = 'complete' === strtolower($status);

        $order->set_status($isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending');
        $order->payment_complete();
        $order->save();

        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $isCompleted ? LogRepository::DEFAULT : LogRepository::SUCCESS);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    /**
     * Ajax function
     */
    public function get_vault_token(): void
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $cardProcessor = new CardProcessor($_POST);
            $type = !empty($_POST['type']) ? $_POST['type'] : null;
            try {

                switch ($type) {
                    case 'clear-user-tokens':
                        (new UserTokenRepository())->deleteAllUserTokens();
                        break;
                    case 'standalone-3ds-token':
                        echo $cardProcessor->getStandalone3dsToken();
                        break;
                    default:
                        echo $cardProcessor->getVaultToken();
                }
            } catch (Exception $e) {
                (new LogRepository())->createLogRecord('', 'Charges', 'UnfulfilledCondition', $e->getMessage(), LogRepository::ERROR);
                wc_add_notice(__('Error:', PAY_DOCK_TEXT_DOMAIN) . ' ' . $e->getMessage(), 'error');
            }

        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    public function webhook()
    {

    }
}
