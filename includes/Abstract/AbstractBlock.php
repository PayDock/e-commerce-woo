<?php

namespace Paydock\Abstract;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Paydock\PaydockPlugin;

abstract class AbstractBlock extends AbstractPaymentMethodType
{

    public function is_active()
    {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles()
    {
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

    protected function getScriptPath(string $name): string
    {
        return "assets/build/js/frontend/$name.js";
    }

    protected function getAssetPath(string $name): string
    {
        return PAY_DOCK_PLUGIN_PATH."assets/build/js/frontend/$name.asset.php";
    }
}
