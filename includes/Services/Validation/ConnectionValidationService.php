<?php

namespace PowerBoard\Services\Validation;

use Exception;
use PowerBoard\API\ConfigService;
use PowerBoard\Enums\CredentialSettingsEnum;
use PowerBoard\Enums\EnvironmentSettingsEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Helpers\MasterWidgetTemplatesHelper;
use PowerBoard\Helpers\NotificationEventsHelper;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Services\SettingsService;

class ConnectionValidationService {
	private ?string $old_environment         = null;
	private ?string $old_access_token        = null;
	private ?string $old_widget_access_token = null;

	private const IS_WEBHOOK_SET_OPTION = 'is_power_board_webhook_set';

	public ?WidgetConfigurationSettingService $service = null;
	private ?array $errors                             = [];
	private ?array $data                               = [];
	private bool $access_token_validation_failed       = false;
	private ?string $environment_settings              = null;
	private ?string $access_token_settings             = null;
	private ?string $widget_access_token_settings      = null;
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

	private function validate(): void {
		if ( $this->validate_environment() ) {
			if ( $this->validate_credential() ) {
				$this->set_webhooks();
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

	private function validate_environment(): bool {
		if ( ! empty( $this->environment_settings ) ) {
			return true;
		}

		$this->errors[] = 'No environment selected. Please select an environment and try again. ';
		return false;
	}

	private function validate_credential(): bool {
		if (
			$this->check_access_key_connection( $this->access_token_settings )
			&& $this->check_widget_key_connection( $this->widget_access_token_settings )
		) {
			return true;
		}

		$this->errors[] = 'Invalid credentials. Please update and try again. ';

		return false;
	}

	private function check_access_key_connection( ?string $access_token ): bool {
		$this->access_token_validation_failed = false;
		$this->save_old_credential();
		if ( $access_token === '********************' ) {
			$access_token = $this->old_access_token;
		}

		ConfigService::$access_token = $access_token;

		$this->get_configuration_templates();
		$this->get_customisation_templates();

		$this->restore_credential();

		if ( ! $this->access_token_validation_failed ) {
			ConfigService::$access_token = $access_token;
		}

		return ! $this->access_token_validation_failed;
	}

	/**
	 * Uses functions (set_transient) from WordPress
	 */
	private function get_configuration_templates() {
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
	private function get_customisation_templates() {
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

	private function save_old_credential() {
		$this->old_access_token        = ConfigService::$access_token;
		$this->old_widget_access_token = ConfigService::$widget_access_token;
	}

	private function restore_credential() {
		ConfigService::$access_token        = $this->old_access_token;
		ConfigService::$widget_access_token = $this->old_widget_access_token;
	}

	private function check_widget_key_connection( ?string $widget_access_token ): bool {
		$this->save_old_credential();
		if ( $widget_access_token === '********************' ) {
			$widget_access_token = $this->old_widget_access_token;
		}

		ConfigService::$widget_access_token = $widget_access_token;

		$result    = $this->api_adapter_service->token();
		$valid_key = empty( $result['error'] );

		$this->restore_credential();

		return $valid_key;
	}

	/**
	 * Uses a function (get_site_url, __, wp_json_encode, update_option) from WordPress
	 */
	private function set_webhooks(): void {
		$webhook_events = NotificationEventsHelper::events();
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( strpos( get_site_url(), 'localhost' ) !== false ) {
			return;
		}

		$not_set_webhooks = $webhook_events;
		/* @noinspection PhpUndefinedFunctionInspection */
		$webhook_site_url      = get_site_url() . '/wc-api/power-board-webhook/';
		$should_create_webhook = true;
		$webhook_request       = $this->api_adapter_service->search_notifications( [ 'type' => 'webhook' ] );

		if ( ! empty( $webhook_request['resource']['data'] ) ) {
			$events = [];
			foreach ( $webhook_request['resource']['data'] as $webhook ) {
				if ( $webhook['destination'] === $webhook_site_url ) {
					$events[] = $webhook['event'];
				}
			}

			$not_set_webhooks = array_diff( $webhook_events, $events );
			if ( empty( $not_set_webhooks ) ) {
				$should_create_webhook = false;
			}
		}

		$webhook_ids = [];
		if ( $should_create_webhook ) {
			foreach ( $not_set_webhooks as $event ) {
				$result = $this->api_adapter_service->create_notification(
					[
						'event'            => $event,
						'destination'      => $webhook_site_url,
						'type'             => 'webhook',
						'transaction_only' => false,
					]
				);

				if ( ! empty( $result['resource']['data']['_id'] ) ) {
					$webhook_ids[] = $result['resource']['data']['_id'];
				} else {
					/* @noinspection PhpUndefinedFunctionInspection */
					$this->errors[] = __(
						'Can\'t create webhook',
						'power-board'
					) . ( ! empty( $result['error'] ) ? ' ' . wp_json_encode( $result['error'] ) : '' );

					return;
				}
			}

			if ( ! empty( $webhook_ids ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				update_option( self::IS_WEBHOOK_SET_OPTION, $webhook_ids );
			}
		}
	}

	public function get_errors(): array {
		return $this->errors;
	}
}
