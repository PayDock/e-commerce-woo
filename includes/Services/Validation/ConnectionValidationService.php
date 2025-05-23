<?php
declare( strict_types=1 );

namespace WooPlugin\Services\Validation;

use Exception;
use WooPlugin\API\ConfigService;
use WooPlugin\Enums\EnvironmentSettingsEnum;
use WooPlugin\Enums\MasterWidgetSettingsEnum;
use WooPlugin\Enums\SettingGroupsEnum;
use WooPlugin\Helpers\MasterWidgetTemplatesHelper;
use WooPlugin\Helpers\SettingsHelper;
use WooPlugin\Services\Settings\APIAdapterService;
use WooPlugin\Services\PaymentGateway\MasterWidgetPaymentService;

class ConnectionValidationService {
	private ?string $old_access_token                     = null;
	private static bool $invalid_credentials_shown_global = false;
	private static bool $no_version_selected_shown_global = false;
	private static bool $no_config_template_shown_global  = false;

	public ?MasterWidgetPaymentService $service = null;
	private ?array $errors                      = [];
	private ?array $data                        = [];
	private ?string $environment_settings       = null;
	private ?string $access_token_settings      = null;
	private ?string $configuration_id_settings  = null;
	private ?string $checkout_version           = null;
	private APIAdapterService $widget_api_adapter_service;


	/**
	 * Uses functions (do_action, update_option and apply_filters) from WordPress
	 * Uses a method (get_option_key) from WooCommerce
	 */
	public function __construct( MasterWidgetPaymentService $service ) {
		$this->service = $service;
		$this->prepare_form_data();

		$this->set_api_init_variables();

		$this->widget_api_adapter_service = APIAdapterService::get_instance();
		$this->widget_api_adapter_service->initialise( $this->environment_settings, $this->access_token_settings );

		$this->validate();
		/* @noinspection PhpUndefinedMethodInspection */
		$option_key = $service->get_option_key();
		/* @noinspection PhpUndefinedFunctionInspection */
		do_action( 'woocommerce_update_option', [ 'id' => $option_key ] );

		/**
		 * Filter
		 *
		 * @noinspection PhpUndefinedFunctionInspection
		 * @since 1.0.0
		 */
		update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings ),
			'yes'
		);
	}

	/**
	 * Uses a function (do_action) from WordPress
	 */
	private function prepare_form_data(): void {
		/* @noinspection PhpUndefinedMethodInspection */
		$post_data = $this->service->get_post_data();
		/* @noinspection PhpUndefinedMethodInspection */
		foreach ( $this->service->get_form_fields() as $key => $field ) {
			try {
				/* @noinspection PhpUndefinedMethodInspection */
				$this->data[ $key ] = $this->service->get_field_value( $key, $field, $post_data );

				if ( $field['type'] === 'select' || $field['type'] === 'checkbox' ) {
					/* @noinspection PhpUndefinedFunctionInspection */
					do_action(
						'woocommerce_update_non_option_setting',
						[
							'id'    => $key,
							'type'  => $field['type'],
							'value' => $this->data[ $key ],
						]
					);
				}
			} catch ( Exception $e ) {
				/* @noinspection PhpUndefinedMethodInspection */
				$this->service->add_error( $e->getMessage() );
			}
		}
	}

	private function validate(): void {
		if ( $this->validate_environment() ) {
			$this->validate_credential();
		}
	}

	private function set_api_init_variables(): void {
		$environment_settings_key   = SettingsHelper::get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::ENVIRONMENT,
				EnvironmentSettingsEnum::ENVIRONMENT,
			]
		);
		$this->environment_settings = $this->data[ $environment_settings_key ];

		$version_settings_key   = SettingsHelper::get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CHECKOUT,
				MasterWidgetSettingsEnum::VERSION,
			]
		);
		$this->checkout_version = $this->data[ $version_settings_key ];

		$access_token_settings_key   = SettingsHelper::get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				'ACCESS_KEY',
			]
		);
		$this->access_token_settings = $this->data[ $access_token_settings_key ];

		$configuration_template_setting_key = SettingsHelper::get_option_name(
																$this->service->id,
																[
																	SettingGroupsEnum::CHECKOUT,
																	MasterWidgetSettingsEnum::CONFIGURATION_ID,
																]
															);
		$this->configuration_id_settings    = $this->data[ $configuration_template_setting_key ];
	}

	private function validate_environment(): bool {
		if ( ! empty( $this->environment_settings ) ) {
			return true;
		}

		$this->errors[] = 'No environment selected. Please select an environment and try again.';
		return false;
	}

	private function validate_credential(): void {
		if (
			$this->access_token_settings === '********************'
		) {
			if ( $this->validate_checkout_version() ) {
				$this->check_is_configuration_template_selected();
			}
		} else {
			if (
				$this->check_access_key_connection( $this->access_token_settings )
			) {
				if ( $this->validate_checkout_version() ) {
					$this->get_configuration_templates();
					$this->get_customisation_templates();
				}
				return;
			}

			if ( ! self::$invalid_credentials_shown_global ) {
				$this->errors[]                         = 'Invalid credentials. Please update and try again.';
				self::$invalid_credentials_shown_global = true;
			}
		}
	}

	private function validate_checkout_version(): bool {
		if ( ! empty( $this->checkout_version ) ) {
			return true;
		}

		if ( ! self::$no_version_selected_shown_global ) {
			$this->errors[]                         = 'No checkout version selected. Please select a version and try again.';
			self::$no_version_selected_shown_global = true;
		}

		return false;
	}

	private function check_is_configuration_template_selected(): void {
		if ( empty( $this->configuration_id_settings ) ) {
			if ( ! self::$no_config_template_shown_global ) {
				$this->errors[]                        = 'No configuration template ID selected. Please select a template and try again.';
				self::$no_config_template_shown_global = true;
			}
		}
	}

	private function check_access_key_connection( ?string $access_token ): bool {
		$access_token_validation_failed = false;
		if ( $access_token !== '********************' ) {
			$this->save_old_credential();
			ConfigService::$access_token = $access_token;

			$access_token_validation_failed = ! $this->get_configuration_templates_for_validation();

			$this->restore_credential();

			if ( ! $access_token_validation_failed ) {
				ConfigService::$access_token = $access_token;
			}
		}

		return ! $access_token_validation_failed;
	}

	/**
	 * Uses functions (set_transient) from WordPress
	 */
	private function get_configuration_templates(): void {
		$configuration_templates_result = $this->widget_api_adapter_service->get_configuration_templates_ids( $this->checkout_version );
		$has_error                      = ! empty( $configuration_templates_result['error'] );
		$configuration_templates        = MasterWidgetTemplatesHelper::map_templates( $configuration_templates_result['resource']['data'], $has_error );

		if ( ! $has_error ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_configuration_templates_' . $this->environment_settings, $configuration_templates, 60 );
		}

		$configuration_id_key = SettingsHelper::get_option_name(
				$this->service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CONFIGURATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $configuration_templates, $has_error, $configuration_id_key, MasterWidgetSettingsEnum::CONFIGURATION_ID );
	}

	private function get_configuration_templates_for_validation(): bool {
		$configuration_templates_result = $this->widget_api_adapter_service->get_configuration_templates_for_validation();
		$has_error                      = ! empty( $configuration_templates_result['error'] );

		if ( $has_error ) {
			return false;
		}

		return true;
	}

	/**
	 * Uses functions (set_transient) from WordPress
	 */
	private function get_customisation_templates(): void {
		$customisation_templates_result = $this->widget_api_adapter_service->get_customisation_templates_ids( $this->checkout_version );
		$has_error                      = ! empty( $customisation_templates_result['error'] );
		$customisation_templates        = MasterWidgetTemplatesHelper::map_templates( $customisation_templates_result['resource']['data'], $has_error, true );

		if ( ! $has_error ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_customisation_templates_' . $this->environment_settings, $customisation_templates, 60 );
		}

		$customisation_id_key = SettingsHelper::get_option_name(
				$this->service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CUSTOMISATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $customisation_templates, $has_error, $customisation_id_key, MasterWidgetSettingsEnum::CUSTOMISATION_ID );
	}

	private function save_old_credential(): void {
		$this->old_access_token = ConfigService::$access_token;
	}

	private function restore_credential(): void {
		ConfigService::$access_token = $this->old_access_token;
	}

	public function get_errors(): array {
		return array_unique( $this->errors );
	}
}
