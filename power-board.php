<?php
/**
 * Copyright (c) PowerBoard, Inc. and its affiliates. All Rights Reserved
 * Plugin Name: PowerBoard for WooCommerce
 * Plugin URI: https://github.com/PayDock/e-commerce-woo/tree/power_board
 * Description: Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.
 * Author: PowerBoard
 * Author URI: https://www.commbank.com.au/
 * Version: 2.0.46
 * Requires at least: 6.0
 * Text Domain: power-board-for-woo
 * Tested up to: 6.5.3
 * WC requires at least: 8.0.0
 * WC tested up to: 8.9.1
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
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
	define( 'POWER_BOARD_PLUGIN_VERSION', '2.0.46' );
}

if ( ! defined( 'POWER_BOARD_TEXT_DOMAIN' ) ) {
	define( 'POWER_BOARD_TEXT_DOMAIN', 'power_board' );
}

require_once 'vendor/autoload.php';

PowerBoard\PowerBoardPlugin::getInstance();
