<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSingleton;
use Paydock\Enums\SettingsTabs;
use Paydock\PaydockPlugin;
use Paydock\Services\Checkout\ApmsPaymentService;
use Paydock\Services\Checkout\BankAccountPaymentService;
use Paydock\Services\Checkout\CardPaymentService;
use Paydock\Services\Checkout\WalletsPaymentService;
use Paydock\Services\Settings\LiveConnectionSettingService;
use Paydock\Services\Settings\LogsSettingService;
use Paydock\Services\Settings\SandboxConnectionSettingService;
use Paydock\Services\Settings\WidgetSettingService;

class FiltersService extends AbstractSingleton
{
    protected static ?FiltersService $instance = null;

    protected function __construct()
    {
        $this->addWooCommerceFilters();
        $this->addSettingsLink();
    }

    public function registerInWooCommercePaymentClass(array $methods): array
    {
        global $current_section;
        global $current_tab;

        $methods[] = LiveConnectionSettingService::class;
        if ($current_tab != 'checkout'
            || in_array($current_section, array_map(fn(SettingsTabs $tab) => $tab->value, SettingsTabs::secondary()))) {
            $methods[] = SandboxConnectionSettingService::class;
            $methods[] = WidgetSettingService::class;
            $methods[] = LogsSettingService::class;
            $methods[] = CardPaymentService::class;
            $methods[] = BankAccountPaymentService::class;
            $methods[] = WalletsPaymentService::class;
            $methods[] = ApmsPaymentService::class;
        }


        return $methods;
    }

    protected function addSettingsLink(): void
    {
        add_filter('plugin_action_links_'.plugin_basename(PAY_DOCK_PLUGIN_FILE), [$this, 'getSettingLink']);
    }

    protected function addWooCommerceFilters(): void
    {
        add_filter('woocommerce_payment_gateways', [$this, 'registerInWooCommercePaymentClass']);
        add_filter('woocommerce_register_shop_order_post_statuses', [$this, 'addCustomOrderStatuses']);
        add_filter('wc_order_statuses', [$this, 'addCustomOrderSingleStatusesStatuses']);
    }

    public function getSettingLink(array $links): array
    {
        array_unshift($links, sprintf(
                '<a href="%1$s">%2$s</a>',
                admin_url('admin.php?page=wc-settings&tab=checkout&section='.PaydockPlugin::PLUGIN_PREFIX),
                __('Settings', PaydockPlugin::PLUGIN_PREFIX)
            )
        );

        return $links;
    }

    public function addCustomOrderStatuses($order_statuses)
    {
        $order_statuses['wc-paydock-paid'] = array(
            'label' => 'Paid via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Paid via Paydock <span class="count">(%s)</span>',
                'Paid via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-paydock-pending'] = array(
            'label' => 'Pending via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Panding via Paydock <span class="count">(%s)</span>',
                'Panding via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-paydock-failed'] = array(
            'label' => 'Fail via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Fail via Paydock <span class="count">(%s)</span>',
                'Fail via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-paydock-authorize'] = array(
            'label' => 'Authorized via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Authorized via Paydock <span class="count">(%s)</span>',
                'Authorized via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );

        return $order_statuses;
    }

    public function addCustomOrderSingleStatusesStatuses($order_statuses)
    {
        $order_statuses['wc-paydock-failed'] = 'Fail via Paydock';
        $order_statuses['wc-paydock-pending'] = 'Pending via Paydock';
        $order_statuses['wc-paydock-paid'] = 'Paid via Paydock';
        $order_statuses['wc-paydock-authorize'] = 'Authorized via Paydock';

        return $order_statuses;
    }
}
