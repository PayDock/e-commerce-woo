<?php

$config_file = $argv[1];
$config = include $config_file;
$is_prod = $argv[2] === "prod";

$available_plugins = ['power-board', 'paydock'];

foreach ($available_plugins as $plugin) {
  if (file_exists(($is_prod ? './' : '../') . $plugin . '.php')) {
    unlink(($is_prod ? './' : '../') . $plugin . '.php');
  }
};

$plugin_content = <<<EOT
<?php

/**
 * Copyright (c) {$config['PLUGIN_NAME']}, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: {$config['PLUGIN_NAME']}
 * Plugin URI: {$config['PLUGIN_URI']}
 * Description: {$config['PLUGIN_DESCRIPTION']}
 * Author: {$config['PLUGIN_AUTHOR']}
 * Author URI: {$config['PLUGIN_AUTHOR_URI']}
 * Version: 3.1.3
 * Requires at least: 6.4.2
 * Text Domain: {$config['PLUGIN_TEXT_DOMAIN']}
 * Tested up to: 6.6
 * Stable tag: 3.1.3
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 6.4.2
 * WC tested up to: 8.5
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! defined( 'PLUGIN_NAME' ) ) {
  define( 'PLUGIN_NAME', '{$config['PLUGIN_NAME']}' );
}

if ( ! defined( 'PLUGIN_FILE' ) ) {
  define( 'PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PLUGIN_PATH' ) ) {
  define( 'PLUGIN_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_URL' ) ) {
  define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) . '/' );
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
  define( 'PLUGIN_VERSION', '3.1.3' );
}

if ( ! defined( 'PLUGIN_WIDGET_NAME' ) ) {
  define( 'PLUGIN_WIDGET_NAME', '{$config['PLUGIN_WIDGET_NAME']}' );
}

if ( ! defined( 'PLUGIN_TEXT_DOMAIN' ) ) {
  define( 'PLUGIN_TEXT_DOMAIN', '{$config['PLUGIN_TEXT_DOMAIN']}' );
}

if ( ! defined( 'PLUGIN_PREFIX' ) ) {
  define( 'PLUGIN_PREFIX', '{$config['PLUGIN_PREFIX']}' );
}

if ( ! defined( 'PLUGIN_TEXT_NAME' ) ) {
  define( 'PLUGIN_TEXT_NAME', '{$config['PLUGIN_TEXT_NAME']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_API_URL' ) ) {
  define( 'PLUGIN_PRODUCTION_API_URL', '{$config['PLUGIN_PRODUCTION_API_URL']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_API_URL' ) ) {
  define( 'PLUGIN_SANDBOX_API_URL', '{$config['PLUGIN_SANDBOX_API_URL']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT' ) ) {
  define( 'PLUGIN_PRODUCTION_ENVIRONMENT', '{$config['PLUGIN_PRODUCTION_ENVIRONMENT']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT' ) ) {
  define( 'PLUGIN_SANDBOX_ENVIRONMENT', '{$config['PLUGIN_SANDBOX_ENVIRONMENT']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_WIDGET_URL' ) ) {
  define( 'PLUGIN_PRODUCTION_WIDGET_URL', '{$config['PLUGIN_PRODUCTION_WIDGET_URL']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_WIDGET_URL' ) ) {
  define( 'PLUGIN_SANDBOX_WIDGET_URL', '{$config['PLUGIN_SANDBOX_WIDGET_URL']}' );
}

require_once 'vendor/autoload.php';

WooPlugin\WooPluginPlugin::getInstance();
EOT;

file_put_contents(($is_prod ? './' : '../') . $config['PLUGIN_TEXT_DOMAIN'] . '.php', $plugin_content);
