<?php declare( strict_types=1 );
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 */

namespace PowerBoard\Services\Settings;

use Exception;
use PowerBoard\Enums\CredentialSettingsEnum;
use PowerBoard\Enums\EnvironmentSettingsEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Enums\SettingsSectionEnum;
use PowerBoard\Helpers\CredentialSettingsHelper;
use PowerBoard\Helpers\EnvironmentSettingsHelper;
use PowerBoard\Helpers\MasterWidgetSettingsHelper;
use PowerBoard\Helpers\SettingGroupsHelper;
use PowerBoard\Services\Assets\AdminAssetsService;
use PowerBoard\Services\HashService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use PowerBoard\Services\Validation\ConnectionValidationService;
use WC_Admin_Settings;
use WC_Blocks_Utils;
use WC_Payment_Gateway;

/**
 * Some properties used comes from the extension WC_Payment_Gateway from WooCommerce
 *
 * @property string $id
 * @property string $title
 * @property string $method_title
 * @property string $method_description
 * @property string $icon
 * @property bool $has_fields
 * @property string $plugin_id
 * @property array $settings
 * @property array $form_fields
 */
class WidgetConfigurationSettingService extends WC_Payment_Gateway {
	protected ?SettingsService $service = null;
	protected TemplateService $template_service;

	/**
	 * Uses functions (__, is_checkout and is_admin) from WordPress
	 * Uses a function (wc_get_page_id) from WooCommerce
	 * Uses a method (init_settings) from WC_Payment_Gateway
	 */
	public function __construct() {
		$this->service = SettingsService::get_instance();
		$this->id      = $this->get_id();
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->method_title = __( 'PowerBoard Gateway', 'power-board' );
		$this->title        = $this->method_title;
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->method_description = __(
			'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard\'s payment orchestration.',
			'power-board'
		);
		$this->icon               = POWER_BOARD_PLUGIN_URL . 'assets/images/logo.png';

		/* @noinspection PhpUndefinedMethodInspection */
		$this->init_settings();
		$this->init_form_fields();

		/* @noinspection PhpUndefinedFunctionInspection */
		$this->has_fields = is_checkout() && WC_Blocks_Utils::has_block_in_page( wc_get_page_id( 'checkout' ), 'woocommerce/checkout' );

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_admin() ) {
			new AdminAssetsService();
			$this->template_service = new TemplateService( $this );
		}

		foreach ( CredentialSettingsEnum::cases() as $credential_settings ) {
			$key = $this->service->get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CREDENTIALS,
					$credential_settings,
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
	}

	public function init_form_fields(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( !is_admin() ) {
			return;
		}
		foreach ( SettingGroupsEnum::cases() as $setting_group ) {
			$key = PLUGIN_PREFIX . '_' . $this->service->get_option_name(
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

	private function get_credential_options(): array {
		$fields = [];

		foreach ( CredentialSettingsEnum::cases() as $credential_settings ) {
			$key            = $this->service->get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CREDENTIALS,
					$credential_settings,
				]
			);
			$fields[ $key ] = [
				'type'  => CredentialSettingsHelper::get_input_type( $credential_settings ),
				'title' => CredentialSettingsHelper::get_label( $credential_settings ),
			];
			$description    = CredentialSettingsHelper::get_description( $credential_settings );
			if ( $description ) {
				$fields[ $key ]['description'] = $description;
				$fields[ $key ]['desc_tip']    = true;
			}
		}

		return $fields;
	}

	private function get_checkout_options(): array {
		$fields              = [];
		$access_token        = $this->get_access_token();
		$widget_access_token = $this->get_widget_access_token();
		$environment         = $this->get_environment();
		$version             = $this->get_version();

		foreach ( MasterWidgetSettingsEnum::cases() as $checkout_settings ) {
			$key = $this->service->get_option_name(
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
				$options = MasterWidgetSettingsHelper::get_options_for_ui( $checkout_settings, $environment, $access_token, $widget_access_token, $version );

				if ( ! empty( $options ) && ( MasterWidgetSettingsHelper::get_input_type( $checkout_settings ) ) === 'select' ) {
					$fields[ $key ]['options'] = $options;
					$fields[ $key ]['class']   = PLUGIN_PREFIX . '-settings' . ( MasterWidgetSettingsEnum::CUSTOMISATION_ID === $checkout_settings ? ' is-optional' : '' );
					$fields[ $key ]['default'] = MasterWidgetSettingsHelper::get_default( $checkout_settings );
				}
			}
		}

		return $fields;
	}

	private function get_environment_options(): array {
		$fields = [];
		foreach ( EnvironmentSettingsEnum::cases() as $environment_settings ) {
			$key = $this->service->get_option_name(
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
				$fields[ $key ]['class']   = PLUGIN_PREFIX . '-settings';
				$fields[ $key ]['default'] = EnvironmentSettingsHelper::get_default( $environment_settings );
			}
		}

		return $fields;
	}

	public function get_access_token(): ?string {
		$token_key = $this->service->get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				CredentialSettingsEnum::ACCESS_KEY,
			]
		);
		if ( array_key_exists( $token_key, $this->settings ) ) {
			try {
				$decrypted_key = HashService::decrypt( $this->settings[ $token_key ] );
			} catch ( Exception $error ) {
				$decrypted_key = null;
			}
			return $decrypted_key;
		}
		return null;
	}

	public function get_widget_access_token(): ?string {
		$widget_token_key = $this->service->get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				CredentialSettingsEnum::WIDGET_KEY,
			]
		);
		if ( array_key_exists( $widget_token_key, $this->settings ) ) {
			try {
				$decrypted_key = HashService::decrypt( $this->settings[ $widget_token_key ] );
			} catch ( Exception $error ) {
				$decrypted_key = null;
			}
			return $decrypted_key;
		}
		return null;
	}

	public function get_environment(): ?string {
		$environment_key = $this->service->get_option_name(
			$this->id,
			[
				SettingGroupsEnum::ENVIRONMENT,
				EnvironmentSettingsEnum::ENVIRONMENT,
			]
		);
		if ( array_key_exists( $environment_key, $this->settings ) ) {
			return $this->settings[ $environment_key ];
		}
		return null;
	}

	public function get_version(): ?string {
		$version_key = $this->service->get_option_name(
			$this->id,
			[
				SettingGroupsEnum::CHECKOUT,
				MasterWidgetSettingsEnum::VERSION,
			]
		);
		if ( array_key_exists( $version_key, $this->settings ) ) {
			return $this->settings[ $version_key ];
		}
		return null;
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
		foreach ( CredentialSettingsEnum::cases() as $credential_settings ) {
			$key                            = $this->service->get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CREDENTIALS,
					$credential_settings,
				]
			);
			$hashed_credential_keys[ $key ] = $credential_settings;
			$settings_keys[ $key ]          = $credential_settings;
		}

		foreach ( EnvironmentSettingsEnum::cases() as $environment_settings ) {
			$key                   = $this->service->get_option_name(
				$this->id,
				[
					SettingGroupsEnum::ENVIRONMENT,
					$environment_settings,
				]
			);
			$settings_keys[ $key ] = $environment_settings;
		}
		foreach ( MasterWidgetSettingsEnum::cases() as $master_widget_settings ) {
			$key                   = $this->service->get_option_name(
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

	protected function get_id(): string {
		return SettingsSectionEnum::WIDGET_CONFIGURATION;
	}

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

		foreach ( CredentialSettingsEnum::cases() as $credential_settings ) {
			$credential_key = $this->service->get_option_name(
				$this->id,
				[
					SettingGroupsEnum::CREDENTIALS,
					$credential_settings,
				]
			);

			$this->settings[ $credential_key ] = ! empty( $this->settings[ $credential_key ] ) ? '********************' : '';
		}

		$form_fields = compact( 'form_fields' );

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
}
