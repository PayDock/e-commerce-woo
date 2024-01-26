<?php

namespace Paydock\Services;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Paydock\Abstract\AbstractSettingService;
use Paydock\Abstract\AbstractSingleton;
use Paydock\PaydockPlugin;

class ActionsService extends AbstractSingleton
{
    protected static ?ActionsService $instance = null;

    protected function __construct()
    {
        add_action('before_woocommerce_init', function () {
            $this->addCompatibilityWithWooCommerce();
            $this->addPaymentActions();
        });
        $this->addCustomJs();
    }

    public function addCustomJs(): void
    {
        add_action('admin_footer', function () {
            include_once plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . 'templates/custom_admin_scripts.php';
        });
    }

    protected function addCompatibilityWithWooCommerce(): void
    {
        if (class_exists(FeaturesUtil::class)) {
            FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                PAY_DOCK_PLUGIN_FILE,
                true
            );
        }
    }

    protected function addPaymentActions(): void
    {
        add_action(
            'woocommerce_update_options_payment_gateways_' . AbstractSettingService::LIVE_CONNECTION_TAB,
            [new LiveConnectionSettingService(), 'process_admin_options']
        );
        add_action(
            'woocommerce_update_options_payment_gateways_' . AbstractSettingService::SANDBOX_CONNECTION_TAB,
            [new SandboxConnectionSettingService(), 'process_admin_options']
        );
        add_action(
            'woocommerce_update_options_payment_gateways_' . AbstractSettingService::WIDGET_TAB,
            [new WidgetSettingService(), 'process_admin_options']
        );
        add_action(
            'woocommerce_update_options_payment_gateways_' . AbstractSettingService::LOG_TAB,
            [new LogsTabService(), 'process_admin_options']
        );
        add_action(
            'woocommerce_update_options_payment_gateways_' . AbstractSettingService::LOG_TAB,
            [new LogsTabService(), 'process_admin_options']
        );

        add_action('woocommerce_get_sections', fn($settingsTab) => array_merge(
            $settingsTab, [
                AbstractSettingService::LOG_TAB => __('', PaydockPlugin::PLUGIN_PREFIX),
                AbstractSettingService::SANDBOX_CONNECTION_TAB => __('', PaydockPlugin::PLUGIN_PREFIX),
                AbstractSettingService::WIDGET_TAB => __('', PaydockPlugin::PLUGIN_PREFIX),
                AbstractSettingService::WEBHOOKS_TAB => __('', PaydockPlugin::PLUGIN_PREFIX),
            ]
        ));
    }
}
