<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractSettingService;
use Paydock\API\ConfigService;
use Paydock\Library\Sdk\ResponseException;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;

class ValidationService
{
    private array $errors = [];

    private array $result = [];

    private array $data = [];

    private array $getawayIds = [];

    private ?SDKAdapterService $adapterService = null;

    public function __construct(private readonly AbstractSettingService $service)
    {

        $this->adapterService = SDKAdapterService::getInstance();
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
                if ('select' === $field['type'] || 'checkbox' === $field['type']) {
                    do_action('woocommerce_update_non_option_setting', [
                        'id'    => $key,
                        'type'  => $field['type'],
                        'value' => $this->data[$key],
                    ]);
                }

                $this->result[$key] = $this->data[$key];
            } catch (\Exception $e) {
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
            $enabledKey = SettingsService::getInstance()->getOptionName($this->service->id, [
                SettingGroups::Wallets->name,
                $method->name,
                WalletSettings::Enable->name,
            ]);
            $gatewayKey = SettingsService::getInstance()->getOptionName($this->service->id, [
                SettingGroups::Wallets->name,
                $method->name,
                WalletSettings::GatewayId->name,
            ]);
            $isEnabled = 'yes' === $this->data[$enabledKey];
            if ($isEnabled) {
                $result = match ($method) {
                    WalletPaymentMethods::PayPalSmartButton => $this->validateId(
                        $this->data[$gatewayKey],
                        'PayPal Smart Button',
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
            $enabledKey = SettingsService::getInstance()->getOptionName($this->service->id, [
                SettingGroups::Wallets->name,
                $method->name,
                WalletSettings::Enable->name,
            ]);
            $gatewayKey = SettingsService::getInstance()->getOptionName($this->service->id, [
                SettingGroups::Wallets->name,
                $method->name,
                WalletSettings::GatewayId->name,
            ]);
            $isEnabled = 'yes' === $this->data[$enabledKey];
            if ($isEnabled) {
                $result = match ($method) {
                    WalletPaymentMethods::PayPalSmartButton => $this->validateId(
                        $this->data[$gatewayKey],
                        'PayPal Smart Button',
                        true
                    ),
                    default => $this->validateId($this->data[$gatewayKey], $method->name),
                };
            }

            if (!$result) {
                $this->errors[] = 'Wrong gatewayId for ' . $method->name . ' wallet.';
            }
        }
    }

    private function validateBankAccount(): void
    {
        $enabledKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::BankAccount->name,
            BankAccountSettings::Enable->name,
        ]);
        $gatewayKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::BankAccount->name,
            BankAccountSettings::GatewayId->name,
        ]);

        $result = false;

        if ('yes' !== $this->data[$enabledKey]) {
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
        $enableKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Card->name,
            CardSettings::Enable->name,
        ]);

        $gatewayIdKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Card->name,
            CardSettings::GatewayId->name,
        ]);

        $gatewayIdKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Card->name,
            CardSettings::GatewayId->name,
        ]);

        $supportedCardTypesKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Card->name,
            CardSettings::SupportedCardTypes->name,
        ]);

        $this->result[$enableKey] = $this->data[$enableKey];

        if ('yes' !== $this->data[$enableKey]) {
            $this->result[$gatewayIdKey] = $this->data[$gatewayIdKey];
        }

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
                        $this->errors[] = 'Selected types of cards (' . $this->data[$supportedCardTypesKey] . ') are not supported by this Gateway ID';
                    }
                }

            } else {
                $this->errors[] = 'Incorrect Gateway Id for the card :' . $this->data[$gatewayIdKey];
            }
        }
    }

    private function getSupportCardTypeByGatewayId($gatewayIdKey): string
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
        $accessKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Credentials->name,
            CredentialSettings::AccessKey->name,
        ]);

        $typeKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Credentials->name,
            CredentialSettings::Type->name,
        ]);

        $isAccessKey = CredentialsTypes::AccessKey->name == $this->data[$typeKey];
        if ($isAccessKey
            && !empty($this->data[$accessKey])
            && $this->checkAccessKeyConnection($this->data[$accessKey])
        ) {
            $this->result[$typeKey] = $this->data[$typeKey];
            $this->result[$accessKey] = $this->data[$accessKey];

            return true;
        }

        $publicKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Credentials->name,
            CredentialSettings::PublicKey->name,
        ]);
        $secretKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::Credentials->name,
            CredentialSettings::SecretKey->name,
        ]);
        if (
            !$isAccessKey
            && !empty($this->data[$publicKey])
            && !empty($this->data[$secretKey])
            && $this->checkCredentialConnection($this->data[$publicKey], $this->data[$secretKey])
        ) {
            $this->result[$typeKey] = $this->data[$typeKey];
            $this->result[$publicKey] = $this->data[$publicKey];
            $this->result[$secretKey] = $this->data[$secretKey];

            return true;
        }

        $this->errors[] = 'Invalid credentials';

        return false;
    }

    private function checkAccessKeyConnection(string $token): bool
    {
        ConfigService::$accessToken = $token;
        ConfigService::$publicKey = null;
        ConfigService::$secretKey = null;

        $this->getawayIds = $this->adapterService->searchGateway(['sort_direction' => 'DESC']);

        if (!empty($this->getawayIds['error'])) {
            return false;
        }

        return !empty($this->getawayIds);
    }

    private function checkCredentialConnection(string $publicKey, string $secretKey): bool
    {
        $result = true;

        ConfigService::$accessToken = null;
        ConfigService::$publicKey = $publicKey;
        ConfigService::$secretKey = $secretKey;

        if (AbstractSettingService::SANDBOX_CONNECTION_TAB === $this->service->currentSection) {
            $host = 'https://api-sandbox.paydock.com';
        } else {
            $host = 'https://api.paydock.com';
        }

        $curl = curl_init($host . '/v1/payment_sources/tokens');
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS     => json_encode(['gateway_id' => '', 'type' => '']),
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "x-user-public-key: $publicKey",
            ],
        ]);
        $responce = curl_exec($curl);
        $responce = json_decode($responce, true);

        if (!is_array($responce) || !isset($responce['status']) || (201 != $responce['status'])) {
            $result = false;
        }

        $this->getawayIds = $this->adapterService->searchGateway(['sort_direction' => 'DESC']);
        if (!empty($this->getawayIds['error'])) {
            $result = false;
        }

        return $result;
    }
}
