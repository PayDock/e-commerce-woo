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
use Paydock\Enums\TypeExchangeOTT;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\PaydockPlugin;
use Paydock\Services\HashService;
use Paydock\Services\SettingsService;
use Paydock\Services\Validation\ConnectionValidationService;

class LiveConnectionSettingService extends AbstractSettingService
{
    public function __construct()
    {
        parent::__construct();

        $service = SettingsService::getInstance();
        foreach (CredentialSettings::cases() as $credentialSettings) {
            if (in_array($credentialSettings->name, CredentialSettings::getHashed())) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::CREDENTIALS()->name,
                    $credentialSettings->name,
                ]);

                if (!empty($this->settings[$key])) {
                    $this->settings[$key] = HashService::decrypt($this->settings[$key]);
                }
            }
        }
    }

    protected function getId(): string
    {
        return SettingsTabs::LIVE_CONNECTION()->value;
    }

    public function init_form_fields(): void
    {
        $service = SettingsService::getInstance();

        foreach (SettingGroups::cases() as $settingGroup) {
            $key = PaydockPlugin::PLUGIN_PREFIX.'_'.$service->getOptionName($this->id, [
                    $settingGroup->name,
                    'label',
                ]);

            if (SettingGroups::CARD() == $settingGroup) {
                $this->form_fields[$key.'_label'] = [
                    'type' => 'big_label',
                    'title' => __('Payment Methods:', PaydockPlugin::PLUGIN_PREFIX),
                ];
            }

            $this->form_fields[$key] = [
                'type' => 'big_label',
                'title' => __($settingGroup->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            $this->form_fields = array_merge($this->form_fields, match ($settingGroup->name) {
                SettingGroups::CREDENTIALS()->name => $this->getCredentialOptions(),
                SettingGroups::CARD()->name => $this->getCardOptions(),
                SettingGroups::BANK_ACCOUNT()->name => $this->getBankAccountOptions(),
                SettingGroups::WALLETS()->name => $this->getWalletsOptions(),
                SettingGroups::A_P_M_S()->name => $this->getAPMsOptions()
            });
        }
    }

    private function getCredentialOptions(): array
    {
        $fields = [];
        $service = SettingsService::getInstance();

        foreach (CredentialSettings::cases() as $credentialSettings) {
            if ($credentialSettings->name != CredentialSettings::SANDBOX()->name) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::CREDENTIALS()->name,
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

                if (CredentialSettings::TYPE() == $credentialSettings) {
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
            $key = $service->getOptionName($this->id, [SettingGroups::CARD()->name, $cardSettings->name]);
            $fields[$key] = [
                'type' => $cardSettings->getInputType(),
                'title' => __(preg_replace(['/ Id/', '/ id/'], ' ID', $cardSettings->getLabel()),
                    PaydockPlugin::PLUGIN_PREFIX),
                'default' => $cardSettings->getDefault(),
            ];

            if ($description = $cardSettings->getDescription()) {
                $fields[$key]['description'] = $description;
                $fields[$key]['desc_tip'] = true;
            }

            $fields[$key]['options'] = match ($cardSettings->name) {
                CardSettings::DS()->name => DSTypes::toArray(),
                CardSettings::FRAUD()->name => FraudTypes::toArray(),
                CardSettings::SAVE_CARD_OPTION()->name => SaveCardOptions::toArray(),
                CardSettings::TYPE_EXCHANGE_OTT()->name => TypeExchangeOTT::toArray(),
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
                SettingGroups::BANK_ACCOUNT()->name,
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

            if (BankAccountSettings::SAVE_CARD_OPTION() == $bankAccountSettings) {
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
                SettingGroups::WALLETS()->name,
                $walletPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($walletPaymentMethods->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (WalletSettings::cases() as $walletSettings) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::WALLETS()->name,
                    $walletPaymentMethods->name,
                    $walletSettings->name,
                ]);

                $fields[$key] = [
                    'type' => $walletSettings->getInputType(),
                    'title' => __(preg_replace(['/ Id/', '/ id/'], ' ID', $walletSettings->getLabel()),
                        PaydockPlugin::PLUGIN_PREFIX),
                ];

                if ($description = $walletSettings->getDescription()) {
                    $fields[$key]['description'] = $description;
                    $fields[$key]['desc_tip'] = true;
                }
            }

            if (WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $walletPaymentMethods->name) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::WALLETS()->name,
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
                SettingGroups::A_P_M_S()->name,
                $otherPaymentMethods->name,
                'label',
            ])] = [
                'type' => 'label',
                'title' => __($otherPaymentMethods->getLabel(), PaydockPlugin::PLUGIN_PREFIX),
            ];

            foreach (APMsSettings::cases() as $APMsSettings) {
                if ($otherPaymentMethods->name === OtherPaymentMethods::AFTERPAY()->name &&
                    $APMsSettings->name === APMsSettings::DIRECT_CHARGE()->name) {
                    continue;
                }

                $key = $service->getOptionName($this->id, [
                    SettingGroups::A_P_M_S()->name,
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

                if (APMsSettings::SAVE_CARD_OPTION() == $APMsSettings) {
                    $fields[$key]['options'] = SaveCardOptions::toArray();
                }
            }
        }

        return $fields;
    }

    public function process_admin_options()
    {
        $this->init_settings();
        $validationService = new ConnectionValidationService($this);
        $this->settings = array_merge($this->settings, $validationService->getResult());

        $service = SettingsService::getInstance();

        foreach (CredentialSettings::cases() as $credentialSettings) {
            if (in_array($credentialSettings->name, CredentialSettings::getHashed())) {
                $key = $service->getOptionName($this->id, [
                    SettingGroups::CREDENTIALS()->name,
                    $credentialSettings->name,
                ]);

                if (!empty($this->settings[$key])) {
                    $this->settings[$key] = HashService::encrypt($this->settings[$key]);
                }
            }
        }
        foreach ($validationService->getErrors() as $error) {
            $this->add_error($error);
            \WC_Admin_Settings::add_error($error);
        }

        $option_key = $this->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);
        return update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_'.$this->id, $this->settings),
            'yes'
        );
    }
}
