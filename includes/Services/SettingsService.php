<?php
declare( strict_types=1 );

namespace WooPlugin\Services;

use WooPlugin\Enums\MasterWidgetSettingsEnum;
use WooPlugin\Enums\SettingGroupsEnum;
use WooPlugin\Helpers\SettingsHelper;
use WooPlugin\Services\Settings\APIAdapterService;
use WooPlugin\Services\PaymentGateway\MasterWidgetPaymentService;

final class SettingsService {
	private static ?SettingsService $instance           = null;
	private ?MasterWidgetPaymentService $widget_service = null;

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_environment(): ?string {
		$widget_service = $this->get_settings_service();
		return $widget_service->get_environment();
	}

	private function get_settings_service(): MasterWidgetPaymentService {
		if ( ! is_null( $this->widget_service ) ) {
			return $this->widget_service;
		}

		$this->widget_service = MasterWidgetPaymentService::get_instance();

		return $this->widget_service;
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
		return $widget_service->get_version();
	}

	/**
	 * Uses a method (get_option) from WC_Payment_Gateway
	 */
	public function get_checkout_customisation_id(): ?string {
		$widget_service = $this->get_settings_service();

		/* @noinspection PhpUndefinedMethodInspection */
		return $widget_service->get_option(
			SettingsHelper::get_option_name(
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
		return $widget_service->get_configuration_id();
	}

	/**
	 * Uses functions (get_transient, set_transient) from WordPress
	 */
	private function get_plugin_configuration_environments(): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$stored_configuration_environment = get_transient( PLUGIN_PREFIX . '_environment_url' );

		if ( ! empty( $stored_configuration_environment ) ) {
			$plugin_configuration_environments = $stored_configuration_environment;
		} else {
			$widget_api_adapter_service = APIAdapterService::get_instance();
			$widget_api_adapter_service->initialise( $this->get_environment(), $this->get_access_token() );
			$plugin_configuration              = $widget_api_adapter_service->get_plugin_configuration_by_version();
			$plugin_configuration_environments = $plugin_configuration['environment_url'];

			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_environment_url', $plugin_configuration_environments, 60 );
		}

		return $plugin_configuration_environments;
	}

	public function get_widget_script_url(): string {
		$environment = $this->get_environment();

		$plugin_configuration_environments = $this->get_plugin_configuration_environments();

		switch ( $environment ) {
			case PLUGIN_PRODUCTION_ENVIRONMENT_VALUE:
				$environment_key = PLUGIN_PRODUCTION_ENVIRONMENT_URL_KEY;
				break;
			case PLUGIN_SANDBOX_ENVIRONMENT_VALUE:
				$environment_key = PLUGIN_SANDBOX_ENVIRONMENT_URL_KEY;
				break;
			case PLUGIN_STAGING_ENVIRONMENT_VALUE:
				$environment_key = PLUGIN_STAGING_ENVIRONMENT_URL_KEY;
		}

		if ( !empty( $environment_key ) ) {
			if ( $environment_key == 'sandbox' ) {
				return 'https://widget.paydock.com/sdk/v1.122.3-beta/widget.umd.min.js';
			} else {
				return 'https://widget.paydock.com/sdk/v1.112.30-beta/widget.umd.js';
			}
		} else {
			return ' ';
		}
	}
}
