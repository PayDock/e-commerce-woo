# Changelog

## [3.3.0] - 2024-12-03

### Compatibility

- Compatible with WooCommerce versions `8.9.3` and `9.4.1`.

### Changed

- Removed the option to authenticate the plugin using public and secret keys, enhancing security and simplifying configuration.

### Bug Fixes

- Removed deprecated PHP 8+ functions to maintain compatibility with PHP 7.4.

## [3.2.0] - 2024-11-26

### Compatibility

- Compatible with WooCommerce versions `8.9.3` and `9.4.1`.

### Added

- Implemented Sodium as the default encryption method for enhanced security and performance.

### Bug Fixes

- Resolved issue where orders in "Pending Payment" status could still be completed via the Orders page — the "Pay" button will no longer appear in this context.
- Fixed problem with the "power_board_fraud" entry being written to the database to check for fraud status, ensuring that these entries are properly deleted after use.
- Addressed issue with "power_board_status_change_error" being saved generically— error messages are now shown only for the user who made the change, improving clarity and accuracy.

### Technical Changes

- Removed the use of session_start(), as this PHP core function is intended for pure PHP projects; the CMS now handles user sessions automatically, improving system efficiency.

## [3.1.4] - 2024-11-01

### Compatibility

- Compatible with WooCommerce versions `8.9.3` and `9.3.3`.

### Bug Fixes

- #### Refund issues:

  - Prevented refunds exceeding the total amount captured.
  - Fixed issue where order status was not updating to "Refunded" when the order was partially refunded.
  - Resolved problem where refunding a completed order from the WooCommerce site did not reflect the change on the PowerBoard charge.
  - Corrected issue where partially refunding an order and then marking it as 'Completed' resulted in the full order being refunded.
  - Ensured consistency in refund error messages.
  - Addressed issue where the "Net Payment" field on the Order Details page was left blank after a refund transaction.
<br/>

- #### Capture issues:
  - Fixed issue where capturing an authorized transaction from WooCommerce orders was not possible.
  - Resolved incorrect calculation of the Net Payment amount when partial capture and refund are performed.
<br/>

- #### Checkout with card issues:

  - Fixed issue preventing order creation using cards for users not logged into WooCommerce.
  - Resolved problem where orders were placed successfully despite the "Authentication Not Available" error.
  - Addressed error appearing when saving a card for customers without a Gateway ID if the phone number was not provided.
  - Corrected issue where users were shown an error when entering a valid card after an order failed.
  - Fixed issue where selecting a saved card from the dropdown and entering a new card still created a charge with the wrong card number.
  - Resolved problem preventing order creation when selecting a saved card without a gateway ID.
  - Fixed issue where non-3DS cards resulted in failed transactions 3DS was enabled.
  - Corrected error message displayed on the card widget when the user entered an invalid phone number.
  - Addressed issue where the saved card dropdown was not visible when a transaction failed due to invalid details.
  - Resolved selecting a saved card, switching between different payment methods (wallets or APMs), and then selecting a different saved card preventing users from placing orders.
<br/>

- #### Checkout with wallet and APMs issues:

  - Fixed issue where APM configuration was incorrectly taken from card payments.
  - Corrected display of wallet and APM buttons on the checkout page when cards were disabled in settings.
  - Addressed issue where cart total updates caused payment failures for ZipPay and AfterPay v1.
<br/>

- #### Statuses issues:

  - Resolved issue where changing the shipping cost did not update the total cost in the backend and caused the charge in PowerBoard to reflect the old value with the shipping cost added.
  - Fixed issue where orders with payments in "Authorised" status on PowerBoard were not reflected as "On Hold" in WooCommerce.
  - Temporarily hid the "Add Items" button when the order is "On Hold".

### Technical Changes

- Implemented fixes for security vulnerabilities identified and raised during audits to enhance overall system protection.

## [3.1.3] - 2024-09-13

Initial Release

### Compatibility

- Compatible with WooCommerce version `8.9.3`.
