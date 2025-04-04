<?php

$config_file = $argv[1];
$config      = include $config_file;
if ( empty( $config['STAGING_API_URL'] ) ) {
	$staging_api_url = '';
	$staging_value   = '';
	$staging_key     = '';
	$staging_name    = '';
} else {
	$staging_api_url = $config['STAGING_API_URL'];
	$staging_value   = $config['STAGING_ENVIRONMENT_VALUE'];
	$staging_key     = $config['STAGING_ENVIRONMENT_URL_KEY'];
	$staging_name    = $config['STAGING_ENVIRONMENT_NAME'];
}
$is_prod = $argv[2] === 'prod';

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
 * Version: {$config['PLUGIN_VERSION']}
 * Requires at least: 6.4.2
 * Text Domain: {$config['PLUGIN_TEXT_DOMAIN']}
 * Tested up to: 6.6
 * Stable tag: {$config['PLUGIN_VERSION']}
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

if ( ! defined( 'PLUGIN_URL' ) ) {
	define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
	define( 'PLUGIN_VERSION', '{$config['PLUGIN_VERSION']}' );
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

if ( ! defined( 'PLUGIN_METHOD_TITLE' ) ) {
	define( 'PLUGIN_METHOD_TITLE', '{$config['PLUGIN_METHOD_TITLE']}' );
}

if ( ! defined( 'PLUGIN_METHOD_DESCRIPTION' ) ) {
	define( 'PLUGIN_METHOD_DESCRIPTION', '{$config['PLUGIN_METHOD_DESCRIPTION']}' );
}

if ( ! defined( 'PLUGIN_VERSIONS_JSON_URL' ) ) {
	define( 'PLUGIN_VERSIONS_JSON_URL', '{$config['PLUGIN_VERSIONS_JSON_URL']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_API_URL' ) ) {
	define( 'PLUGIN_PRODUCTION_API_URL', '{$config['PRODUCTION_API_URL']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_VALUE', '{$config['PRODUCTION_ENVIRONMENT_VALUE']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY', '{$config['PRODUCTION_ENVIRONMENT_URL_KEY']}' );
}

if ( ! defined( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_PRODUCTION_ENVIRONMENT_NAME', '{$config['PRODUCTION_ENVIRONMENT_NAME']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_API_URL' ) ) {
	define( 'PLUGIN_SANDBOX_API_URL', '{$config['SANDBOX_API_URL']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_VALUE', '{$config['SANDBOX_ENVIRONMENT_VALUE']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY', '{$config['SANDBOX_ENVIRONMENT_URL_KEY']}' );
}

if ( ! defined( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_SANDBOX_ENVIRONMENT_NAME', '{$config['SANDBOX_ENVIRONMENT_NAME']}' );
}

if ( ! defined( 'PLUGIN_STAGING_API_URL' ) ) {
	define( 'PLUGIN_STAGING_API_URL', '$staging_api_url' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_VALUE' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_VALUE', '$staging_value' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_URL_KEY', '$staging_key' );
}

if ( ! defined( 'PLUGIN_STAGING_ENVIRONMENT_NAME' ) ) {
	define( 'PLUGIN_STAGING_ENVIRONMENT_NAME', '$staging_name' );
}

require_once __DIR__ . '/vendor/autoload.php';

WooPlugin\WooPluginPlugin::get_instance();

EOT;

file_put_contents( ( $is_prod ? '.' : '../..' ) . '/plugin.php', $plugin_content );
