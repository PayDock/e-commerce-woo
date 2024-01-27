<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSingleton;
use Paydock\Enums\SettingsTabs;
use Paydock\PaydockPlugin;
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
        if ($current_tab == 'checkout'
            && in_array($current_section, array_map(fn(SettingsTabs $tab) => $tab->value, SettingsTabs::secondary()))) {
            $methods[] = SandboxConnectionSettingService::class;
            $methods[] = WidgetSettingService::class;
            $methods[] = LogsSettingService::class;
        }


        return $methods;
    }

    protected function addSettingsLink(): void
    {
        add_filter('plugin_action_links_' . plugin_basename(PAY_DOCK_PLUGIN_FILE), [$this, 'getSettingLink']);
    }

    protected function addWooCommerceFilters(): void
    {
        add_filter('woocommerce_payment_gateways', [$this, 'registerInWooCommercePaymentClass']);
    }

    public function getSettingLink(array $links): array
    {
        array_unshift($links, sprintf(
                '<a href="%1$s">%2$s</a>',
                admin_url('admin.php?page=wc-settings&tab=checkout&section=' . PaydockPlugin::PLUGIN_PREFIX),
                __('Settings', PaydockPlugin::PLUGIN_PREFIX)
            )
        );

        return $links;
    }
}
