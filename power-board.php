<?php

/**
 * Copyright (c) PowerBoard, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: PowerBoard for WooCommerce
 * Plugin URI: https://github.com/PayDock/jsp-woocommerce/tree/power_board
 * Description: PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard's payment orchestration.
 * Author: PowerBoard
 * Author URI: https://www.commbank.com.au/
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

if ( ! defined( 'POWER_BOARD_PLUGIN_FILE' ) ) {
	define( 'POWER_BOARD_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_PATH' ) ) {
	define( 'POWER_BOARD_PLUGIN_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_URL' ) ) {
	define( 'POWER_BOARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_VERSION' ) ) {
	define( 'POWER_BOARD_PLUGIN_VERSION', '3.1.3' );
}

require_once 'vendor/autoload.php';

PowerBoard\PowerBoardPlugin::getInstance();
