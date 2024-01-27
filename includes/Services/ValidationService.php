<?php

namespace Paydock\Services;

use Exception;
use Paydock\Abstract\AbstractSettingService;
use Paydock\API\ConfigService;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\CredentialsTypes;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\SettingsTabs;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;

class ValidationService
{
    private const ENABLED_CONDITION = 'yes';
    private array $errors = [];

    private array $result = [];

    private array $data = [];

    private array $getawayIds = [];

    private ?SDKAdapterService $adapterService = null;

    public function __construct(private readonly AbstractSettingService $service)
    {

        $this->adapterService = SDKAdapterService::getInstance();
        $this->adapterService->initialise(SettingsTabs::LiveConnection->value === $this->service->id);

        $this->prepareFormData();
        $this->validate();

        $option_key = $service->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);

        return update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings),
            'yes'
        );
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function prepareFormData(): void
    {
        $post_data = $this->service->get_post_data();
        foreach ($this->service->get_form_fields() as $key => $field) {
            try {
                $this->data[$key] = $this->service->get_field_value($key, $field, $post_data);
                $this->result[$key] = $this->data[$key];

                if ('select' === $field['type'] || 'checkbox' === $field['type']) {
                    do_action('woocommerce_update_non_option_setting', [
                        'id'    => $key,
                        'type'  => $field['type'],
                        'value' => $this->data[$key],
                    ]);
                }
            } catch (Exception $e) {
                $this->service->add_error($e->getMessage());
            }
        }
    }

    private function validate(): void
    {
        if ($this->validateCredential()) {
            $this->validateCard();
            $this->validateBankAccount();
            $this->validateWallets();
            $this->validateAPMs();
        }
    }

    private function validateWallets(): void
    {
        foreach (WalletPaymentMethods::cases() as $method) {
            $result = true;
            $enabledKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::Wallets->name,
                    $method->name,
                    WalletSettings::Enable->name,
                ]);
            $gatewayKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::Wallets->name,
                    $method->name,
                    WalletSettings::GatewayId->name,
                ]);
            $isEnabled = self::ENABLED_CONDITION === $this->data[$enabledKey];
            if ($isEnabled) {
                print_r($this->getawayIds['resource']['data']);
                exit;
                $result = match ($method) {
                    WalletPaymentMethods::PayPalSmartButton => $this->validateId(
                        $this->data[$gatewayKey],
                        'Paypal',
                        true
                    ),
                    WalletPaymentMethods::Afterpay => $this->validateId(
                        $this->data[$gatewayKey],
                        'Afterpay v2',
                        true
                    ),
                    default => $this->validateId($this->data[$gatewayKey], 'MPGS'),
                };
            }

            if (!$result) {
                $this->errors[] = 'Wrong gatewayId for ' . $method->getLabel() . ' wallet.';
            }
        }
    }

    private function validateAPMs(): void
    {
        foreach (OtherPaymentMethods::cases() as $method) {
            $result = true;
            $enabledKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::APMs->name,
                    $method->name,
                    WalletSettings::Enable->name,
                ]);
            $gatewayKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::APMs->name,
                    $method->name,
                    WalletSettings::GatewayId->name,
                ]);
            $isEnabled = self::ENABLED_CONDITION === $this->data[$enabledKey];
            if ($isEnabled) {
                $result = match ($method) {
                    OtherPaymentMethods::PayPal => $this->validateId(
                        $this->data[$gatewayKey],
                        'PayPal',
                        true
                    ),
                    OtherPaymentMethods::Afterpay => $this->validateId(
                        $this->data[$gatewayKey],
                        'Afterpay v1',
                        true
                    ),
                    default => $this->validateId($this->data[$gatewayKey], $method->name),
                };
            }

            if (!$result) {
                $this->errors[] = 'Wrong gatewayId for ' . $method->getLabel() . ' APM.';
            }
        }
    }

    private function validateBankAccount(): void
    {
        $enabledKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::BankAccount->name,
                BankAccountSettings::Enable->name,
            ]);
        $gatewayKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::BankAccount->name,
                BankAccountSettings::GatewayId->name,
            ]);

        $result = false;

        if (self::ENABLED_CONDITION !== $this->data[$enabledKey]) {
            $result = true;
        }

        if (!$result && $this->validateId($this->data[$gatewayKey], 'bank account')) {
            $result = true;
        }

        if (!$result) {
            $this->errors[] = 'Wrong bank account gateway ID.';
        }
    }

    private function validateCard(): void
    {
        $enableKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Card->name,
                CardSettings::Enable->name,
            ]);

        $gatewayIdKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Card->name,
                CardSettings::GatewayId->name,
            ]);

        $this->result[$enableKey] = $this->data[$enableKey];

        if ('yes' !== $this->data[$enableKey]) {
            $this->result[$gatewayIdKey] = $this->data[$gatewayIdKey];
        }

        $supportedCardTypesKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Card->name,
            CardSettings::SupportedCardTypes->name,
        ]);


        if ('yes' == $this->data[$enableKey] && !empty($this->data[$gatewayIdKey])) {
            if ($this->validateId($this->data[$gatewayIdKey])) {
                $this->result[$gatewayIdKey] = $this->data[$gatewayIdKey];
                $supportCardTypeByGatewayId = $this->getSupportCardTypeByGatewayId($this->data[$gatewayIdKey]);
                if ($supportCardTypeByGatewayId) {
                    if ($this->data[$supportedCardTypesKey]) {
                        $supportCardType = str_replace(' ', '', $this->data[$supportedCardTypesKey]);
                        $arraySupportedCardTypesKey = explode(',', $supportCardType);
                        if (!in_array($supportCardTypeByGatewayId, $arraySupportedCardTypesKey)) {
                            $this->errors[] = 'Selected types of cards (' . $this->data[$supportedCardTypesKey] . ') are not supported by this Gateway ID';
                        }
                    } else {
                        $this->errors[] = 'You do not select any supported card types';
                    }
                }

            } else {
                $this->errors[] = 'Incorrect Gateway Id for the card :' . $this->data[$gatewayIdKey];
            }
        }

    }

    private function getSupportCardTypeByGatewayId($gatewayIdKey): ?string
    {
        foreach ($this->getawayIds['resource']['data'] as $getawayId) {
            if ($getawayId['_id'] == $gatewayIdKey) {
                return $getawayId['type'];
            }
        }
        return false;
    }

    private function validateId(string $id, string $checkedName = '', bool $checkFull = false): bool
    {
        $needCheck = !empty($checkedName);

        foreach ($this->getawayIds['resource']['data'] as $getawayId) {
            $checked = !$needCheck;

            if ($needCheck && $checkFull) {
                $checked = strtolower($checkedName) == strtolower($getawayId['name']);
            } elseif ($needCheck) {
                $checked = str_contains(strtolower($getawayId['name']), strtolower($checkedName));
            }

            if ($getawayId['_id'] == $id && $checked) {
                return true;
            }
        }

        return false;
    }

    private function validateCredential(): bool
    {
        $accessKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Credentials->name,
                CredentialSettings::AccessKey->name,
            ]);

        $typeKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Credentials->name,
                CredentialSettings::Type->name,
            ]);

        $isAccessKey = CredentialsTypes::AccessKey->name == $this->data[$typeKey];

        $publicKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Credentials->name,
                CredentialSettings::PublicKey->name,
            ]);

        $secretKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::Credentials->name,
                CredentialSettings::SecretKey->name,
            ]);

        if (
            ($isAccessKey && !empty($this->data[$accessKey]) && $this->checkAccessKeyConnection($this->data[$accessKey]))
            || (
                !$isAccessKey
                && !empty($this->data[$publicKey])
                && !empty($this->data[$secretKey])
                && $this->checkCredentialConnection($this->data[$publicKey], $this->data[$secretKey])
            )
        ) {
            return true;
        }

        $this->errors[] = 'Invalid credentials';

        return false;
    }

    private function checkAccessKeyConnection(?string $accessToken): bool
    {
        ConfigService::$accessToken = $accessToken;
        ConfigService::$publicKey = null;
        ConfigService::$secretKey = null;

        $this->getawayIds = $this->adapterService->searchGateway(['sort_direction' => 'DESC']);

        return empty($this->getawayIds['error']);
    }

    private function checkPublicKey(?string $publicKey): bool
    {
        ConfigService::$publicKey = $publicKey;
        ConfigService::$secretKey = null;
        ConfigService::$accessToken = null;

        $publicResult = $this->adapterService->token();

        return empty($publicResult['error']);
    }

    private function checkSecretKey(?string $secretKey): bool
    {
        ConfigService::$publicKey = null;
        ConfigService::$accessToken = null;
        ConfigService::$secretKey = $secretKey;

        $this->getawayIds = $this->adapterService->searchGateway(['sort_direction' => 'DESC']);

        return empty($this->getawayIds['error']);
    }

    private function checkCredentialConnection(?string $public, ?string $secret): bool
    {
        return $this->checkPublicKey($public) && $this->checkSecretKey($secret);
    }
}
