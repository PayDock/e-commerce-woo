<?php

namespace Paydock\Services;

use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\SettingGroups;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;

final class SettingsService
{
    private static ?SettingsService $instance = null;

    protected function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setCredentialsSetting(string $id, CredentialSettings $option, mixed $value): array|bool
    {
        return update_option($this->getOptionName($id, [SettingGroups::Credentials->name, $option->name]), $value);
    }

    public function setBankAccountSetting(string $id, BankAccountSettings $option, mixed $value): array|bool
    {
        return update_option($this->getOptionName($id, [SettingGroups::BankAccount->name, $option->name]), $value);
    }

    public function setCardSetting(string $id, CardSettings $option, mixed $value): array|bool
    {
        return update_option($this->getOptionName($id, [SettingGroups::Card->name, $option->name]), $value);
    }

    public function setWalletsSetting(
        string               $id,
        WalletPaymentMethods $method,
        WalletSettings       $option,
        mixed                $value): array|bool
    {
        return update_option(
            $this->getOptionName($id, [SettingGroups::Wallets->name, $method->name, $option->name]),
            $value
        );
    }

    public function setAPMsSetting(string $id, OtherPaymentMethods $method, APMsSettings $option, mixed $value): array|bool
    {
        return update_option(
            $this->getOptionName($id, [SettingGroups::APMs->name, $method->name, $option->name]),
            $value
        );
    }

    public function getCredentialsSetting(string $id, CredentialSettings $option): array|bool
    {
        return get_option($this->getOptionName($id, [SettingGroups::Credentials->name, $option->name]));
    }

    public function getBankAccountSetting(string $id, BankAccountSettings $option, mixed $value): array|bool
    {
        return get_option($this->getOptionName($id, [SettingGroups::BankAccount->name, $option->name]));
    }

    public function getCardSetting(string $id, CardSettings $option): array|bool
    {
        return get_option($this->getOptionName($id, [SettingGroups::Card->name, $option->name]));
    }

    public function getWalletsSetting(string $id, WalletPaymentMethods $method, WalletSettings $option): array|bool
    {
        return get_option($this->getOptionName($id, [SettingGroups::Wallets->name, $method->name, $option->name]));
    }

    public function getAPMsSetting(string $id, OtherPaymentMethods $method, APMsSettings $option): array|bool
    {
        return get_option($this->getOptionName($id, [SettingGroups::APMs->name, $method->name, $option->name]));
    }

    public function getOptionName(string $id, array $fragments): string
    {
        return implode('_', array_merge([$id], $fragments));
    }
}
