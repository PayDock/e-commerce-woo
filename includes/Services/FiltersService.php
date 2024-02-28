<?php

namespace PowerBoard\Services;

use PowerBoard\Abstract\AbstractSingleton;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\Checkout\ApmsPaymentService;
use PowerBoard\Services\Checkout\BankAccountPaymentService;
use PowerBoard\Services\Checkout\CardPaymentService;
use PowerBoard\Services\Checkout\WalletsPaymentService;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;
use PowerBoard\Services\Settings\WidgetSettingService;

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
        add_filter('plugin_action_links_'.plugin_basename(POWER_BOARD_PLUGIN_FILE), [$this, 'getSettingLink']);
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
        $options = get_option("power_board_fraud_{$orderId}");
        if ($options === false) {
            return $text;
        }

        return __('Your order is being processed. We’ll get back to you shortly', POWER_BOARD_TEXT_DOMAIN);
    }

    public function getSettingLink(array $links): array
    {
        array_unshift($links, sprintf(
                '<a href="%1$s">%2$s</a>',
                admin_url('admin.php?page=wc-settings&tab=checkout&section='.PowerBoardPlugin::PLUGIN_PREFIX),
                __('Settings', PowerBoardPlugin::PLUGIN_PREFIX)
            )
        );

        return $links;
    }

    public function addCustomOrderStatuses($order_statuses)
    {
        $order_statuses['wc-power_board-paid'] = array(
            'label' => 'Paid via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Paid via PowerBoard <span class="count">(%s)</span>',
                'Paid via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-pending'] = array(
            'label' => 'Pending via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Panding via PowerBoard <span class="count">(%s)</span>',
                'Panding via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-failed'] = array(
            'label' => 'Fail via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Fail via PowerBoard <span class="count">(%s)</span>',
                'Fail via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-authorize'] = array(
            'label' => 'Authorized via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Authorized via PowerBoard <span class="count">(%s)</span>',
                'Authorized via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-cancelled'] = array(
            'label' => 'Cancelled authorize via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled authorize via PowerBoard <span class="count">(%s)</span>',
                'Cancelled authorize via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );

        $order_statuses['wc-power_board-refunded'] = array(
            'label' => 'Refunded via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Refunded via PowerBoard <span class="count">(%s)</span>',
                'Refunded via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-p-refund'] = array(
            'label' => 'Partial refunded via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Partial refunded via PowerBoard <span class="count">(%s)</span>',
                'Partial refunded via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );
        $order_statuses['wc-power_board-requested'] = array(
            'label' => 'Requested via PowerBoard',
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Requested via PowerBoard <span class="count">(%s)</span>',
                'Requested via PowerBoard <span class="count">(%s)</span>', 'woocommerce'),
        );

        return $order_statuses;
    }

    public function addCustomOrderSingleStatusesStatuses($order_statuses)
    {
        $order_statuses['wc-power_board-failed'] = 'Fail via PowerBoard';
        $order_statuses['wc-power_board-pending'] = 'Pending via PowerBoard';
        $order_statuses['wc-power_board-paid'] = 'Paid via PowerBoard';
        $order_statuses['wc-power_board-authorize'] = 'Authorized via PowerBoard';
        $order_statuses['wc-power_board-cancelled'] = 'Cancelled authorize via PowerBoard';
        $order_statuses['wc-power_board-refunded'] = 'Refunded via PowerBoard';
        $order_statuses['wc-power_board-p-refund'] = 'Partial refunded via PowerBoard';
        $order_statuses['wc-power_board-requested'] = 'Requested via PowerBoard';
        return $order_statuses;
    }
}
