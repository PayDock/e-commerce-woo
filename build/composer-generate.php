<?php

$config_file = $argv[1];
$config = include $config_file;
$is_prod = $argv[2] === "prod";

$composer = <<<EOT
{
    "name": "{$config['PLUGIN_PREFIX']}/sp-woocommerce",
    "description": "{$config['PLUGIN_TEXT_NAME']} simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using {$config['PLUGIN_TEXT_NAME']}'s payment orchestration.",
    "type": "project",
    "license": "MIT",
    "minimum-stability": "dev",
    "autoload": {
        "psr-4": {
            "WooPlugin\\\\": "includes/"
        }
    }
}
EOT;

file_put_contents(($is_prod ? '.' : '..') . '/composer.json', $composer);

