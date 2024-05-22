<?php

/**
 * Copyright (c) Paydock, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: Paydock for WooCommerce
 * Plugin URI: https://github.com/Paydock/jsp-woocommerce
 * Description: Paydock simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using Paydock's payment orchestration.
 * Author: Paydock
 * Author URI: https://www.paydock.com/
 * Version: 1.0.43
 * Requires at least: 6.4.2
 * Text Domain: paydock-for-woo
 * Tested up to: 6.4.2
 * WC requires at least: 6.4.2
 * WC tested up to: 8.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'PAY_DOCK_PLUGIN_FILE' ) ) {
	define( 'PAY_DOCK_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PAY_DOCK_PLUGIN_PATH' ) ) {
	define( 'PAY_DOCK_PLUGIN_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'PAY_DOCK_PLUGIN_URL' ) ) {
	define( 'PAY_DOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PAY_DOCK_PLUGIN_VERSION' ) ) {
	define( 'PAY_DOCK_PLUGIN_VERSION', '1.0.43' );
}

if ( ! defined( 'PAY_DOCK_TEXT_DOMAIN' ) ) {
	define( 'PAY_DOCK_TEXT_DOMAIN', 'pay_dock' );
}

require_once 'vendor/autoload.php';

Paydock\PaydockPlugin::getInstance();
