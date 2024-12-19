<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\Services\Settings\APIAdapterService;

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
				return 'Configuration ID';
			case self::CUSTOMISATION_ID:
				return 'Customisation ID';
			default:
				return ucfirst( strtolower( str_replace( '_', ' ', $this->name ) ) );
		}
	}

	public function get_options( $env, $access_token, $widget_access_token, $version ): array {
		switch ( $this->name ) {
			case self::VERSION:
				return $this->get_versions();
			case self::CONFIGURATION_ID:
				return $this->get_configuration_ids( $env, $access_token, $widget_access_token, $version );
			case self::CUSTOMISATION_ID:
				return $this->get_customisation_ids( $env, $access_token, $widget_access_token, $version );
			default:
				return array();
		}
	}

	public function get_versions(): array {
		return array(
			'1' => '1',
		);
	}

	public function get_configuration_ids( $env, $access_token, $widget_access_token, $version ): array {
		$stored_configuration_templates = get_transient( 'configuration_templates_' . $env );
		if ( ! empty( $stored_configuration_templates ) ) {
			return $stored_configuration_templates;
		}

		$this->init_api_adapter( $env, $access_token, $widget_access_token );
		$result = $this->api_adapter_service->get_configuration_templates_ids( $version );

		if ( ! empty( $result['error'] ) ) {
			$this->configuration_templates = array();
		} else {
			$data = $result['resource']['data'];
			foreach ( $data as $configuration_template ) {
				$this->configuration_templates[ $configuration_template['_id'] ] = $configuration_template['label'] . ' | ' . $configuration_template['_id'];
			}
		}

		set_transient( 'configuration_templates_' . $env, $this->configuration_templates, 60 );
		return $this->configuration_templates;
	}

	public function get_customisation_ids( $env, $access_token, $widget_access_token, $version ): array {
		$stored_customisation_templates = get_transient( 'customisation_templates_' . $env );
		if ( ! empty( $stored_customisation_templates ) ) {
			return $stored_customisation_templates;
		}

		$this->init_api_adapter( $env, $access_token, $widget_access_token );
		$result = $this->api_adapter_service->get_customisation_templates_ids( $version );

		if ( ! empty( $result['error'] ) ) {
			$this->customisation_templates = array();
		} else {
			$data = $result['resource']['data'];
			foreach ( $data as $customisation_template ) {
				$this->customisation_templates[ $customisation_template['_id'] ] = $customisation_template['label'] . ' | ' . $customisation_template['_id'];
			}
			$this->customisation_templates = ! empty( $this->customisation_templates ) ? $this->customisation_templates + array( '' => '' ) : array();
		}

		set_transient( 'customisation_templates_' . $env, $this->customisation_templates, 60 );
		return $this->customisation_templates;
	}

	public function get_default() {
		switch ( $this->name ) {
			case self::VERSION:
				$result = '1';
				break;
			default:
				$result = null;
				break;
		}

		return $result;
	}

	protected function init_api_adapter( $env, $access_token, $widget_access_token ) {
		if ( empty( $this->api_adapter_service ) ) {
			$this->api_adapter_service = APIAdapterService::get_instance();
		}
		$this->api_adapter_service->initialise( $env, $access_token, $widget_access_token );
	}
}
