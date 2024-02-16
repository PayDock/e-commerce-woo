<?php

namespace Paydock\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Paydock\Abstract\AbstractSingleton;
use Paydock\Controllers\Admin\WidgetController;
use Paydock\Enums\SettingsTabs;
use Paydock\PaydockPlugin;
use Paydock\Services\Checkout\BankAccountPaymentService;
use Paydock\Util\ApmBlock;
use Paydock\Util\BankAccountBlock;
use Paydock\Util\PaydockGatewayBlocks;
use Paydock\Util\WalletsBlock;

class ActionsService extends AbstractSingleton
{
    protected const PROCESS_OPTIONS_FUNCTION = 'process_admin_options';
    protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
    protected const SECTION_HOOK = 'woocommerce_get_sections';
    protected static ?ActionsService $instance = null;

    protected function __construct()
    {
        add_action('before_woocommerce_init', function () {
            $this->addCompatibilityWithWooCommerce();
            $this->addPaymentActions();
            $this->addPaymentMethodToChekout();
            $this->addSettingsActions();
            $this->addEndpoints();
        });
    }

    protected function addCompatibilityWithWooCommerce(): void
    {
        if (class_exists(FeaturesUtil::class)) {
            FeaturesUtil::declare_compatibility('custom_order_tables', PAY_DOCK_PLUGIN_FILE);
        }
    }

    protected function addPaymentActions()
    {
        $payments = [
            'paydock_bank_account_gateway' => new BankAccountPaymentService(),
        ];
        foreach ($payments as $paymentKey => $payment) {
            add_action('woocommerce_update_options_payment_gateways_'.$paymentKey, [$payment, 'process_admin_options']);
            add_action('woocommerce_scheduled_subscription_payment_'.$paymentKey, [
                $payment,
                'process_subscription_payment'
            ], 10, 2);
            add_action('woocommerce_after_checkout_billing_form', [
                $payment,
                'woocommerce_before_checkout_form'
            ], 10, 1);
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
            FeaturesUtil::declare_compatibility('cart_checkout_blocks',
                PAY_DOCK_PLUGIN_FILE, true);
        });

        add_action('woocommerce_blocks_payment_method_type_registration',
            function (PaymentMethodRegistry $payment_method_registry) {
                $payment_method_registry->register(new PaydockGatewayBlocks);
                $payment_method_registry->register(new BankAccountBlock());
                $payment_method_registry->register(new WalletsBlock());
                $payment_method_registry->register(new ApmBlock());
            });
    }

    protected function addSettingsActions(): void
    {
        foreach (SettingsTabs::cases() as $settingsTab) {
            add_action(self::PROCESS_OPTIONS_HOOK_PREFIX.$settingsTab->value, [
                $settingsTab->getSettingService(), self::PROCESS_OPTIONS_FUNCTION,
            ]);
            add_action(self::SECTION_HOOK, fn($systemTabs) => array_merge($systemTabs, [
                $settingsTab->value => __('', PaydockPlugin::PLUGIN_PREFIX),
            ]));
        }
    }

    protected function addEndpoints()
    {
        add_action('rest_api_init', function () {
            register_rest_route('paydock/v1', '/wallets/charge', array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [new WidgetController(), 'createWalletCharge'],
                'permission_callback' => '__return_true'
            ));
        });
    }
}
