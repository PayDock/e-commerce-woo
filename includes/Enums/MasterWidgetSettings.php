<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\Helpers\MasterWidgetTemplatesHelper;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\SettingsService;

class MasterWidgetSettings extends AbstractEnum {
	protected const VERSION          = 'VERSION';
	protected const CONFIGURATION_ID = 'CONFIGURATION_ID';
	protected const CUSTOMISATION_ID = 'CUSTOMISATION_ID';

	private $api_adapter_service     = null;
	private $configuration_templates = null;
	private $customisation_templates = null;

	public function get_input_type(): string {
		switch ( $this->name ) {
			case self::VERSION:
			case self::CONFIGURATION_ID:
			case self::CUSTOMISATION_ID:
			default:
				return 'select';
		}
	}

	public function get_label(): string {
		switch ( $this->name ) {
			case self::VERSION:
				return 'Version';
			case self::CONFIGURATION_ID:
				return 'Configuration Template ID';
			case self::CUSTOMISATION_ID:
				return 'Customisation Template ID (optional)';
			default:
				return ucfirst( strtolower( str_replace( '_', ' ', $this->name ) ) );
		}
	}

	public function get_options_for_ui( $env, $access_token, $widget_access_token, $version ): array {
		switch ( $this->name ) {
			case self::VERSION:
				return $this->get_versions_for_ui();
			case self::CONFIGURATION_ID:
				return $this->get_configuration_ids_for_ui( $env, $access_token, $widget_access_token, $version );
			case self::CUSTOMISATION_ID:
				return $this->get_customisation_ids_for_ui( $env, $access_token, $widget_access_token, $version );
			default:
				return [];
		}
	}

	public function get_versions_for_ui(): array {
		return [ '1' => '1' ];
	}

	public function get_configuration_ids_for_ui( $env, $access_token, $widget_access_token, $version ): array {
		$stored_configuration_templates = get_transient( 'configuration_templates_' . $env );
		$has_error                      = false;
		if ( ! empty( $stored_configuration_templates ) ) {
			$this->configuration_templates = $stored_configuration_templates;
		} else {
			$this->init_api_adapter( $env, $access_token, $widget_access_token );
			$result                        = $this->api_adapter_service->get_configuration_templates_ids( $version );
			$has_error                     = $result['error'];
			$this->configuration_templates = MasterWidgetTemplatesHelper::map_templates( $result['resource']['data'], ! empty( $has_error ) );

			set_transient( 'configuration_templates_' . $env, $this->configuration_templates, 60 );
		}

		$configuration_id_key = SettingsService::get_instance()
			->get_option_name(
				'power_board',
				[
					SettingGroups::CHECKOUT()->name,
					self::CONFIGURATION_ID()->name,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $this->configuration_templates, ! empty( $has_error ), $configuration_id_key );

		return $this->configuration_templates;
	}

	public function get_customisation_ids_for_ui( $env, $access_token, $widget_access_token, $version ): array {
		$stored_customisation_templates = get_transient( 'customisation_templates_' . $env );
		$has_error                      = false;
		if ( ! empty( $stored_customisation_templates ) ) {
			$this->customisation_templates = $stored_customisation_templates;
		} else {

			$this->init_api_adapter( $env, $access_token, $widget_access_token );
			$result                        = $this->api_adapter_service->get_customisation_templates_ids( $version );
			$has_error                     = $result['error'];
			$this->customisation_templates = MasterWidgetTemplatesHelper::map_templates( $result['resource']['data'], ! empty( $has_error ), true );

			set_transient( 'customisation_templates_' . $env, $this->customisation_templates, 60 );
		}

		$customisation_id_key = SettingsService::get_instance()
			->get_option_name(
				'power_board',
				[
					SettingGroups::CHECKOUT()->name,
					self::CUSTOMISATION_ID()->name,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $this->customisation_templates, ! empty( $has_error ), $customisation_id_key );

		return $this->customisation_templates;
	}

	public function get_default(): string {
		switch ( $this->name ) {
			case self::VERSION:
				return '1';
			default:
				return '';
		}
	}

	protected function init_api_adapter( $env, $access_token, $widget_access_token ) {
		if ( empty( $this->api_adapter_service ) ) {
			$this->api_adapter_service = APIAdapterService::get_instance();
		}
		$this->api_adapter_service->initialise( $env, $access_token, $widget_access_token );
	}
}
