<?php

/**
 * Copyright (c) PowerBoard for WooCommerce, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: PowerBoard for WooCommerce
 * Plugin URI: https://github.com/PayDock/jsp-woocommerce/tree/power_board
 * Description: PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard's payment orchestration.
 * Author: PowerBoard
 * Author URI: https://www.commbank.com.au/business/payments/take-online-payments/powerboard.html#getting-started
 * Version: 3.1.3
 * Requires at least: 6.4.2
 * Text Domain: power-board
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
  define( 'PLUGIN_NAME', 'PowerBoard for WooCommerce' );
}

if ( ! defined( 'PLUGIN_FILE' ) ) {
  define( 'PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PLUGIN_PATH' ) ) {
  define( 'PLUGIN_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_URL' ) ) {
  define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
  define( 'PLUGIN_VERSION', '3.1.3' );
}

if ( ! defined( 'PLUGIN_WIDGET_NAME' ) ) {
  define( 'PLUGIN_WIDGET_NAME', 'cba' );
}

if ( ! defined( 'PLUGIN_TEXT_DOMAIN' ) ) {
  define( 'PLUGIN_TEXT_DOMAIN', 'power-board' );
}

if ( ! defined( 'PLUGIN_PREFIX' ) ) {
  define( 'PLUGIN_PREFIX', 'power_board' );
}

if ( ! defined( 'PLUGIN_TEXT_NAME' ) ) {
  define( 'PLUGIN_TEXT_NAME', 'PowerBoard' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_API_URL' ) ) {
  define( 'PLUGIN_PRODUCTION_API_URL', 'https://api.powerboard.commbank.com.au/v1/' );
}

if ( ! defined( 'PLUGIN_SANDBOX_API_URL' ) ) {
  define( 'PLUGIN_SANDBOX_API_URL', 'https://api.preproduction.powerboard.commbank.com.au/v1/' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT' ) ) {
  define( 'PLUGIN_PRODUCTION_ENVIRONMENT', 'production_cba' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT' ) ) {
  define( 'PLUGIN_SANDBOX_ENVIRONMENT', 'preproduction_cba' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_WIDGET_URL' ) ) {
  define( 'PLUGIN_PRODUCTION_WIDGET_URL', 'https://widget.powerboard.commbank.com.au/sdk/{version}/widget.umd.js' );
}

if ( ! defined( 'PLUGIN_SANDBOX_WIDGET_URL' ) ) {
  define( 'PLUGIN_SANDBOX_WIDGET_URL', 'https://widget.preproduction.powerboard.commbank.com.au/sdk/{version}/widget.umd.js' );
}

require_once 'vendor/autoload.php';

WooPlugin\WooPluginPlugin::getInstance();