<?php
/**
 * Copyright (c) Paydock, Inc. and its affiliates. All Rights Reserved
 * Plugin Name: Paydock for WooCommerce
 * Plugin URI: https://github.com/PayDock/e-commerce-woo
 * Description: Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.
 * Author: Paydock
 * Author URI: https://www.paydock.com/
 * Version: 2.0.46
 * Requires at least: 6.0
 * Text Domain: paydock-for-woo
 * Tested up to: 6.5.3
 * WC requires at least: 8.0.0
 * WC tested up to: 8.9.1
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
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
	define( 'PAY_DOCK_PLUGIN_VERSION', '2.0.46' );
}

if ( ! defined( 'PAY_DOCK_TEXT_DOMAIN' ) ) {
	define( 'PAY_DOCK_TEXT_DOMAIN', 'pay_dock' );
}

require_once 'vendor/autoload.php';

Paydock\PaydockPlugin::getInstance();
