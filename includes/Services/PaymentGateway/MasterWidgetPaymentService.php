<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 */

declare( strict_types=1 );

namespace PowerBoard\Services\PaymentGateway;

use PowerBoard\Enums\EnvironmentSettingsEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Helpers\EnvironmentSettingsHelper;
use PowerBoard\Helpers\MasterWidgetSettingsHelper;
use PowerBoard\Helpers\SettingGroupsHelper;
use PowerBoard\Helpers\SettingsHelper;
use PowerBoard\Services\Assets\AdminAssetsService;
use PowerBoard\Services\HashService;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use PowerBoard\Services\Validation\ConnectionValidationService;
use Exception;
use WC_Admin_Settings;
use WC_Blocks_Utils;
use WC_Order;
use WC_Payment_Gateway;

/**
 * Some properties used comes from the extension WC_Payment_Gateway from WooCommerce
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $method_title
 * @property string $method_description
 * @property string $icon
 * @property bool $has_fields
 * @property array $supports
 * @property array $settings
 * @property array $form_fields
 * @property string $enabled
 * @property string $plugin_id
 */
class MasterWidgetPaymentService extends WC_Payment_Gateway {
	private static ?MasterWidgetPaymentService $instance = null;
	protected TemplateService $template_service;

	/**
	 * Uses functions (__, _x, add_action) from WordPress
	 * Uses a method (init_settings) from WC_Payment_Gateway
	 * Uses a property (method_description) from WC_Payment_Gateway
	 */
	public function __construct() {
		$this->id         = POWER_BOARD_PLUGIN_PREFIX;
		$this->has_fields = true;
		$this->supports   = [ 'products', 'default_credit_card_form' ];

		/* @noinspection PhpUndefinedFunctionInspection */
		$this->method_title = _x( 'PowerBoard payment', 'PowerBoard payment method', 'power-board' );
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->title = __( 'PowerBoard', 'power-board' );
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->method_description = __(
			'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard\'s payment orchestration.',
			'power-board'
		);
		$this->description        = '';
		$this->icon               = POWER_BOARD_PLUGIN_URL . 'assets/images/logo.png';

		// Load the settings
		$this->init_form_fields();
		/* @noinspection PhpUndefinedMethodInspection */
		$this->init_settings();

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_admin() ) {
			$this->title = $this->method_title;
			new AdminAssetsService();
			$this->template_service = new TemplateService( $this );

			$key = SettingsHelper::get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CREDENTIALS,
					'ACCESS_KEY',
				]
			);

			if ( ! empty( $this->settings[ $key ] ) ) {
				try {
					$decrypted_key = HashService::decrypt( $this->settings[ $key ] );
				} catch ( Exception $error ) {
					$decrypted_key = null;
				}
				$this->settings[ $key ] = $decrypted_key;
			}
		}

		// Actions
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wp_ajax_nopriv_power_board_create_error_notice', [ $this, 'power_board_create_error_notice' ], 20 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wp_ajax_power_board_create_error_notice', [ $this, 'power_board_create_error_notice' ], 20 );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_checkout_fields', [ $this, 'setup_phone_fields_settings' ] );
	}

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * This function is used on WC_Payment_Gateway
	 *
	 * @noinspection PhpUnused
	 */
	public function get_title(): string {
		return trim( $this->title ) ? $this->title : $this->method_title;
	}
	/**
	 * This function is used on WC_Payment_Gateway
	 *
	 * @noinspection PhpUnused
	 */
	public function is_available(): bool {
		if ( $this->enabled === 'yes' ) {
			// Prevent using this gateway on frontend if there are any configuration errors.
			return $this->has_valid_required_fields();
		}

		return parent::is_available();
	}

	public function init_form_fields(): void {
		foreach ( SettingGroupsEnum::cases() as $setting_group ) {
			$key = SettingsHelper::get_option_name(
					$this->id,
					[
						$setting_group,
						'label',
					]
				);

			$this->form_fields[ $key ] = [
				'type'  => 'big_label',
				'title' => SettingGroupsHelper::get_label( $setting_group ),
			];

			switch ( $setting_group ) {
				case SettingGroupsEnum::ENVIRONMENT:
					$merged_options = $this->get_environment_options();
					break;
				case SettingGroupsEnum::CREDENTIALS:
					$merged_options = $this->get_credential_options();
					break;
				case SettingGroupsEnum::CHECKOUT:
					$merged_options = $this->get_checkout_options();
					break;
				default:
					$merged_options = [];
					break;
			}

			$this->form_fields = array_merge( $this->form_fields, $merged_options );
		}
	}

	public function get_version(): ?string {
		$version_key = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CHECKOUT,
				MasterWidgetSettingsEnum::VERSION,
			]
		);
		$settings    = $this->get_db_settings();
		if ( array_key_exists( $version_key, $settings ) ) {
			return $settings[ $version_key ];
		}
		return null;
	}

	public function get_access_token(): ?string {
		$token_key = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				'ACCESS_KEY',
			]
		);
		$settings  = $this->get_db_settings();
		if ( array_key_exists( $token_key, $settings ) ) {
			try {
				$decrypted_key = HashService::decrypt( $settings[ $token_key ] );
			} catch ( Exception $error ) {
				$decrypted_key = null;
			}
			return $decrypted_key;
		}
		return null;
	}

	public function get_configuration_id(): ?string {
		$configuration_id = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CHECKOUT,
				MasterWidgetSettingsEnum::CONFIGURATION_ID,
			]
		);
		if ( array_key_exists( $configuration_id, $this->settings ) ) {
			return $this->settings[ $configuration_id ];
		}
		return null;
	}

	public function get_environment(): ?string {
		$environment_key = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::ENVIRONMENT,
				EnvironmentSettingsEnum::ENVIRONMENT,
			]
		);
		$settings        = $this->get_db_settings();
		if ( array_key_exists( $environment_key, $settings ) ) {
			return $settings[ $environment_key ];
		}
		return null;
	}

	/**
	 * Process the payment and return the result.
	 * This function is used on WC_Payment_Gateway
	 * Uses a function (sanitize_text_field) from WordPress
	 * Uses functions (wc_get_order, WC and get_return_url) from WooCommerce
	 * Uses method (get_return_url) from WC_Payment_Gateway
	 * phpcs:disable WordPress.Security.NonceVerification -- processed through the WooCommerce form handler
	 *
	 * @noinspection PhpUnused
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_get_order( $order_id );

		/* @noinspection PhpUndefinedFunctionInspection */
		$session = WC()->session;
		/* @noinspection PhpUndefinedFunctionInspection */
		$checkout_order = $session->get( 'power_board_checkout_cart_' . wp_create_nonce( 'power-board-checkout-cart' ) );
		$order          = $this->get_order_to_process_payment( $order, $checkout_order );
		$order->set_status( 'processing' );
		$order->payment_complete();
		setcookie( 'cart_total', '0', time() + 3600, '/' );

		/* @noinspection PhpUndefinedFunctionInspection */
		$charge_id = isset( $_POST['chargeid'] ) ? sanitize_text_field( wp_unslash( $_POST['chargeid'] ) ) : '';

		$order->update_meta_data( '_power_board_charge_id', $charge_id );
		/* @noinspection PhpUndefinedFunctionInspection */
		WC()->cart->empty_cart();
		$order->save();

		/* @noinspection PhpUndefinedMethodInspection */
		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}
	// phpcs:enable

	/**
	 * Uses functions (WC and get_woocommerce_currency) from WooCommerce
	 */
	public function get_settings(): array {
		$settings_service = SettingsService::get_instance();

		/* @noinspection PhpUndefinedFunctionInspection */
		return [
			// Wordpress data.
			'environment'             => $settings_service->get_environment(),
			// Woocommerce data.
			'amount'                  => WC()->cart->get_total(),
			'currency'                => strtoupper( get_woocommerce_currency() ),
			// Widget.
			'title'                   => 'PowerBoard',
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		];
	}

	/**
	 * Ajax function
	 * Uses functions (sanitize_text_field, wp_verify_nonce, __ and wp_json_error) from WordPress
	 * Uses functions (wc_add_notice and wc_print_notices) from WooCommerce
	 */
	public function power_board_create_error_notice(): ?array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, 'power-board-create-error-notice' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', 'power-board' ) ] );

			return null;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';
		/* @noinspection PhpUndefinedFunctionInspection */
		$notice_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'error';
		if ( $message ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wc_clear_notices();
			/* @noinspection PhpUndefinedFunctionInspection */
			wc_add_notice( esc_html( $message ), $notice_type );
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$response['data'] = wc_print_notices();

		return $response;
	}

	public function setup_phone_fields_settings( $address_fields ): array {
		$address_fields['billing']['billing_phone']['required'] = false;
		$address_fields['shipping']['shipping_phone']           = [
			'label'        => 'Phone',
			'type'         => 'tel',
			'required'     => false,
			'class'        => [ 'form-row-wide' ],
			'validate'     => [ 'phone' ],
			'autocomplete' => 'tel',
			'priority'     => 95,
		];
		return $address_fields;
	}

	/**
	 * This function is used on WC_Payment_Gateway
	 * Uses functions (wp_create_nonce and wp_json_encode) from WordPress
	 *
	 * @noinspection PhpUnused
	 */
	public function payment_fields(): void {
		$template = new TemplateService( $this );
		SDKAdapterService::get_instance();

		$settings = $this->get_settings();
		/* @noinspection PhpUndefinedFunctionInspection */
		$nonce = wp_create_nonce( 'power-board-create-charge-intent' );

		/* @noinspection PhpUndefinedFunctionInspection */
		$data = [
			'description' => $this->description,
			'id'          => $this->id,
			'nonce'       => $nonce,
			'settings'    => wp_json_encode( $settings ),
		];
		$template->include_checkout_html(
			'method-form',
			$data
		);
	}

	/**
	 * Processes the admin options for the payment gateway
	 * This function is used on WC_Payment_Gateway
	 * Uses functions (wp_unslash, do_action, update_option and apply_filters) from WordPress
	 * Uses a function (wc_clean) from WooCommerce
	 * Uses methods (init_settings, get_form_fields, get_field_type, validate_text_field, get_option, add_error and get_option_key) from WC_Payment_Gateway
     * phpcs:disable WordPress.Security.NonceVerification -- processed through the WooCommerce form handler
	 *
	 * @noinspection PhpUnused
	 */
	public function process_admin_options(): bool {
		/* @noinspection PhpUndefinedMethodInspection */
		$this->init_settings();
		$validation_service = new ConnectionValidationService( $this );

		$hashed_credential_keys = [];
		$settings_keys          = [];

		$access_key = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				'ACCESS_KEY',
			]
		);

		$hashed_credential_keys[ $access_key ] = 'ACCESS_KEY';
		$settings_keys[ $access_key ]          = 'ACCESS_KEY';

		foreach ( EnvironmentSettingsEnum::cases() as $environment_settings ) {
			$key                   = SettingsHelper::get_option_name(
				$this->id,
				[
					SettingGroupsEnum::ENVIRONMENT,
					$environment_settings,
				]
			);
			$settings_keys[ $key ] = $environment_settings;
		}
		foreach ( MasterWidgetSettingsEnum::cases() as $master_widget_settings ) {
			$key                   = SettingsHelper::get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CHECKOUT,
					$master_widget_settings,
				]
			);
			$settings_keys[ $key ] = $master_widget_settings;
		}

		/* @noinspection PhpUndefinedMethodInspection */
		foreach ( $this->get_form_fields() as $key => $field ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$type = $this->get_field_type( $field );

			$option_key = $this->plugin_id . $this->id . '_' . $key;

			/* @noinspection PhpUndefinedFunctionInspection */
			$value = ! empty( $_POST[ $option_key ] ) ? wc_clean( wp_unslash( $_POST[ $option_key ] ) ) : null;

			if ( method_exists( $this, 'validate_' . $type . '_field' ) ) {
				$value = $this->{'validate_' . $type . '_field'}( $key, $value );
			} else {
				/* @noinspection PhpUndefinedMethodInspection */
				$value = $this->validate_text_field( $key, $value );
			}

			if ( array_key_exists( $key, $settings_keys ) ) {
				if ( ! empty( $validation_service->get_errors() ) || $value === '********************' ) {
					/* @noinspection PhpUndefinedMethodInspection */
					$value = $this->get_option( $key );
				}
			}

			$this->settings[ $key ] = $value;
		}

		foreach ( $hashed_credential_keys as $key => $credential_settings ) {
			try {
				$decrypted_key = HashService::decrypt( $this->settings[ $key ] );
			} catch ( Exception $error ) {
				$decrypted_key = null;
				/* @noinspection PhpUndefinedMethodInspection */
				$this->add_error( $error );
				WC_Admin_Settings::add_error( $error );
			}
			$is_encrypted = $decrypted_key !== $this->settings[ $key ];

			if ( ! empty( $this->settings[ $key ] ) && ! $is_encrypted ) {
				try {
					$encrypted_key = HashService::encrypt( $this->settings[ $key ] );
				} catch ( Exception $error ) {
					$encrypted_key = null;
					/* @noinspection PhpUndefinedMethodInspection */
					$this->add_error( $error );
					WC_Admin_Settings::add_error( $error );
				}
				$this->settings[ $key ] = $encrypted_key;
			}
		}

		foreach ( $validation_service->get_errors() as $error ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$this->add_error( $error );
			WC_Admin_Settings::add_error( $error );
		}

		/* @noinspection PhpUndefinedMethodInspection */
		$option_key = $this->get_option_key();
		/* @noinspection PhpUndefinedFunctionInspection */
		do_action( 'woocommerce_update_option', [ 'id' => $option_key ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		return update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ),
			'yes'
		);
	}
    // phpcs:enable

	/**
	 * This function is used on admin.php template
	 *
	 * @noinspection PhpUnused
	 */
	public function parent_generate_settings_html( $form_fields = [], $should_echo = true ): ?string {
		return parent::generate_settings_html( $form_fields, $should_echo );
	}

	/**
	 * This function is used on WC_Payment_Gateway
	 * Uses a method (get_form_fields) from WC_Payment_Gateway
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function generate_settings_html( $form_fields = [], $should_echo = true ): ?string {
		if ( empty( $form_fields ) ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$form_fields = $this->get_form_fields();
		}

		$credential_key                    = SettingsHelper::get_option_name( $this->id, [ SettingGroupsEnum::CREDENTIALS, 'ACCESS_KEY' ] );
		$this->settings[ $credential_key ] = ! empty( $this->settings[ $credential_key ] ) ? '********************' : '';
		$form_fields                       = compact( 'form_fields' );

		if ( $should_echo ) {
			$this->template_service->include_admin_html( 'admin', $form_fields );
		} else {
			return $this->template_service->get_admin_html( 'admin', $form_fields );
		}

		return null;
	}

	/**
	 * This function is used on WC_Payment_Gateway
	 *
	 * @noinspection PhpUnused
	 */
	public function generate_big_label_html( $key, $value ): string {
		return $this->template_service->get_admin_html( 'big-label', compact( 'key', 'value' ) );
	}

	private function get_credential_options(): array {
		$key = SettingsHelper::get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				'ACCESS_KEY',
			]
		);

		return [
			$key => [
				'type'        => 'password',
				'title'       => 'API Access Token',
				'description' => 'Enter your API Access Token. This token is used to securely authenticate your payment operations. It is also used to retrieve the values for the Checkout Template ID fields shown below.',
				'desc_tip'    => true,
			],
		];
	}

	private function get_checkout_options(): array {
		$fields       = [];
		$access_token = $this->get_access_token();
		$environment  = $this->get_environment();
		$version      = $this->get_version();

		foreach ( MasterWidgetSettingsEnum::cases() as $checkout_settings ) {
			$key = SettingsHelper::get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CHECKOUT,
					$checkout_settings,
				]
			);

			$fields[ $key ] = [
				'type'  => MasterWidgetSettingsHelper::get_input_type( $checkout_settings ),
				'title' => preg_replace( [ '/ Id/', '/ id/' ], ' ID', MasterWidgetSettingsHelper::get_label( $checkout_settings ) ),
			];

			if ( MasterWidgetSettingsEnum::VERSION === $checkout_settings || ! empty( $environment ) ) {
				$options = MasterWidgetSettingsHelper::get_options_for_ui( $checkout_settings, $environment, $access_token, $version );

				if ( ! empty( $options ) && ( MasterWidgetSettingsHelper::get_input_type( $checkout_settings ) ) === 'select' ) {
					$fields[ $key ]['options'] = $options;
					$fields[ $key ]['class']   = POWER_BOARD_PLUGIN_PREFIX . '-settings' . ( MasterWidgetSettingsEnum::CUSTOMISATION_ID === $checkout_settings ? ' is-optional' : '' );
					$fields[ $key ]['default'] = '';
				}
			}
		}

		return $fields;
	}

	private function get_environment_options(): array {
		$fields = [];
		foreach ( EnvironmentSettingsEnum::cases() as $environment_settings ) {
			$key = SettingsHelper::get_option_name(
				$this->id,
				[
					SettingGroupsEnum::ENVIRONMENT,
					$environment_settings,
				]
			);

			$fields[ $key ] = [
				'type'  => EnvironmentSettingsHelper::get_input_type( $environment_settings ),
				'title' => preg_replace( [ '/ Id/', '/ id/' ], ' ID', EnvironmentSettingsHelper::get_label( $environment_settings ) ),
			];

			$options = EnvironmentSettingsHelper::get_options_for_ui( $environment_settings );

			if ( ! empty( $options ) && ( EnvironmentSettingsHelper::get_input_type( $environment_settings ) ) === 'select' ) {
				$fields[ $key ]['options'] = $options;
				$fields[ $key ]['class']   = POWER_BOARD_PLUGIN_PREFIX . '-settings';
				$fields[ $key ]['default'] = EnvironmentSettingsHelper::get_default( $environment_settings );
			}
		}

		return $fields;
	}

	private function get_order_to_process_payment( WC_Order $current_order, array $checkout_order ): WC_Order {
		if ( empty( $checkout_order ) ) {
			return $current_order;
		}

		$current_order_total  = $current_order->get_total( false );
		$checkout_order_total = $checkout_order['total'];

		/* @noinspection PhpUndefinedFunctionInspection */
		$current_customer  = WC()->session->get( 'customer' );
		$checkout_customer = $checkout_order['shipping_address'];

		if ( $current_customer['date_modified'] !== $checkout_customer['date_modified'] ) {
			$current_order->set_billing_address(
				[
					'first_name' => $checkout_customer['first_name'],
					'last_name'  => $checkout_customer['last_name'],
					'company'    => $checkout_customer['company'],
					'phone'      => $checkout_customer['phone'],
					'email'      => $checkout_customer['email'],
					'address_1'  => $checkout_customer['address_1'],
					'address_2'  => $checkout_customer['address_2'],
					'city'       => $checkout_customer['city'],
					'state'      => $checkout_customer['state'],
					'postcode'   => $checkout_customer['postcode'],
					'country'    => $checkout_customer['country'],
				]
			);
			$current_order->set_shipping_address(
				[
					'first_name' => $checkout_customer['shipping_first_name'],
					'last_name'  => $checkout_customer['shipping_last_name'],
					'company'    => $checkout_customer['shipping_company'],
					'phone'      => $checkout_customer['shipping_phone'],
					'email'      => $checkout_customer['email'],
					'address_1'  => $checkout_customer['shipping_address_1'],
					'address_2'  => $checkout_customer['shipping_address_2'],
					'city'       => $checkout_customer['shipping_city'],
					'state'      => $checkout_customer['shipping_state'],
					'postcode'   => $checkout_customer['shipping_postcode'],
					'country'    => $checkout_customer['shipping_country'],
				]
			);

			$current_order->calculate_totals();
			$current_order->save();
		}

		if ( $current_order_total !== $checkout_order_total ) {
			$order_items = $current_order->get_items();
			foreach ( $order_items as $item_id => $item ) {
				$current_order->remove_item( $item_id );
			}

			$checkout_order_items = $checkout_order['items'];
			foreach ( $checkout_order_items as $checkout_order_item ) {
				$product_id = $checkout_order_item['product_id'];
				$quantity   = $checkout_order_item['quantity'];
				/* @noinspection PhpUndefinedFunctionInspection */
				$product = wc_get_product( $product_id );

				if ( $product && $product->exists() && $quantity > 0 ) {
					$current_order->add_product( $product, $quantity );
				}
			}

			$checkout_shipping = $checkout_order['shipping_total'];
			$current_shipping  = $current_order->get_shipping_total( false );
			if ( $current_shipping !== $checkout_shipping ) {
				$shipping_lines       = $current_order->get_items( 'shipping' );
				$shipping_id          = explode( ':', $checkout_order['selected_shipping_id'] );
				$shipping_method_id   = $shipping_id[0];
				$shipping_instance_id = $shipping_id[1];
				$selected_shipping    = $checkout_order['selected_shipping'];
				foreach ( $shipping_lines as $shipping_line ) {
					// Check if the current shipping method is flat-rate:1
					if ( $shipping_method_id !== $shipping_line->get_method_id() && $shipping_instance_id !== $shipping_line->get_instance_id() ) {
						$shipping_line->set_meta_data( $selected_shipping );
						$shipping_line->set_method_id( $shipping_method_id );
						$shipping_line->set_instance_id( $shipping_instance_id );
						$shipping_line->set_method_title( $selected_shipping->get_label() );
						$shipping_line->set_total( $selected_shipping->get_cost() );
						$shipping_line->save();
					}
				}
			}

			$current_order->calculate_totals();
			$current_order->save();
		}

		return $current_order;
	}

	/**
	 * Uses a method (get_option) from WordPress
	 */
	private function get_db_settings() {
		if ( empty( $this->settings ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$this->settings = get_option( $this->plugin_id . $this->id . '_settings', [] );
		}
		return $this->settings;
	}

	private function has_valid_required_fields(): bool {
		$version          = $this->get_version();
		$environment      = $this->get_environment();
		$access_token     = $this->get_access_token();
		$configuration_id = $this->get_configuration_id();

		return isset( $version ) && isset( $environment ) && isset( $access_token ) && isset( $configuration_id );
	}
}
