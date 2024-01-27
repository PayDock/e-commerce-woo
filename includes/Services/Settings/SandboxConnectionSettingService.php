<?php

namespace Paydock\Services\Settings;

use Paydock\Enums\CredentialSettings;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\SettingsTabs;
use Paydock\PaydockPlugin;
use Paydock\Services\SettingsService;

class SandboxConnectionSettingService extends LiveConnectionSettingService
{
    protected function getId(): string
    {
        return SettingsTabs::SandBoxConnection->value;
    }

    public function init_form_fields(): void
    {
        $sandBoxOptionKey = SettingsService::getInstance()
            ->getOptionName($this->id, [SettingGroups::Credentials->name, CredentialSettings::Sandbox->name]);

        $this->form_fields[$sandBoxOptionKey] = [
            'type' => CredentialSettings::Sandbox->getInputType(),
            'label' => __(
                'To test your Paydock for WooCommerce Plugin, you can use the sandbox mode.',
                PaydockPlugin::PLUGIN_PREFIX
            ),
            'title' => __(CredentialSettings::Sandbox->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
        ];
        parent::init_form_fields();
    }
}
