<?php

namespace PowerBoard\Services\Validation;

use Exception;
use PowerBoard\Enums\CredentialSettingsEnum;
use PowerBoard\Enums\EnvironmentSettingsEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Services\SettingsService;

class ConnectionValidationService {
	public $service                       = null;
	private $errors                       = [];
	private $data                         = [];
	private $environment_settings         = null;
	private $access_token_settings        = null;
	private $widget_access_token_settings = null;
	private $widget_api_adapter_service;


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
		$post_data = $this->service->get_post_data();
		foreach ( $this->service->get_form_fields() as $key => $field ) {
			try {
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
				$this->service->add_error( $e->getMessage() );
			}
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
		if ( $this->access_token_settings === '********************' || $this->access_token_settings === null ) {
			$this->access_token_settings = $this->service->get_access_token();
		}

		$widget_access_token_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			[
				SettingGroupsEnum::CREDENTIALS,
				CredentialSettingsEnum::WIDGET_KEY,
			]
		);
		$this->widget_access_token_settings = $this->data[ $widget_access_token_settings_key ];
		if ( $this->widget_access_token_settings === '********************' || $this->widget_access_token_settings === null ) {
			$this->widget_access_token_settings = $this->service->get_widget_access_token();
		}
	}

	public function get_errors(): array {
		return $this->errors;
	}
}
