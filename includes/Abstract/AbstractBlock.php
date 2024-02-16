<?php

namespace Paydock\Abstract;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Paydock\PaydockPlugin;
use Paydock\Services\SettingsService;

abstract class AbstractBlock extends AbstractPaymentMethodType
{
    private static $isLoad = false;

    public function is_active()
    {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
        if (!self::$isLoad) {
            $sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.js';
//        $sdkUrl = 'https://widget.paydock.com/sdk/{version}/widget.umd.min.js';
            $sdkUrl = preg_replace('{version}', SettingsService::getInstance()->getVersion(), $sdkUrl);

            wp_enqueue_script(
                'paydock-form',
                PAY_DOCK_PLUGIN_URL.'/assets/js/frontend/form.js',
                array(),
                time(),
                true
            );
            wp_enqueue_style(
                'paydock-widget-css',
                PAY_DOCK_PLUGIN_URL.'/assets/css/frontend/widget.css',
                array(),
                time()
            );

            wp_enqueue_script('paydock-api', $sdkUrl, array(), time(), true);

            self::$isLoad = true;
        }

        $scriptPath = 'assets/build/js/frontend/'.static::SCRIPT.'.js';
        $scriptAssetPath = 'assets/build/js/frontend/'.static::SCRIPT.'.asset.php';
        $scriptUrl = plugins_url($scriptPath, PAY_DOCK_PLUGIN_FILE);
        $scriptName = PaydockPlugin::PLUGIN_PREFIX.'-'.static::SCRIPT;

        $scriptAsset = file_exists($scriptAssetPath) ? require($scriptAssetPath) : [
            'dependencies' => [],
            'version' => PAY_DOCK_PLUGIN_VERSION
        ];
        wp_register_script($scriptName, $scriptUrl, $scriptAsset['dependencies'], $scriptAsset['version'], true);

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations($scriptName);
        }

        return [$scriptName];
    }
}
