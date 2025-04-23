=== Paydock for WooCommerce ===

Contributors: paydock
https://paydock.com/
Tags: paydock, woocommerce, payment, gateways, payment gateways
Requires PHP: 7.4
Requires at least: 6.6
Tested up to: 6.7.1
Stable tag: 4.1.0
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

== Description ==

Gain access to a highly customisable WooCommerce Payment Checkout experience, utilising Paydock's Checkout Feature. Connect services such as card payments, digital wallets, alternative payment methods and value-added services with a few quick configuration steps.

== Installation ==

**Please note that WooCommerce must be installed and active before this plugin can be used.**

Prerequisites to install and configure the Paydock plugin, you will need a WordPress instance running:

* WooCommerce version: 9.5.2
* [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
* [WooCommerce PHP and WordPress Support](https://woocommerce.com/document/update-php-wordpress/)

Note: Encryption of admin values within the plugin requires Sodium or as a fallback OpenSSL to be enabled.

== Configuration and Additional Information ==

To configure the plugin and access other important details, please refer to the full documentation on our [GitHub repository](https://github.com/PayDock/e-commerce-woo/blob/release/README.md).
This page includes setup instructions, configuration steps, and important information about the plugin's functionality.

== Screenshots ==

1. Frontend
2. Admin side settings

== Changelog ==
*** Changelog ***

[4.1.0] - 2025-04-17
* Compatibility
  - Compatible with WooCommerce version `9.5.2`.

* Added
  - Added compatibility fix for "Woo Additional Terms" plugin.

* Changed
  - Moved order status update out of order creation to enable compatibility with "Preorders" external plugin.

* Fixed
  - Fixed an issue where the Classic checkout could sometimes process a new order with the order ID of an already processing order.

[4.0.0] - 2025-04-08

* Compatibility
  - Compatible with WooCommerce version `9.5.2`.

* Added
  - Enhanced logging capability to improvement merchant observability.

* Technical Changes
  - Updated the plugin to utilise the Paydock Checkout feature. This displays as one payment option but provides access to all previously available payment methods. Merchants must configure their supported payment methods and options via the Paydock dashboard using the Checkout templates functionality.

[3.0.5] - 2024-07-23

* Compatibility
  - Compatible with WooCommerce version `8.9.3`.

* Changes
  - Statuses, openssl, paths

[3.0.4] - 2024-07-17

* Changes
  - Fixes, updates, tweaks

[2.0.53] - 2024-07-09

* Changes
  - Min-max feature

[2.0.46] - 2024-05-30

* Changes
  - Patch, small fixes

[2.0.44] - 2024-05-28

* Changes
  - Screenshots, readme, changelog, etc.

[2.0.37] - 2024-05-22

* Changes
  - Completely new version

[1.0.28] - 2024-04-28

* Changes
  - Fix bugs

[1.0.19] - 2024-04-27

* Initial release

