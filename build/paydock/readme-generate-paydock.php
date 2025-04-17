<?php

$is_prod = $argv[1] === 'prod';

$readme = <<<EOT
# Paydock for WooCommerce #

Accept more payment methods with Paydock. Connect multiple payment gateways with a central interface to manage the transactions.

## Description ##

Gain access to a highly customisable WooCommerce Payment Checkout experience, utilising Paydock's Checkout Feature. 
Connect services such as card payments, digital wallets, alternative payment methods and value-added services with a few quick configuration steps.

## Installation ##

**Please note that WooCommerce must be installed and active before this plugin can be used.**

Prerequisites to install and configure the Paydock plugin, you will need a WordPress instance running:

- WooCommerce version: 9.5.2
- [WooCommerce Server Requirements](https://woocommerce.com/document/server-requirements/)
- [WooCommerce PHP and WordPress Support](https://woocommerce.com/document/update-php-wordpress/)

Note: Encryption of admin values within the plugin requires Sodium or as a fallback OpenSSL to be enabled.

### Install the plugin

1. **Download the Plugin**

   - Download the Paydock for WooCommerce plugin ZIP file from: https://github.com/PayDock/e-commerce-woo/releases/latest

2. **Log in to your WordPress dashboard**
   - Open your web browser. Navigate to your WordPress site and log in with your administrator credentials.

3. **Access the plugin menu**
   - Once logged in, navigate to the left sidebar and click "Plugins" → "Add New" at the top of the page.

4. **Upload the plugin**
   - The "Upload Plugin" button is located at the top of the “Add Plugins” page. Click on it to proceed.
   ![image](assets/images/upload-plugin.png)
   - Click the "Choose File" button and navigate to where you saved the Paydock for WooCommerce plugin ZIP file. Select the file and click "Open".

5. **Install the plugin**
   - With the file selected, click the "Install Now" button to upload and install the plugin.

6. **Activate the plugin**
   - After the installation, you’ll be taken to a new page. Click the "Activate Plugin" button to activate Paydock on your site.

## Configure the plugin

1. Open the payment methods page by navigating to the left sidebar and click "WooCommerce" → "Settings" → at the top of the page.
   ![image](assets/images/configure-plugin-1.png)

2. Observe that the new Payment method ‘Paydock payment’ is on the list. Ensure it is enabled. Press “Manage” to access the configuration page.
   ![image](assets/images/configure-plugin-2.png)

3. This will take you to the configuration page:
   ![image](assets/images/configure-plugin-3.png)
   1. **Environment** – Select your environment, options are:
      1. Sandbox
      2. Production

   2. **API Access Token** – Use the Paydock dashboard to create your API access token. 

      **Note** - The following permissions must be enabled in the ‘For API’ section of the Paydock access token screen when creating your access token:
      ![image](assets/images/create-access-token-2.png)
      ![image](assets/images/create-access-token-1.png)

      **Note** - Please ensure that once your access token is created, that you enter it here and then press save. This will then populate the ‘Configuration Template ID’ and ‘Customisation Template ID’ dropdown lists with the templates you have created in the Checkout feature.

   3. **Version** – The version of the Checkout feature that you want to integrate with, in v4.1.0 of the WooCommerce plugin, only version = 1 will be available. 
   As more features and functionality are added to the checkout feature, this version number will increment and provide additional features in the WooCommerce plugin.

   4. **Configuration template ID – As per ‘API Access Token’ step 3.ii**, once you have entered a valid access token and pressed save this will populate the dropdown list with the available configuration template ID’s you have created in your Paydock company dashboard.

      **Note** - This is a mandatory field that must be selected in order for your Checkout to render inside the WooCommerce Dashboard.

   5. **Customisation template ID – As per ‘API Access Token’ step 3.ii**, once you have entered a valid access token and pressed save this will populate the dropdown list with the available customisation template ID’s you have created in your Paydock company dashboard.

      **Note** - This is an optional field and does not need to be selected.

## Order management and refunds

### How to Access WooCommerce orders?
Hover over 'WooCommerce' to see a submenu, click on 'Orders' within the submenu.<br>
This takes you to the main 'Orders' page, where you can view the list of all orders.
![image](assets/images/orders.png)

### How to issue a refund?

1. Open an order by clicking on the hyperlink in the ‘Order’ column (see above screenshot).

2. Once the order is open, click the refund button
   ![image](assets/images/refunds-1.png)
   The following options will appear:
   ![image](assets/images/refunds-2.png)

3. Choose whether to refund specific items (and whether to restock those) or simply enter a refund amount and an optional note and press the Refund button displayed. <br>
   This will add a note to your order advising that a refund was performed + update the status to ‘refunded’.

4. If you perform a partial refund, you can continue to perform additional refunds up until ‘Total available to refund’ = $0
   ![image](assets/images/refunds-3.png)
   
**Note** – As refund creation does not create a new charge_id in Paydock (as it is a matched refund), no additional ID will be captured in WooCommerce when refund is completed, as the refund exists in the context of the previously completed charge_id. <br>
Please note, a refund will only be completed from WooCommerce -> Paydock -> Gateway if the original order was placed in WooCommerce in the same way. Ie. If a manual MOTO order is placed in WooCommerce and subsequently replicated in Paydock, the same processed must be followed in cases where a refund is required for MOTO/manually placed orders.

## More information
This section provides additional information about the Paydock plugin for WooCommerce.

### Version compatibility
The v4.1.0 WooCommerce plugin has been tested and validated against a vanilla installation of:
   - WooCommerce version: 9.5.2
   - WordPress version: 6.7.1
   - PHP version 8.2

### WooCommerce -> Paydock syncing
The plugin has been designed in a way where there is a one-way sync from WooCommerce -> Paydock.

**What does this mean?** <br>
If any actions are performed directly within the Paydock dashboard (such as charge creation or refund), this will not be synced back to WooCommerce. <br>
Additionally, manually updating the status of a WooCommerce order does not trigger any updates to the Paydock system.

### Logging
There is a daily file that is created that logs all requests made from the WooCommerce plugin to the Paydock API. This can be accessed here:
![image](assets/images/logs.png)

### MOTO orders
If a MOTO order needs to be made, the process is as follows.
   1. Complete manual order creation in WooCommerce
   2. Complete manual charge creation in Paydock

**Note** – As stated in ‘WooCommerce -> Paydock syncing’ section, the completion of the manual charge creation in Paydock will not sync back to WooCommerce. The order management and status transition must be managed manually by WooCommerce admin for MOTO orders. The same logic applies for refunds of MOTO orders.

### Charge ID mapping
When a charge is created in Paydock (completed or failed), it will be captured in the WooCommerce order notes section. The importance of this mapping, is so that you can search for the charge_id in the Paydock dashboard to view the logs if you need to see more information about the charge. Here is an example of an order where:
   1. Initial charge attempt failed 
   2. Subsequent charge attempt succeeded 
   3. A partial refund was completed
   
You can see there are 2 charge_id’s that were created, one for the failed attempt and one for the succeeded attempt. The refund was performed in the context of the succeeded attempt.
![image](assets/images/charge-id.png)

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
{$changelog}

EOT;

file_put_contents( ( $is_prod ? '.' : '../..' ) . '/readme.txt', $readme_txt );
