<?php
namespace Paydock\Util;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Paydock\API\ConfigService;
use Paydock\Services\CheckoutPaymentService;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;

final class PaydockGatewayBlocks extends AbstractPaymentMethodType
{

    private $gateway;
    protected $name = 'paydock'; // your payment gateway name

    public function initialize()
    {
        $this->settings = get_option('woocommerce_paydock_settings', []);
        $this->gateway = new CheckoutPaymentService();
    }

    public function is_active()
    {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
        $script_path = 'assets/build/js/frontend/blocks.js';
        $script_asset_path = PAY_DOCK_PLUGIN_PATH . '/assets/build/js/frontend/blocks.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require($script_asset_path)
            : array(
                'dependencies' => array(),
                'version' => PAY_DOCK_PLUGIN_VERSION
            );
        $script_url = PAY_DOCK_PLUGIN_URL . $script_path;

        wp_register_script(
            'paydock_gateway',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('paydock_gateway');

        }
        return ['paydock_gateway'];
    }


    public function get_payment_method_data()
    {
        SDKAdapterService::getInstance();
        $settingsService = SettingsService::getInstance();

        return [
            'title' => $settingsService->getWidgetTitle(),
            'description' => $settingsService->getWidgetDescription(),
            'paymentCardTitle' => $settingsService->getWidgetPaymentCardTitle(),
            'paymentCardDescription' => $settingsService->getWidgetPaymentCardDescription(),
            'cardDirectCharge' => $settingsService->getCardDirectCharge(),
            'cardSaveCard' => $settingsService->getCardSaveCard(),
            'styles' => $settingsService->getWidgetStyles(),
            'publicKey' => ConfigService::$publicKey,
            'gatewayId' => $settingsService->getCardGatewayId(),
            'supports' => array_filter($this->gateway->supports, [$this->gateway, 'supports'])
        ];
    }

}
