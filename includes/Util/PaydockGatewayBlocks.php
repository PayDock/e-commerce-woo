<?php

namespace Paydock\Util;

use Paydock\Abstract\AbstractBlock;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Paydock\API\ConfigService;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\Checkout\CardPaymentService;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;

final class PaydockGatewayBlocks extends AbstractBlock
{
    protected const SCRIPT = 'blocks';

    private CardPaymentService $gateway;
    protected $name = 'paydock';

    public function initialize()
    {
        $this->settings = get_option('woocommerce_paydock_settings', []);
        $this->gateway = new CardPaymentService();
    }

    public function is_active()
    {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
        $script_path = 'assets/build/js/frontend/' . self::SCRIPT . '.js';
        $script_asset_path = PAY_DOCK_PLUGIN_PATH . '/assets/build/js/frontend/' . self::SCRIPT . '.asset.php';
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
        $userTokens = [];
        if (is_user_logged_in()) {
            $userTokens['tokens'] = (new UserTokenRepository)->getUserTokens();
        }
        
        return array_merge($userTokens, [
            // Wordpress data
            'isUserLoggedIn' => is_user_logged_in(),
            'isSandbox' => $settingsService->isSandbox(),
            // Woocommerce data
            'amount' => WC()->cart->total,
            'currency' => strtoupper(get_woocommerce_currency()),
            // Widget
            'title' => $settingsService->getWidgetPaymentCardTitle(),
            'description' => $settingsService->getWidgetPaymentCardDescription(),
            'styles' => $settingsService->getWidgetStyles(),
            // Tokens & keys
            'publicKey' => ConfigService::$publicKey,
            'selectedToken' => '',
            'paymentSourceToken' => '',
            // Card
            'cardSupportedCardTypes' => $settingsService->getCardSupportedCardTypes(),
            'gatewayId' => $settingsService->getCardGatewayId(),
            // 3DS
            'card3DS' => $settingsService->getCard3DS(),
            'card3DSServiceId' => $settingsService->getCard3DSServiceId(),
            'card3DSFlow' => $settingsService->getCardTypeExchangeOtt(),
            'charge3dsId' => '',
            // Fraud
            'cardFraud' => $settingsService->getCardFraud(),
            'cardFraudServiceId' => $settingsService->getCardFraudServiceId(),
            // SaveCard
            'cardSaveCard' => $settingsService->getCardSaveCard(),
            'cardSaveCardOption' => $settingsService->getCardSaveCardOption(),
            'cardSaveCardChecked' => false,
            // Other
            'supports' => array_filter($this->gateway->supports, [$this->gateway, 'supports'])
        ]);
    }

}