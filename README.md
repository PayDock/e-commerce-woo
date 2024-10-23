# PowerBoard for WooCommerce #

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

## Description ##

Accept more payment methods with PowerBoard. Connect multiple payment gateways with a central interface to manage the transactions.

Use PowerBoard to add a payment gateway for credit cards, bank transfers, PayPal or even Buy now pay later gateways.

## Installation ##

Prerequisites to install and configure the PowerBoard plugin, you will need a Wordpress instance running:

- Compatible with the latest version of WooCommerce 8 (Tested with 8.9.3)
- [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
- [WooCommerce PHP and Wordpress Support](https://woocommerce.com/document/update-php-wordpress/)

### Step-by-Step Installation

1. **Download the Plugin**

   - Zip files available from our releases section [here](https://github.com/CommBank-PowerBoard/powerboard-e-commerce-woo/releases/latest)

2. **Install the Plugin**
   - Go to your Wordpress Site
   - Select Plugins -> Add new Plugin -> Upload Plugin

3. **Upload the zip file and activate the plugin**

## Third Party API and libraries

This plugin provides the ability to use payment methods through the PowerBoard API:
* for sandbox https://api.staging.powerboard.commbank.com.au/v1/
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
