<?php
declare( strict_types=1 );

namespace PowerBoard\Services;

use PowerBoard\Enums\ConfigAPIEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

final class SettingsService {
	private static ?SettingsService $instance                  = null;
	private ?WidgetConfigurationSettingService $widget_service = null;
	private ?string $environment                               = null;

	public function get_environment(): ?string {
		$widget_service    = $this->get_settings_service();
		$this->environment = $widget_service->get_environment();
		return $this->environment;
	}

	private function get_settings_service(): WidgetConfigurationSettingService {
		if ( ! is_null( $this->widget_service ) ) {
			return $this->widget_service;
		}

		$this->widget_service = new WidgetConfigurationSettingService();

		return $this->widget_service;
	}

	public function get_option_name( string $id, array $fragments ): string {
		return implode( '_', array_merge( [ $id ], $fragments ) );
	}

	public function get_access_token(): ?string {
		$widget_service = $this->get_settings_service();
		return $widget_service->get_access_token();
	}

	/**
	 * Uses a method (get_option) from WC_Payment_Gateway
	 */
	public function get_checkout_template_version(): ?string {
		$widget_service = $this->get_settings_service();

		/* @noinspection PhpUndefinedMethodInspection */
		return $widget_service->get_option(
			$this->get_option_name(
				$widget_service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::VERSION,
				]
			)
		);
	}

	/**
	 * Uses a method (get_option) from WC_Payment_Gateway
	 */
	public function get_checkout_customisation_id(): ?string {
		$widget_service = $this->get_settings_service();

		/* @noinspection PhpUndefinedMethodInspection */
		return $widget_service->get_option(
			$this->get_option_name(
				$widget_service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CUSTOMISATION_ID,
				]
			)
		);
	}

	/**
	 * Uses a method (get_option) from WC_Payment_Gateway
	 */
	public function get_checkout_configuration_id(): ?string {
		$widget_service = $this->get_settings_service();

		/* @noinspection PhpUndefinedMethodInspection */
		return $widget_service->get_option(
			$this->get_option_name(
				$widget_service->id,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CONFIGURATION_ID,
				]
			)
		);
	}

	/**
	 * Uses functions (get_transient, set_transient) from WordPress
	 */
	private function get_plugin_configuration_environments(): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$stored_configuration_environment = get_transient( 'environment_url' );

		if ( ! empty( $stored_configuration_environment ) ) {
			$plugin_configuration_environments = $stored_configuration_environment;
		} else {
			$widget_api_adapter_service = APIAdapterService::get_instance();
			$widget_api_adapter_service->initialise( $this->get_environment(), $this->get_access_token() );
			$plugin_configuration              = $widget_api_adapter_service->get_plugin_configuration_by_version();
			$plugin_configuration_environments = $plugin_configuration['environment_url'];

			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( 'environment_url', $plugin_configuration_environments, 60 );
		}

		return $plugin_configuration_environments;
	}

	public function get_widget_script_url(): string {
		if ( empty( $this->environment ) ) {
			$this->get_environment();
		}

		$plugin_configuration_environments = $this->get_plugin_configuration_environments();

		switch ( $this->environment ) {
			case ConfigAPIEnum::PRODUCTION_ENVIRONMENT_VALUE:
				$environment_key = ConfigAPIEnum::PRODUCTION_ENVIRONMENT_URL_KEY;
				break;
			case ConfigAPIEnum::SANDBOX_ENVIRONMENT_VALUE:
				$environment_key = ConfigAPIEnum::SANDBOX_ENVIRONMENT_URL_KEY;
				break;
			case ConfigAPIEnum::STAGING_ENVIRONMENT_VALUE:
				$environment_key = ConfigAPIEnum::STAGING_ENVIRONMENT_URL_KEY;
		}

		return ! empty( $environment_key ) ? $plugin_configuration_environments[ $environment_key ] : '';
	}

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
