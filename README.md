=== PowerBoard for WooCommerce ===

Contributors: PowerBoard
https://www.commbank.com.au/
Tags: powerboard, woocommerce, payment, gateways, payment gateways
Requires PHP: 7.4
Requires at least: 6.0
Tested up to: 6.6
Stable tag: 3.1.3
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

== Description ==

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

Use PowerBoard to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

== Installation ==

To install and configure the PowerBoard plugin, you need:

* Web Server - Nginx
* PHP 8.1
* MySQL version 8.0 or greater OR MariaDB 11.1 
* Support for HTTPS (SSL certificate)
* PHP memory limit of 256MB
* PHP Requirements (curl, gd2, mbstring, xml, json, and zip)

### Step-by-Step Installation

1. **Download the Plugin**
   - Download the plugin from the repository:  
     [PowerBoard WooCommerce Plugin] (https://github.com/PayDock/e-commerce-woo/tree/power_board)

2. **Install the Plugin**
   - Go to WordPress -> Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

4. **Watch the Tutorial**
   - Watch the video tutorial with step by step guidance: [Video Tutorial] (https://www.loom.com/share/e3baad357d4444c6967ef4b96377784b?sid=4f21b0af-43f2-4081-9ce7-76bf946fa535)

5. **Obtain Admin Credentials**

To download the latest version of PowerBoard's WooCommerce plugin, you can manually trigger a build and download the generated artefact directly from GitHub:

1. **Trigger the Build**
   - Visit the Actions tab in PowerBoard's GitHub repository: [PowerBoard GitHub] (https://github.com/PayDock/e-commerce-woo/tree/power_board)
   - Under Workflows, find the workflow named "Build and upload the PowerBoard plugin"
   - Click on "Run workflow"
   - Select the branch and click the green "Run workflow" button

2. **Download the Plugin**
   - Once the workflow completes, click on the run that you triggered in the previous step
   - Scroll down to the Artifacts at the bottom of the page
   - Click on the link to download the ZIP file

## Third Party API and libraries

this plugin provides the ability to use payment methods through the PowerBoard API:
* for sandbox https://api.preproduction.powerboard.commbank.com.au/v1/
* for live https://api.powerboard.commbank.com.au/v1/

We also use a PowerBoard widget to implement front-end features ([More here](https://developer.powerboard.commbank.com.au/reference/powerboard-widget))

### Terms of Use and Privacy Policy

You can find all relevant information here:

- [Power Board Web site](https://www.commbank.com.au/business/payments/take-online-payments/powerboard.html)
- [PowerBoard Terms and Conditions to supplement the Merchant Agreement](https://www.commbank.com.au/content/dam/commbank-assets/business/merchants/2022-09/powerboard-terms-and-conditions-july-2022.pdf)
- [Group Privacy Statement](https://www.commbank.com.au/support/privacy.html?ei=CB-footer_privacy)
- [Important documents](https://www.commbank.com.au/important-info.html?ei=CB-footer_ImportantDocs)
- [Cookies policy](https://www.commbank.com.au/important-info/cookies.html?ei=CB-footer_cookies)

This plugin transmits the payment and order information that the user provides on the checkout page to Power Board only 
when making a payment using one of the methods provided by this plugin.
The following data is transferred for payment:
* All payment details
* Delivery data (if delivery is included in the price and paid for when placing the order)
* Product details

## Source

This plugin contains compile and non compile js code, if you need customize something. Code that need compile for working with woocommerce block in `/resource` path.
In root dir you can find `webpack.config.js` file, its default config for compile front-end js, but you can use it as a starting point to create your own configuration.
Also we use helper code that not need compile what contained in assets path.

== Screenshots ==

1. Frontend
2. Admin side settings
3. API side

== Changelog ==

= 1.0.19 =
* Initial release

= 1.0.28 =
* Bug fixes

= 1.4.0 =
* First release on the plugins store

= 1.5.7 =
* Bug fixes

= 2.0.37 =
* Completely new plugin. This version includes new code, supports new versions of PHP, MySQL, WordPress, WooCommerce, and v2 API of the PowerBoard app.

= 2.0.44 =
* New features, readme, changelog, etc.

= 2.0.46 =
* Patch, small fixes

= 2.0.53 =
* Min-max feature

= 3.0.4 =
* Fixes, updates, tweaks

= 3.0.5 =
* Statuses, openssl, paths

= 3.0.15 =
* Fixed problem with display on classic checkout

= 3.0.17 =
* Bug fixes

= 3.1.2 =
* Bug fixes

= 3.1.3 =
* More bugs fixed
