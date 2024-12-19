<?php

namespace PowerBoard\Services\Validation;

use Exception;
use PowerBoard\API\ConfigService;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\EnvironmentSettings;
use PowerBoard\Enums\MasterWidgetSettings;
use PowerBoard\Enums\NotificationEvents;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Helpers\MasterWidgetTemplatesHelper;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class ConnectionValidationService {
	private $old_access_token        = null;
	private $old_widget_access_token = null;

	private const IS_WEBHOOK_SET_OPTION = 'is_power_board_webhook_set';

	public $service                         = null;
	private $errors                         = array();
	private $result                         = array();
	private $data                           = array();
	private $access_token_validation_failed = false;
	private $configuration_templates        = array();
	private $customisation_templates        = array();
	private $api_adapter_service            = null;
	private $widget_api_adapter_service     = null;

	private $environment_settings         = null;
	private $access_token_settings        = null;
	private $widget_access_token_settings = null;
	private $version_settings             = null;


	public function __construct( WidgetConfigurationSettingService $service ) {
		$this->service = $service;
		$this->prepare_form_data();

		$this->set_api_init_variables();

		$this->api_adapter_service        = SDKAdapterService::get_instance();
		$this->widget_api_adapter_service = APIAdapterService::get_instance();
		$this->widget_api_adapter_service->initialise( $this->environment_settings, $this->access_token_settings, $this->widget_access_token_settings );

		$this->validate();

		$option_key = $service->get_option_key();
		do_action( 'woocommerce_update_option', array( 'id' => $option_key ) );

		update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings ),
			'yes'
		);
	}

	private function prepare_form_data(): void {
		$post_data = $this->service->get_post_data();
		foreach ( $this->service->get_form_fields() as $key => $field ) {
			try {
				$this->data[ $key ]   = $this->service->get_field_value( $key, $field, $post_data );
				$this->result[ $key ] = $this->data[ $key ];

				if ( 'select' === $field['type'] || 'checkbox' === $field['type'] ) {
					do_action(
						'woocommerce_update_non_option_setting',
						array(
							'id'    => $key,
							'type'  => $field['type'],
							'value' => $this->data[ $key ],
						)
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
			array(
				SettingGroups::ENVIRONMENT()->name,
				EnvironmentSettings::ENVIRONMENT()->name,
			)
		);
		$this->environment_settings = $this->data[ $environment_settings_key ];

		$version_settings_key = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			array(
				SettingGroups::CHECKOUT()->name,
				MasterWidgetSettings::VERSION()->name,
			)
		);
		// Change $this->service->settings to $this->data when we start showing version select box on form.
		$this->version_settings = $this->service->settings[ $version_settings_key ];

		$access_token_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			array(
				SettingGroups::CREDENTIALS()->name,
				CredentialSettings::ACCESS_KEY()->name,
			)
		);
		$this->access_token_settings = $this->data[ $access_token_settings_key ];
		if ( '********************' === $this->access_token_settings || null === $this->access_token_settings ) {
			$this->access_token_settings = $this->service->get_access_token();
		}

		$widget_access_token_settings_key   = SettingsService::get_instance()
		->get_option_name(
			$this->service->id,
			array(
				SettingGroups::CREDENTIALS()->name,
				CredentialSettings::WIDGET_KEY()->name,
			)
		);
		$this->widget_access_token_settings = $this->data[ $widget_access_token_settings_key ];
		if ( '********************' === $this->widget_access_token_settings || null === $this->widget_access_token_settings ) {
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
				! empty( $this->access_token_settings )
				&& ! empty( $this->widget_access_token_settings )
				&& $this->check_access_key_connection( $this->access_token_settings )
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
		if ( '********************' === $access_token || null === $access_token ) {
			$access_token = $this->old_access_token;
		}

		ConfigService::$access_token = $access_token;

		$this->get_configuration_templates();
		$this->get_customisation_templates();

		$this->restore_credential();

		if ( $this->access_token_validation_failed ) {
			ConfigService::$access_token = $access_token;
		}

		return ! $this->access_token_validation_failed;
	}

	private function get_configuration_templates() {
		$configuration_templates_result = $this->widget_api_adapter_service->get_configuration_templates_ids( $this->version_settings );
        $has_error = ! empty( $configuration_templates_result['error'] );
        $this->configuration_templates = MasterWidgetTemplatesHelper::mapTemplates($configuration_templates_result['resource']['data'], $has_error);

		if ( $has_error ) {
			$this->access_token_validation_failed = true;
		} else {
			set_transient( 'configuration_templates_' . $this->environment_settings, $this->configuration_templates, 60 );
		}
	}

	private function get_customisation_templates() {
		$customisation_templates_result = $this->widget_api_adapter_service->get_customisation_templates_ids( $this->version_settings );
        $has_error = ! empty( $customisation_templates_result['error'] );
        $this->configuration_templates = MasterWidgetTemplatesHelper::mapTemplates($customisation_templates_result['resource']['data'], $has_error);

        if ( ! $has_error ) {
			set_transient( 'customisation_templates_' . $this->environment_settings, $this->customisation_templates, 60 );
		}
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
		if ( '********************' === $widget_access_token || null === $widget_access_token ) {
			$widget_access_token = $this->old_widget_access_token;
		}

		ConfigService::$widget_access_token = $widget_access_token;

		$result = $this->api_adapter_service->token(
			array(
				'gateway_id' => '',
				'type'       => '',
			),
			true
		);
		$result = empty( $result['error'] );

		$this->restore_credential();

		return $result;
	}

	private function set_webhooks(): void {
		$webhook_events = NotificationEvents::events();
		if ( false !== strpos( get_site_url(), 'localhost' ) ) {
			return;
		}

		$not_setted_webhooks   = $webhook_events;
		$webhook_site_url      = get_site_url() . '/wc-api/power-board-webhook/';
		$should_create_webhook = true;
		$webhook_request       = $this->api_adapter_service->search_notifications( array( 'type' => 'webhook' ) );
		if ( ! empty( $webhook_request['resource']['data'] ) ) {
			$events = array();
			foreach ( $webhook_request['resource']['data'] as $webhook ) {
				if ( $webhook['destination'] === $webhook_site_url ) {
					$events[] = $webhook['event'];
				}
			}

			$not_setted_webhooks = array_diff( $webhook_events, $events );
			if ( empty( $not_setted_webhooks ) ) {
				$should_create_webhook = false;
			}
		}

		$webhook_ids = array();
		if ( $should_create_webhook ) {
			foreach ( $not_setted_webhooks as $event ) {
				$result = $this->api_adapter_service->create_notification(
					array(
						'event'            => $event,
						'destination'      => $webhook_site_url,
						'type'             => 'webhook',
						'transaction_only' => false,
					)
				);

				if ( ! empty( $result['resource']['data']['_id'] ) ) {
					$webhook_ids[] = $result['resource']['data']['_id'];
				} else {
					$this->errors[] = __(
						'Can\'t create webhook',
						'power-board'
					) . ( ! empty( $result['error'] ) ? ' ' . wp_json_encode( $result['error'] ) : '' );

					return;
				}
			}

			if ( ! empty( $webhook_ids ) ) {
				update_option( self::IS_WEBHOOK_SET_OPTION, $webhook_ids );
			}
		} else {
			return;
		}
	}

	public function get_errors(): array {
		return $this->errors;
	}
}
