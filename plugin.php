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
 * Version: 1.0.0
 * Requires at least: 6.4.2
 * Text Domain: power-board
 * Tested up to: 6.6
 * Stable tag: 1.0.0
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

if ( ! defined( 'PLUGIN_URL' ) ) {
	define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
	define( 'PLUGIN_VERSION', '1.0.0' );
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

if ( ! defined( 'PLUGIN_METHOD_TITLE' ) ) {
	define( 'PLUGIN_METHOD_TITLE', 'PowerBoard payment' );
}

if ( ! defined( 'PLUGIN_METHOD_DESCRIPTION' ) ) {
	define( 'PLUGIN_METHOD_DESCRIPTION', 'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard\'s payment orchestration.' );
}

if ( ! defined( 'PLUGIN_VERSIONS_JSON_URL' ) ) {
	define( 'PLUGIN_VERSIONS_JSON_URL', 'https://widget.powerboard.commbank.com.au/sdk/platforms/compatibility-registry.json' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_API_URL' ) ) {
	define( 'PLUGIN_PRODUCTION_API_URL', 'https://api.powerboard.commbank.com.au/v1/' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE', 'production_cba' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY', 'production' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME', 'Production' );
}

if ( ! defined( 'PLUGIN_SANDBOX_API_URL' ) ) {
	define( 'PLUGIN_SANDBOX_API_URL', 'https://api.preproduction.powerboard.commbank.com.au/v1/' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE', 'preproduction_cba' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY', 'preproduction' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME', 'Preproduction' );
}

if ( ! defined( 'PLUGIN_STAGING_API_URL' ) ) {
	define( 'PLUGIN_STAGING_API_URL', 'https://api.staging.powerboard.commbank.com.au/v1/' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_VALUE', 'staging_cba' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY', 'staging' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_NAME', 'Staging' );
}

require_once __DIR__ . '/vendor/autoload.php';

WooPlugin\WooPluginPlugin::get_instance();
