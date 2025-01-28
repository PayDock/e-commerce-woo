<?php

namespace PowerBoard\Services\Validation;

use Exception;
use PowerBoard\API\ConfigService;
use PowerBoard\Enums\CredentialSettingsEnum;
use PowerBoard\Enums\EnvironmentSettingsEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Helpers\MasterWidgetTemplatesHelper;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Services\SettingsService;

class ConnectionValidationService {
	private ?string $old_access_token        = null;
	private ?string $old_widget_access_token = null;

	public ?WidgetConfigurationSettingService $service = null;
	private ?array $errors                             = [];
	private ?array $data                               = [];
	private bool $access_token_validation_failed       = false;
	private ?string $environment_settings              = null;
	private ?string $access_token_settings             = null;
	private ?string $widget_access_token_settings      = null;
	private ?string $configuration_id_settings         = null;
	private ?string $version_settings                  = null;
	private SDKAdapterService $api_adapter_service;
	private APIAdapterService $widget_api_adapter_service;


	/**
	 * Uses functions (do_action, update_option and apply_filters) from WordPress
	 * Uses a method (get_option_key) from WooCommerce
	 */
	public function __construct( WidgetConfigurationSettingService $service ) {
		$this->service = $service;
		$this->prepare_form_data();

		$this->set_api_init_variables();

		$this->api_adapter_service        = SDKAdapterService::get_instance();
		$this->widget_api_adapter_service = APIAdapterService::get_instance();
		$this->widget_api_adapter_service->initialise( $this->environment_settings, $this->access_token_settings, $this->widget_access_token_settings );

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
		$environment_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::ENVIRONMENT,
				EnvironmentSettingsEnum::ENVIRONMENT,
			]
		);
		$this->environment_settings = $this->data[ $environment_settings_key ];

		$version_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CHECKOUT,
				MasterWidgetSettingsEnum::VERSION,
			]
		);
		$this->version_settings = $this->data[ $version_settings_key ];

		$access_token_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				CredentialSettingsEnum::ACCESS_KEY,
			]
		);
		$this->access_token_settings = $this->data[ $access_token_settings_key ];

		$widget_access_token_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				CredentialSettingsEnum::WIDGET_KEY,
			]
		);
		$this->widget_access_token_settings = $this->data[ $widget_access_token_settings_key ];

		$configuration_template_setting_key = SettingsService::get_instance()
															->get_option_name(
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

		$this->errors[] = 'No environment selected. Please select an environment and try again. ';
		return false;
	}

	private function validate_credential(): void {
		if (
			$this->access_token_settings === '********************'
			&& $this->widget_access_token_settings === '********************'
		) {
			$this->check_is_configuration_template_selected();
		} else {
			if (
				$this->check_access_key_connection( $this->access_token_settings )
				&& $this->check_widget_key_connection( $this->widget_access_token_settings )
			) {
				return;
			}

			$this->errors[] = 'Invalid credentials. Please update and try again. ';
		}
	}

	private function check_is_configuration_template_selected(): void {
		if ( empty( $this->configuration_id_settings ) ) {
			$this->errors[] = 'No configuration template ID selected. Please select a template and try again.';
		}
	}

	private function check_access_key_connection( ?string $access_token ): bool {
		$this->access_token_validation_failed = false;
		if ( $access_token !== '********************' ) {
			$this->save_old_credential();
			ConfigService::$access_token = $access_token;

			$this->get_configuration_templates();
			$this->get_customisation_templates();

			$this->restore_credential();

			if ( ! $this->access_token_validation_failed ) {
				ConfigService::$access_token = $access_token;
			}
		}

		return ! $this->access_token_validation_failed;
	}

	/**
	 * Uses functions (set_transient) from WordPress
	 */
	private function get_configuration_templates(): void {
		$configuration_templates_result = $this->widget_api_adapter_service->get_configuration_templates_ids( $this->version_settings );
		$has_error                      = ! empty( $configuration_templates_result['error'] );
		$configuration_templates        = MasterWidgetTemplatesHelper::map_templates( $configuration_templates_result['resource']['data'], $has_error );

		if ( $has_error ) {
			$this->access_token_validation_failed = true;
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( 'configuration_templates_' . $this->environment_settings, $configuration_templates, 60 );
		}

		$configuration_id_key = SettingsService::get_instance()
			->get_option_name(
				$this->service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CONFIGURATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $configuration_templates, $has_error, $configuration_id_key );
	}

	/**
	 * Uses functions (set_transient) from WordPress
	 */
	private function get_customisation_templates(): void {
		$customisation_templates_result = $this->widget_api_adapter_service->get_customisation_templates_ids( $this->version_settings );
		$has_error                      = ! empty( $customisation_templates_result['error'] );
		$customisation_templates        = MasterWidgetTemplatesHelper::map_templates( $customisation_templates_result['resource']['data'], $has_error, true );

		if ( ! $has_error ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( 'customisation_templates_' . $this->environment_settings, $customisation_templates, 60 );
		}

		$customisation_id_key = SettingsService::get_instance()
			->get_option_name(
				$this->service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CUSTOMISATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $customisation_templates, $has_error, $customisation_id_key );
	}

	private function save_old_credential(): void {
		$this->old_access_token        = ConfigService::$access_token;
		$this->old_widget_access_token = ConfigService::$widget_access_token;
	}

	private function restore_credential(): void {
		ConfigService::$access_token        = $this->old_access_token;
		ConfigService::$widget_access_token = $this->old_widget_access_token;
	}

	private function check_widget_key_connection( ?string $widget_access_token ): bool {
		$valid_key = true;

		if ( $widget_access_token !== '********************' ) {
			$this->save_old_credential();

			ConfigService::$widget_access_token = $widget_access_token;

			$result    = $this->api_adapter_service->token();
			$valid_key = empty( $result['error'] );

			$this->restore_credential();
		}

		return $valid_key;
	}

	public function get_errors(): array {
		return $this->errors;
	}
}
