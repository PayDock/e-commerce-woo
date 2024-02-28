<?php

namespace PowerBoard\Abstract;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\SettingsService;

abstract class AbstractBlock extends AbstractPaymentMethodType
{
    private static $isLoad = false;

    public function is_active()
    {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
        if (!self::$isLoad && is_checkout()) {
            $sdkUrl = SettingsService::getInstance()->getWidgetScriptUrl();

            wp_enqueue_script(
                'power_board-form',
                POWER_BOARD_PLUGIN_URL.'/assets/js/frontend/form.js',
                array(),
                time(),
                true
            );
            wp_enqueue_style(
                'power_board-widget-css',
                POWER_BOARD_PLUGIN_URL.'/assets/css/frontend/widget.css',
                array(),
                time()
            );

            wp_enqueue_script('power_board-api', $sdkUrl);

            self::$isLoad = true;
        }

        $scriptPath = 'assets/build/js/frontend/'.static::SCRIPT.'.js';
        $scriptAssetPath = 'assets/build/js/frontend/'.static::SCRIPT.'.asset.php';
        $scriptUrl = plugins_url($scriptPath, POWER_BOARD_PLUGIN_FILE);
        $scriptName = PowerBoardPlugin::PLUGIN_PREFIX.'-'.static::SCRIPT;

        $scriptAsset = file_exists($scriptAssetPath) ? require($scriptAssetPath) : [
            'dependencies' => [],
            'version' => POWER_BOARD_PLUGIN_VERSION
        ];
        wp_register_script($scriptName, $scriptUrl, $scriptAsset['dependencies'], $scriptAsset['version'], true);

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations($scriptName);
        }

        return [$scriptName];
    }
}
