<?php

namespace Paydock\Services;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Paydock\Abstract\AbstractSingleton;
use Paydock\Enums\SettingsTabs;
use Paydock\PaydockPlugin;
use Paydock\Util\PaydockGatewayBlocks;

class ActionsService extends AbstractSingleton
{
    protected static ?ActionsService $instance = null;

    protected const PROCESS_OPTIONS_FUNCTION = 'process_admin_options';
    protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
    protected const SECTION_HOOK = 'woocommerce_get_sections';

    protected function __construct()
    {
        add_action('before_woocommerce_init', function () {
            $this->addCompatibilityWithWooCommerce();
            $this->addPaymentActions();
            $this->addPaymentMethodToChekout();
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
        foreach (SettingsTabs::cases() as $settingsTab) {
            add_action(self::PROCESS_OPTIONS_HOOK_PREFIX . $settingsTab->value, [
                $settingsTab->getSettingService(),
                self::PROCESS_OPTIONS_FUNCTION,
            ]);
            add_action(self::SECTION_HOOK, fn($systemTabs) => array_merge($systemTabs, [
                $settingsTab->value => __('', PaydockPlugin::PLUGIN_PREFIX),
            ]));
        }
    }



    /**
     * Add new payment method on chekout page
     */
    protected function addPaymentMethodToChekout()
    {
        if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            return;
        }

        add_action('before_woocommerce_init', function () {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', PAY_DOCK_PLUGIN_FILE, true);
        });

        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function (PaymentMethodRegistry $payment_method_registry) {
                $payment_method_registry->register(new PaydockGatewayBlocks);
            }
        );
    }
}
