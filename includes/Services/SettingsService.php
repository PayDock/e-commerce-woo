<?php

namespace PowerBoard\Services;

use PowerBoard\Enums\ConfigAPIEnum;
use PowerBoard\Enums\MasterWidgetSettingsEnum;
use PowerBoard\Enums\SettingGroupsEnum;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

final class SettingsService {
	private static $instance = null;
	private $widget_service  = null;
	private $environment     = null;

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

	public function get_widget_access_token(): ?string {
		$widget_service = $this->get_settings_service();
		return $widget_service->get_widget_access_token();
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

	public function get_widget_script_url(): string {
		if ( empty( $this->environment ) ) {
			$this->get_environment();
		}

		if ( ConfigAPIEnum::PRODUCTION_ENVIRONMENT === $this->environment ) {
			$sdk_url = ConfigAPIEnum::PRODUCTION_WIDGET_URL;
		} elseif ( ConfigAPIEnum::STAGING_ENVIRONMENT === $this->environment ) {
			$sdk_url = ConfigAPIEnum::STAGING_WIDGET_URL;
		} else {
			$sdk_url = ConfigAPIEnum::SANDBOX_WIDGET_URL;
		}

		return strtr( $sdk_url, [ '{version}' => 'v1.116.3-beta' ] );
	}

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
