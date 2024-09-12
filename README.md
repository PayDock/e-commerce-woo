=== PowerBoard for WooCommerce ===

Contributors: PowerBoard
https://www.commbank.com.au/

Tags: powerboard, woocommerce, payment, gateways, payment gateways
Stable tag: 3.1.3

License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

== Description ==

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

Use PowerBoard to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

== Installation ==

Prerequisites to install and configure the PowerBoard plugin, you will need a Wordpress instance running:

* WooCommerce versions: 8.3-8.9
* [WooCommerce Server Requirements] (https://woocommerce.com/document/server-requirements/)
* [WooCommerce PHP and Wordpress Support] (https://woocommerce.com/document/update-php-wordpress/)

### Step-by-Step Installation

1. **Download the Plugin**
   - Download the plugin from the repository:  
     [PowerBoard WooCommerce Plugin] (https://github.com/CommBank-PowerBoard/powerboard-e-commerce-woo)

2. **Install the Plugin**
   - Go to WordPress -> Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

To download the latest version of PowerBoard's WooCommerce plugin, you can manually trigger a build and download the generated artefact directly from GitHub:

1. **Trigger the Build**
   - Visit the Actions tab in PowerBoard's GitHub repository: [PowerBoard GitHub] (https://github.com/CommBank-PowerBoard/powerboard-e-commerce-woo)
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
