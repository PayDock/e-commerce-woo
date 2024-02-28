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
        add_filter('woocommerce_thankyou_order_received_text', [$this, 'woocommerceThankyouOrderReceivedText']);
    }

    public function woocommerceThankyouOrderReceivedText($text)
    {
        $orderId = absint(get_query_var('order-received'));
        $options = get_option("paydock_fraud_{$orderId}");
        if ($options === false) {
            return $text;
        }

        return __('Your order is being processed. We’ll get back to you shortly', PAY_DOCK_TEXT_DOMAIN);
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
        $order_statuses['wc-paydock-cancelled'] = array(
            'label' => 'Cancelled authorize via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled authorize via Paydock <span class="count">(%s)</span>',
                'Cancelled authorize via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );

        $order_statuses['wc-paydock-refunded'] = array(
            'label' => 'Refunded via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Refunded via Paydock <span class="count">(%s)</span>',
                'Refunded via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-paydock-p-refund'] = array(
            'label' => 'Partial refunded via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Partial refunded via Paydock <span class="count">(%s)</span>',
                'Partial refunded via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-paydock-requested'] = array(
            'label' => 'Requested via Paydock',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Requested via Paydock <span class="count">(%s)</span>',
                'Requested via Paydock <span class="count">(%s)</span>', 'woocommerce'),
        );

        return $order_statuses;
    }

    public function addCustomOrderSingleStatusesStatuses($order_statuses)
    {
        $order_statuses['wc-paydock-failed'] = 'Fail via Paydock';
        $order_statuses['wc-paydock-pending'] = 'Pending via Paydock';
        $order_statuses['wc-paydock-paid'] = 'Paid via Paydock';
        $order_statuses['wc-paydock-authorize'] = 'Authorized via Paydock';
        $order_statuses['wc-paydock-cancelled'] = 'Cancelled authorize via Paydock';
        $order_statuses['wc-paydock-refunded'] = 'Refunded via Paydock';
        $order_statuses['wc-paydock-p-refund'] = 'Partial refunded via Paydock';
        $order_statuses['wc-paydock-requested'] = 'Requested via Paydock';
        return $order_statuses;
    }
}
