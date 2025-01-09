<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class EnvironmentSettings extends AbstractEnum {
	protected const ENVIRONMENT = 'ENVIRONMENT';

	public function get_input_type(): string {
		switch ( $this->name ) {
			case self::ENVIRONMENT:
			default:
				return 'select';
		}
	}

	public function get_label(): string {
		switch ( $this->name ) {
			case self::ENVIRONMENT:
				return 'Environment';
			default:
				return ucfirst( strtolower( str_replace( '_', ' ', $this->name ) ) );
		}
	}

	public function get_options_for_ui(): array {
		switch ( $this->name ) {
			case self::ENVIRONMENT:
				return $this->get_environments_list_for_ui();
			default:
				return [];
		}
	}

	public function get_environments_list_for_ui(): array {
		return [
			''                                         => 'Select an environment',
			ConfigAPI::STAGING_ENVIRONMENT()->value    => ConfigAPI::STAGING_ENVIRONMENT_NAME()->value,
			ConfigAPI::SANDBOX_ENVIRONMENT()->value    => ConfigAPI::SANDBOX_ENVIRONMENT_NAME()->value,
			ConfigAPI::PRODUCTION_ENVIRONMENT()->value => ConfigAPI::PRODUCTION_ENVIRONMENT_NAME()->value,
		];
	}

	public function get_default() {
		switch ( $this->name ) {
			case self::ENVIRONMENT:
			default:
				$result = '';
		}

		return $result;
	}
}
