<?php
/**
 * Copyright (c) PowerBoard, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: PowerBoard for WooCommerce
 * Plugin URI: https://github.com/CommBank-PowerBoard/powerboard-e-commerce-woo
 * Description: PowerBoard is a payment orchestration solution that delivers payment choice, operational efficiency and security for your business.
 * Author: PowerBoard
 * Author URI: https://www.commbank.com.au/business/payments/take-online-payments/powerboard.html#getting-started
 * Version: 1.0.0
 * Requires at least: 6.6
 * Text Domain: power-board
 * Tested up to: 6.7.1
 * Stable tag: 1.0.0
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 6.6
 * WC tested up to: 9.5.2
 * Requires Plugins: woocommerce
 *
 * @noinspection PhpUndefinedFunctionInspection for plugin_dir_url
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'POWER_BOARD_PLUGIN_FILE' ) ) {
	define( 'POWER_BOARD_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_URL' ) ) {
	define( 'POWER_BOARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_VERSION' ) ) {
	define( 'POWER_BOARD_PLUGIN_VERSION', '1.0.0' );
}

if ( ! defined( 'PLUGIN_PREFIX' ) ) {
	define( 'PLUGIN_PREFIX', 'power_board' );
}

if ( ! defined( 'PLUGIN_NAME' ) ) {
	define( 'PLUGIN_NAME', 'PowerBoard for WooCommerce' );
}

require_once __DIR__ . '/vendor/autoload.php';

PowerBoard\PowerBoardPlugin::get_instance();
