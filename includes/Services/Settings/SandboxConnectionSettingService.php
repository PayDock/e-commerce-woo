<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\SettingsService;

class SandboxConnectionSettingService extends LiveConnectionSettingService {
	public function init_form_fields(): void {
		$sandBoxOptionKey = SettingsService::getInstance()
		                                   ->getOptionName( $this->id, [
			                                   SettingGroups::CREDENTIALS()->name,
			                                   CredentialSettings::SANDBOX()->name
		                                   ] );

		$this->form_fields[ $sandBoxOptionKey ] = [
			'type' => CredentialSettings::SANDBOX()->getInputType(),
			'label' => __(
				'To test your PowerBoard for WooCommerce Plugin, you can use the sandbox mode.',
				'power-board'
			),
			'title' => CredentialSettings::SANDBOX()->getLabel(),
		];
		parent::init_form_fields();
	}

	protected function getId(): string {
		return SettingsTabs::SANDBOX_CONNECTION()->value;
	}
}
