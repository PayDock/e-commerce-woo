<?php
$plugin_content = <<<EOT
# Paydock for WooCommerce #

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

## Description ##

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

Use Paydock to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

## Installation ##

Prerequisites to install and configure the Paydock plugin, you will need a Wordpress instance running:

- Compatible with the latest version of WooCommerce 8 (Tested with 8.9.3)
- [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
- [WooCommerce PHP and Wordpress Support](https://woocommerce.com/document/update-php-wordpress/)

### Step-by-Step Installation

1. **Download the Plugin**

   - Zip files available from our releases section [here](https://github.com/PayDock/e-commerce-woo/releases/latest)

2. **Install the Plugin**
   - Go to your Wordpress Site
   - Select Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

## Source

This plugin contains compile and non compile js code, if you need customize something. Code that need compile for working with woocommerce block in `/resource` path.
In root dir you can find `webpack.config.js` file, its default config for compile front-end js, but you can use it as a starting point to create your own configuration.
Also we use helper code that not need compile what contained in assets path.
EOT;

file_put_contents('../README.md', $plugin_content);

$changelog = file_get_contents('../changelog.txt');

$readme_txt = <<<EOT
=== Paydock for WooCommerce ===

Contributors: paydock
Donate link: https://paydock.com/
Tags: paydock, woocommerce, payment, gateways, payment gateways
Requires PHP: 7.4
Requires at least: 6.3
Tested up to: 6.6
Stable tag: 3.1.3
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

== Description ==

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

Use Paydock to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

For a full list of payment gateways Paydock is connected to, visit [Paydock] (https://paydock.com/features/api/ "PayDock gateways")

== Installation ==

Prerequisites to install and configure the Paydock plugin, you will need a Wordpress instance running:

* WooCommerce versions: 8.3-8.9
* [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
* [WooCommerce PHP and Wordpress Support](https://woocommerce.com/document/update-php-wordpress/)

### Step-by-Step Installation

1. **Download the Plugin**
   - Zip files available from our releases section [here](https://github.com/PayDock/e-commerce-woo/releases/latest)

2. **Install the Plugin**
   - Go to WordPress -> Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

== Screenshots ==

1. Frontend
2. Admin side settings
3. API side

== Changelog ==
{$changelog}

EOT;

file_put_contents('../readme.txt', $readme_txt);

