<?php
/**
 * Copyright (c) PowerBoard, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 * Plugin Name: PowerBoard for WooCommerce
 * Plugin URI: https://github.com/PowerBoard/jsp-woocommerce
 * Description: PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard's payment orchestration.
 * Author: PowerBoard
 * Author URI: https://powerboard.commbank.com.au/
 * Version: 0.7.0
 * Requires at least: 6.4.2
 * Text Domain: power_board-for-woo
 * Tested up to: 6.4.2
 * WC requires at least: 6.4.2
 * WC tested up to: 8.5
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('POWER_BOARD_PLUGIN_FILE')) {
    define('POWER_BOARD_PLUGIN_FILE', __FILE__);
}

if (!defined('POWER_BOARD_PLUGIN_PATH')) {
    define('POWER_BOARD_PLUGIN_PATH', dirname(__FILE__));
}

if (!defined('POWER_BOARD_PLUGIN_URL')) {
    define('POWER_BOARD_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('POWER_BOARD_PLUGIN_VERSION')) {
    define('POWER_BOARD_PLUGIN_VERSION', '0.7.0');
}

if (!defined('POWER_BOARD_TEXT_DOMAIN')) {
    define('POWER_BOARD_TEXT_DOMAIN', 'power_board');
}

require_once "vendor/autoload.php";

PowerBoard\PowerBoardPlugin::getInstance();
