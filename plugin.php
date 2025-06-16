<?php
/**
 * Copyright (c) Paydock for WooCommerce, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: Paydock for WooCommerce
 * Plugin URI: https://github.com/PayDock/e-commerce-woo
 * Description: Paydock simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using Paydock's payment orchestration.
 * Author: Paydock
 * Author URI: https://paydock.com/
 * Version: 4.1.0
 * Requires at least: 6.6
 * Text Domain: paydock
 * Tested up to: 6.7.1
 * Stable tag: 4.1.0
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 6.6
 * WC tested up to: 9.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'PLUGIN_NAME' ) ) {
	define( 'PLUGIN_NAME', 'Paydock for WooCommerce' );
}

if ( ! defined( 'PLUGIN_NAME_KEY' ) ) {
	define( 'PLUGIN_NAME_KEY', 'paydock-for-woocommerce' );
}

if ( ! defined( 'PLUGIN_FILE' ) ) {
	define( 'PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PLUGIN_URL' ) ) {
	define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
	define( 'PLUGIN_VERSION', '4.1.0' );
}

if ( ! defined( 'PLUGIN_WIDGET_NAME' ) ) {
	define( 'PLUGIN_WIDGET_NAME', 'paydock' );
}

if ( ! defined( 'PLUGIN_TEXT_DOMAIN' ) ) {
	define( 'PLUGIN_TEXT_DOMAIN', 'paydock' );
}

if ( ! defined( 'PLUGIN_PREFIX' ) ) {
	define( 'PLUGIN_PREFIX', 'paydock' );
}

if ( ! defined( 'PLUGIN_TEXT_NAME' ) ) {
	define( 'PLUGIN_TEXT_NAME', 'Paydock' );
}

if ( ! defined( 'PLUGIN_METHOD_TITLE' ) ) {
	define( 'PLUGIN_METHOD_TITLE', 'Paydock payment' );
}

if ( ! defined( 'PLUGIN_METHOD_DESCRIPTION' ) ) {
	define( 'PLUGIN_METHOD_DESCRIPTION', 'Paydock simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using Paydock\'s payment orchestration.' );
}

if ( ! defined( 'PLUGIN_VERSIONS_JSON_URL' ) ) {
	define( 'PLUGIN_VERSIONS_JSON_URL', 'https://widget.paydock.com/sdk/platforms/compatibility-registry.json' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_API_URL' ) ) {
	define( 'PLUGIN_PRODUCTION_API_URL', 'https://api.paydock.com/v1/' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE', 'production' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY', 'production' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME', 'Production' );
}

if ( ! defined( 'PLUGIN_SANDBOX_API_URL' ) ) {
	define( 'PLUGIN_SANDBOX_API_URL', 'https://api-sandbox.paydock.com/v1/' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE', 'sandbox' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY', 'sandbox' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME', 'Sandbox' );
}

if ( ! defined( 'PLUGIN_STAGING_API_URL' ) ) {
	define( 'PLUGIN_STAGING_API_URL', '' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_VALUE', '' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY', '' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_NAME', '' );
}

if ( ! defined( 'PLUGIN_PATH' ) ) {
	$current_dir = plugin_dir_path( __FILE__ );
	define( 'PLUGIN_PATH', $current_dir );
}

require_once __DIR__ . '/vendor/autoload.php';

WooPlugin\WooPluginPlugin::get_instance();
