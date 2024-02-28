<?php

namespace PowerBoard\Services\Validation;

use Exception;
use PowerBoard\Abstract\AbstractSettingService;
use PowerBoard\API\ConfigService;
use PowerBoard\Enums\BankAccountSettings;
use PowerBoard\Enums\CardSettings;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\CredentialsTypes;
use PowerBoard\Enums\FraudTypes;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Enums\WalletSettings;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

class ConnectionValidationService
{
    private const ENABLED_CONDITION = 'yes';

    private const UNSELECTED_CRD_VALUE = 'Please select payment methods...';

    private const AVAILABLE_CARD_TYPES = [
        'mastercard' => 'MasterCard',
        'visa' => 'Visa',
        'amex' => 'American Express',
        'diners' => 'Diners Club',
        'japcb' => 'Japanese Credit Bureau',
        'maestro' => 'Maestro',
        'ausbc' => 'Australian Bank Card',
    ];
    private const IS_WEBHOOK_SET_OPTION = 'is_power_board_webhook_set';
    public const WEBHOOK_EVENT_FRAUD_CHECK_SUCCESS_NAME = 'standalone_fraud_check_success';
    public const WEBHOOK_EVENT_TRANSACTION_SUCCESS_NAME = 'transaction_success';
    public const WEBHOOK_EVENT_TRANSACTION_FAILURE_NAME = 'transaction_failure';
    public const WEBHOOK_EVENTS = [
        self::WEBHOOK_EVENT_FRAUD_CHECK_SUCCESS_NAME,
        self::WEBHOOK_EVENT_TRANSACTION_SUCCESS_NAME,
        self::WEBHOOK_EVENT_TRANSACTION_FAILURE_NAME
    ];

    public ?AbstractSettingService $service = null;
    private array $errors = [];
    private array $result = [];
    private array $data = [];
    private array $getawayIds = [];
    private array $servicesIds = [];
    private ?SDKAdapterService $adapterService = null;

    public function __construct(AbstractSettingService $service)
    {
        $this->service = $service;
        $this->adapterService = SDKAdapterService::getInstance();
        $this->adapterService->initialise(SettingsTabs::LIVE_CONNECTION()->value === $this->service->id);

        $this->prepareFormData();
        $this->validate();

        $option_key = $service->get_option_key();
        do_action('woocommerce_update_option', ['id' => $option_key]);

        update_option(
            $option_key,
            apply_filters('woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings),
            'yes'
        );
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
                        'id' => $key,
                        'type' => $field['type'],
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
            $this->setWebhooks();
        }
    }

    private function validateCredential(): bool
    {
        $accessKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::ACCESS_KEY()->name,
            ]);

        $typeKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::TYPE()->name,
            ]);

        $isAccessKey = CredentialsTypes::ACCESS_KEY()->name == $this->data[$typeKey];

        $publicKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::PUBLIC_KEY()->name,
            ]);

        $secretKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::SECRET_KEY()->name,
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

    private function checkCredentialConnection(?string $public, ?string $secret): bool
    {
        return $this->checkPublicKey($public) && $this->checkSecretKey($secret);
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
        $this->servicesIds = $this->adapterService->searchServices(['sort_direction' => 'DESC']);

        return empty($this->getawayIds['error']);
    }

    private function validateCard(): void
    {
        $enableKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::ENABLE()->name,
            ]);

        $gatewayIdKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::GATEWAY_ID()->name,
            ]);

        $fraudEnableServiceKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::FRAUD()->name,
            ]);

        $fraudGatewayIdKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::FRAUD_SERVICE_ID()->name,
            ]);

        $_3DSEnableServiceKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::DS()->name,
            ]);

        $_3DSGatewayIdKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::CARD()->name,
                CardSettings::DS_SERVICE_ID()->name,
            ]);

        $this->result[$enableKey] = $this->data[$enableKey];

        if ('yes' !== $this->data[$enableKey]) {
            $this->result[$gatewayIdKey] = $this->data[$gatewayIdKey];
        }

        $supportedCardTypesKey = SettingsService::getInstance()->getOptionName($this->service->id, [
            SettingGroups::CARD()->name,
            CardSettings::SUPPORTED_CARD_TYPES()->name,
        ]);


        if ('yes' == $this->data[$enableKey] && !empty($this->data[$gatewayIdKey])) {
            if ($isValidGateway = $this->validateId($this->data[$gatewayIdKey])) {
                $this->result[$gatewayIdKey] = $this->data[$gatewayIdKey];
                $supportCardTypeByGatewayId = $this->getSupportCardTypeByGatewayId($this->data[$gatewayIdKey]);
                if ($supportCardTypeByGatewayId) {
                    if ($this->data[$supportedCardTypesKey]) {
                        if (self::UNSELECTED_CRD_VALUE == $this->data[$supportedCardTypesKey]) {
                            $this->errors[] = 'Supported card types cant be empty.';
                        } else {
                            $supportCardType = strtolower(str_replace(' ', '',
                                $this->data[$supportedCardTypesKey]));
                            $arraySupportedCardTypesKeys = explode(',', $supportCardType);
                            if (empty(array_intersect($arraySupportedCardTypesKeys,
                                array_keys(self::AVAILABLE_CARD_TYPES)))) {
                                $this->errors[] = 'Selected types of cards (' . implode(',',
                                    $arraySupportedCardTypesKeys) . ') are not supported by this Gateway ID';
                            }
                        }
                    } else {
                        $this->errors[] = 'You do not select any supported card types';
                    }
                }

            } else {
                $this->errors[] = 'Incorrect Gateway ID for the card: ' . $this->data[$gatewayIdKey];
            }

            if (
                $isValidGateway
                && (FraudTypes::DISABLE()->name !== $this->data[$fraudEnableServiceKey])
                && !$this->validateId($this->data[$fraudGatewayIdKey])
            ) {
                $this->errors[] = 'Incorrect Fraud Service ID: ' . $this->data[$fraudGatewayIdKey];
            }

            if (
                $isValidGateway
                && (FraudTypes::DISABLE()->name !== $this->data[$_3DSEnableServiceKey])
                && !$this->validateId($this->data[$_3DSGatewayIdKey])
            ) {
                $this->errors[] = 'Incorrect 3DS Service ID: ' . $this->data[$_3DSGatewayIdKey];
            }
        }
    }

    private function validateId(string $id, string $checkedName = '', bool $checkFull = false): bool
    {
        foreach ($this->getawayIds['resource']['data'] as $getawayId) {
            if ($getawayId['_id'] == $id) {
                return true;
            }
        }

        foreach ($this->servicesIds['resource']['data'] as $servicesId) {
            if ($id == $servicesId['_id']) {
                return true;
            }
        }

        return false;
    }

    private function getSupportCardTypeByGatewayId($gatewayIdKey): ?string
    {
        foreach ($this->getawayIds['resource']['data'] as $getawayId) {
            if ($getawayId['_id'] == $gatewayIdKey) {
                return strtolower($getawayId['type']);
            }
        }

        return false;
    }

    private function validateBankAccount(): void
    {
        $enabledKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::BANK_ACCOUNT()->name,
                BankAccountSettings::ENABLE()->name,
            ]);
        $gatewayKey = SettingsService::getInstance()
            ->getOptionName($this->service->id, [
                SettingGroups::BANK_ACCOUNT()->name,
                BankAccountSettings::GATEWAY_ID()->name,
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

    private function validateWallets(): void
    {
        foreach (WalletPaymentMethods::cases() as $method) {
            $result = true;
            $enabledKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::WALLETS()->name,
                    $method->name,
                    WalletSettings::ENABLE()->name,
                ]);
            $gatewayKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::WALLETS()->name,
                    $method->name,
                    WalletSettings::GATEWAY_ID()->name,
                ]);
            $fraudEnableKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::WALLETS()->name,
                    $method->name,
                    WalletSettings::FRAUD()->name,
                ]);
            $fraudGatewayIdKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::WALLETS()->name,
                    $method->name,
                    WalletSettings::FRAUD_SERVICE_ID()->name,
                ]);
            $isEnabled = self::ENABLED_CONDITION === $this->data[$enabledKey];
            if ($isEnabled) {
                $result = match ($method->name) {
                    WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name => $this->validateId(
                        $this->data[$gatewayKey],
                        'Paypal'
                    ),
                    WalletPaymentMethods::AFTERPAY()->name => $this->validateId(
                        $this->data[$gatewayKey],
                        'Afterpay v2'
                    ),
                    default => $this->validateId($this->data[$gatewayKey], 'MPGS'),
                };
            }

            if (!$result) {
                $this->errors[] = 'Wrong Gateway ID for ' . $method->getLabel() . ' wallet.';
            }

            if (
                $isEnabled
                && (self::ENABLED_CONDITION == $this->data[$fraudEnableKey])
                && !$this->validateId($this->data[$fraudGatewayIdKey])
            ) {
                $this->errors[] = 'Incorrect '
                    . $method->getLabel()
                    . ' wallet Fraud Service ID: '
                    . $this->data[$fraudGatewayIdKey];
            }
        }
    }

    private function validateAPMs(): void
    {
        foreach (OtherPaymentMethods::cases() as $method) {
            $result = true;
            $enabledKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::A_P_M_S()->name,
                    $method->name,
                    WalletSettings::ENABLE()->name,
                ]);
            $gatewayKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::A_P_M_S()->name,
                    $method->name,
                    WalletSettings::GATEWAY_ID()->name,
                ]);
            $fraudEnableKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::A_P_M_S()->name,
                    $method->name,
                    WalletSettings::FRAUD()->name,
                ]);
            $fraudGatewayIdKey = SettingsService::getInstance()
                ->getOptionName($this->service->id, [
                    SettingGroups::A_P_M_S()->name,
                    $method->name,
                    WalletSettings::FRAUD_SERVICE_ID()->name,
                ]);
            $isEnabled = self::ENABLED_CONDITION === $this->data[$enabledKey];
            if ($isEnabled) {
                $result = match ($method) {
                    OtherPaymentMethods::AFTERPAY() => $this->validateId(
                        $this->data[$gatewayKey],
                        'Afterpay v1',
                        true
                    ),
                    default => $this->validateId($this->data[$gatewayKey], $method->name),
                };
            }

            if (!$result) {
                $this->errors[] = 'Wrong Gateway ID for ' . $method->getLabel() . ' APM.';
            }

            if (
                $isEnabled
                && (self::ENABLED_CONDITION == $this->data[$fraudEnableKey])
                && !$this->validateId($this->data[$fraudGatewayIdKey])
            ) {
                $this->errors[] = 'Incorrect '
                    . $method->getLabel()
                    . ' APM Fraud Service ID: '
                    . $this->data[$fraudGatewayIdKey];
            }
        }
    }

    private function setWebhooks(): void
    {
        $option = get_option(self::IS_WEBHOOK_SET_OPTION, false);
        if ($option !== false && count((array) $option) === count(self::WEBHOOK_EVENTS)) {
            return;
        }

        $notSettedWebhooks = self::WEBHOOK_EVENTS;
        $webhookSiteUrl = get_site_url() . '/wc-api/power_board-webhook/';
        $shouldCreateWebhook = true;
        $webhookRequest = $this->adapterService->searchNotifications(['type' => 'webhook']);
        if (!empty($webhookRequest['resource']['data'])) {
            $events = [];
            foreach ($webhookRequest['resource']['data'] as $webhook) {
                if ($webhook['destination'] === $webhookSiteUrl) {
                    $events[] = $webhook['event'];
                }
            }

            $notSettedWebhooks = array_diff(self::WEBHOOK_EVENTS, $events);
            if (empty($notSettedWebhooks)) {
                $shouldCreateWebhook = false;
            }
        }

        $webhookIds = [];
        if ($shouldCreateWebhook) {
            foreach ($notSettedWebhooks as $event) {
                $result = $this->adapterService->createNotification([
                    'event' => $event,
                    'destination' => $webhookSiteUrl,
                    'type' => 'webhook',
                    'transaction_only' => false
                ]);

                if (!empty($result['resource']['data']['_id'])) {
                    $webhookIds[] = $result['resource']['data']['_id'];
                } else {
                    $this->errors[] = __('Can\'t create webhook', POWER_BOARD_TEXT_DOMAIN) . (!empty($result['error']) ? ' ' . $result['error'] : '');
                    return;
                }
            }

            if (!empty($webhookIds)) {
                update_option(self::IS_WEBHOOK_SET_OPTION, $webhookIds);
            }
        } else {
            return;
        }
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
