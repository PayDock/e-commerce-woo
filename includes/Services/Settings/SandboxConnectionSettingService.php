<?php

namespace WooPlugin\Services\Settings;

use WooPlugin\Enums\CredentialSettings;
use WooPlugin\Enums\SettingGroups;
use WooPlugin\Enums\SettingsTabs;
use WooPlugin\WooPluginPlugin;
use WooPlugin\Services\SettingsService;

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
				'To test your ' . PLUGIN_TEXT . ' for WooCommerce Plugin, you can use the sandbox mode.',
				PLUGIN_TEXT_DOMAIN
			),
			'title' => CredentialSettings::SANDBOX()->getLabel(),
		];
		parent::init_form_fields();
	}

	protected function getId(): string {
		return SettingsTabs::SANDBOX_CONNECTION()->value;
	}
}
