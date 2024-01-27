<?php

namespace Paydock\Services\Settings;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\CredentialsTypes;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\SaveCardOptions;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\SettingsTabs;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\PaydockPlugin;
use Paydock\Services\SettingsService;
use Paydock\Services\ValidationService;

class LiveConnectionSettingService extends AbstractSettingService
{
    protected function getId(): string
    {
        return SettingsTabs::LiveConnection->value;
    }

    public function init_form_fields(): void
    {
        $service = SettingsService::getInstance();

        foreach (SettingGroups::cases() as $settingGroup) {
            $key = PaydockPlugin::PLUGIN_PREFIX . '_' . $service->getOptionName($this->id, [
                    $settingGroup->name,
                    'label',
                ]);

            if (SettingGroups::Card == $settingGroup) {
                $this->form_fields[$key . '_label'] = [
                    'type' => 'big_label',
                    'title' => __('Payment Methods:', PaydockPlugin::PLUGIN_PREFIX),
                ];
            }

            $this->form_fields[$key] = [
                'type' => 'big_label',
                'title' => __($settingGroup->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            $this->form_fields = array_merge($this->form_fields, match ($settingGroup) {
                SettingGroups::Credentials => $this->getCredentialOptions(),
                SettingGroups::Card => $this->getCardOptions(),
                SettingGroups::BankAccount => $this->getBankAccountOptions(),
                SettingGroups::Wallets => $this->getWalletsOptions(),
                SettingGroups::APMs => $this->getAPMsOptions()
            });
        }
    }

    private function getCredentialOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (CredentialSettings::cases() as $credentialSettings) {
            if ($credentialSettings != CredentialSettings::Sandbox) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Credentials->name,
                    $credentialSettings->name,
                ]);
                $fields[$key] = [
                    'type' => $credentialSettings->getInputType(),
                    'title' => __($credentialSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];
                if ($description = $credentialSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }

                if (CredentialSettings::Type == $credentialSettings) {
                    $fields[$key]['options'] = CredentialsTypes::toArray();
                }
            }
        }

        return $fields;
    }

    private function getCardOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (CardSettings::cases() as $cardSettings) {
            $key = $service->getOptionName($this->id, [SettingGroups::Card->name, $cardSettings->name]);
            $fields[$key] = [
                'type' => $cardSettings->getInputType(),
                'title' => __($cardSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            if ($description = $cardSettings->getDescription()) {
                $fields[$key]['description'] = $description;
                $fields[$key]['desc_tip'] = true;
            }

            $fields[$key]['options'] = match ($cardSettings) {
                CardSettings::DS => DSTypes::toArray(),
                CardSettings::Fraud => FraudTypes::toArray(),
                CardSettings::SaveCardOption => SaveCardOptions::toArray(),
                default => []
            };
        }

        return $fields;
    }

    private function getBankAccountOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (BankAccountSettings::cases() as $bankAccountSettings) {
            $key = $service->getOptionName($this->id, [
                SettingGroups::BankAccount->name,
                $bankAccountSettings->name,
            ]);

            $fields[$key] = [
                'type' => $bankAccountSettings->getInputType(),
                'title' => __($bankAccountSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            if ($description = $bankAccountSettings->getDescription()) {
                $fields[$key]['description'] = $description;
                $fields[$key]['desc_tip'] = true;
            }

            if (BankAccountSettings::SaveCardOption == $bankAccountSettings) {
                $fields[$key]['options'] = SaveCardOptions::toArray();
            }
        }

        return $fields;
    }

    private function getWalletsOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (WalletPaymentMethods::cases() as $walletPaymentMethods) {
            $fields[$service->getOptionName($this->id, [
                SettingGroups::Wallets->name,
                $walletPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($walletPaymentMethods->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (WalletSettings::cases() as $walletSettings) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Wallets->name,
                    $walletPaymentMethods->name,
                    $walletSettings->name,
                ]);

                $fields[$key] = [
                    'type' => $walletSettings->getInputType(),
                    'title' => __($walletSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];

                if ($description = $walletSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }
            }

            if (WalletPaymentMethods::PayPalSmartButton === $walletPaymentMethods) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::Wallets->name,
                    $walletPaymentMethods->name,
                    'pay_later',
                ]);
                $fields[$key] = [
                    'type' => 'checkbox',
                    'title' => __('Pay Later', PaydockPlugin::PLUGIN_PREFIX),
                ];
            }
        }

        return $fields;
    }

    public function getAPMsOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (OtherPaymentMethods::cases() as $otherPaymentMethods) {
            $fields[$service->getOptionName($this->id, [
                SettingGroups::APMs->name,
                $otherPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($otherPaymentMethods->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (APMsSettings::cases() as $APMsSettings) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::APMs->name,
                    $otherPaymentMethods->name,
                    $APMsSettings->name,
                ]);

                $fields[$key] = [
                    'type' => $APMsSettings->getInputType(),
                    'title' => __($APMsSettings->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
                ];

                if ($description = $APMsSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }

                if (APMsSettings::SaveCardOption == $APMsSettings) {
                    $fields[$key]['options'] = SaveCardOptions::toArray();
                }
            }
        }

        return $fields;
    }

    public function process_admin_options()
    {
        $this->init_settings();
        $validationService = new ValidationService($this);
        $this->settings = array_merge($this->settings, $validationService->getResult());

        foreach ($validationService->getErrors() as $error) {
            $this->add_error($error);
            \WC_Admin_Settings::add_error($error);
        }

        $option_key = $this->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);
        return update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings),
            'yes'
        );
    }
}
