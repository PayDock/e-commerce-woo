<?php

$is_prod = $argv[1] === 'prod';

$readme = <<<EOT
# Paydock for WooCommerce #

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

## Description ##

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

Use Paydock to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

## Installation ##

**Please note that WooCommerce must be installed and active before this plugin can be used.**

Prerequisites to install and configure the Paydock plugin, you will need a WordPress instance running:

- WooCommerce versions: 8.9.3 or 9.4.1
- [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
- [WooCommerce PHP and WordPress Support](https://woocommerce.com/document/update-php-wordpress/)

Note: Encryption of admin values within the plugin requires Sodium or as a fallback OpenSSL to be enabled.

### Step-by-Step Installation

1. **Download the Plugin**

   - Zip files available from our releases section [here](https://github.com/PayDock/e-commerce-woo/releases/latest)

2. **Install the Plugin**
   - Go to your WordPress Site
   - Select Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

## Source

This plugin contains compile and non compile js code, if you need customize something. Code that need compile for working with woocommerce block in `/resource` path.
In root dir you can find `webpack.config.js` file, its default config for compile front-end js, but you can use it as a starting point to create your own configuration.
Also, we use helper code that not need compile what contained in assets path.
EOT;

file_put_contents( ( $is_prod ? '.' : '../..' ) . '/README.md', $readme );

$changelog = file_get_contents( ( $is_prod ? '.' : '../..' ) . '/changelog.txt' );

$readme_txt = <<<EOT
=== Paydock for WooCommerce ===

Contributors: paydock
https://paydock.com/
Tags: paydock, woocommerce, payment, gateways, payment gateways
Requires PHP: 7.4
Requires at least: 6.6
Tested up to: 6.7.1
Stable tag: 1.0.0
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

== Description ==

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

Use Paydock to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

== Installation ==

**Please note that WooCommerce must be installed and active before this plugin can be used.**

Prerequisites to install and configure the Paydock plugin, you will need a WordPress instance running:

* WooCommerce versions: 8.9.3 or 9.4.1
* [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
* [WooCommerce PHP and WordPress Support](https://woocommerce.com/document/update-php-wordpress/)

Note: Encryption of admin values within the plugin requires Sodium or as a fallback OpenSSL to be enabled.

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

file_put_contents( ( $is_prod ? '.' : '../..' ) . '/readme.txt', $readme_txt );
