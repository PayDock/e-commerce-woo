<?php

namespace Paydock\Services;

use Paydock\API\ChargeService;
use Paydock\API\ConfigService;
use Paydock\API\GatewayService;
use Paydock\API\CustomerService;
use Paydock\API\TokenService;
use Paydock\API\VaultService;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\CredentialsTypes;
use Paydock\Enums\SettingGroups;
use Paydock\Services\Settings\LiveConnectionSettingService;
use Paydock\Services\Settings\SandboxConnectionSettingService;

class SDKAdapterService
{
    private const ENABLED_CONDITION = 'yes';
    private const PROD_ENV = 'production';
    private const SANDBOX_ENV = 'sandbox';
    private static ?SDKAdapterService $instance = null;

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->initialise();
    }

    /**
     * https://documenter.getpostman.com/view/6912944/TzJpifCf#325c4dce-d7be-4ad9-a78c-0dc989aa94d4
     */
    public function searchGateway(array $parameters = []): array
    {
        $gatewayService = new GatewayService;

        return $gatewayService->search($parameters)->call();
    }

    public function token(array $params = ['gateway_id' => '', 'type' => '']): array
    {
        $tokenService = new TokenService;

        return $tokenService->create($params)->call();
    }

    public function getGatewayById(string $id): array
    {
        $gatewayService = new GatewayService;

        return $gatewayService->get()->setId($id)->call();
    }

    public function createVaultToken(array $params): array
    {
        $vaultService = new VaultService;

        return $vaultService->create($params)->call();
    }

    public function createCustomer(array $params): array
    {
        $customerService = new CustomerService;

        return $customerService->create($params)->call();
    }

    public function createCharge(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->create($params)->call();
    }

    public function initialise(?bool $forcedEnv = null): void
    {
        $isProd = $this->isProd($forcedEnv);

        $settingsService = SettingsService::getInstance();

        if ($isProd) {
            $settings = new LiveConnectionSettingService();

        } else {
            $settings = new SandboxConnectionSettingService();
        }

        $isAccessToken = CredentialsTypes::ACCESS_KEY()->name == $settings->get_option(
            $settingsService->getOptionName($settings->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::TYPE()->name,
            ])
        );

        if ($isAccessToken) {
            $secretKey = $settings->get_option($settingsService->getOptionName($settings->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::ACCESS_KEY()->name,
            ]));
        } else {
            $publicKey = $settings->get_option($settingsService->getOptionName($settings->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::PUBLIC_KEY()->name,
            ]));
            $secretKey = $settings->get_option($settingsService->getOptionName($settings->id, [
                SettingGroups::CREDENTIALS()->name,
                CredentialSettings::SECRET_KEY()->name,
            ]));
        }

        $env = $isProd ? self::PROD_ENV : self::SANDBOX_ENV;

        ConfigService::init($env, $secretKey, $publicKey ?? null);
    }

    private function isProd(?bool $forcedProdEnv = null): bool
    {
        if (is_null($forcedProdEnv)) {
            $settings = new SandboxConnectionSettingService();

            return self::ENABLED_CONDITION !== $settings->get_option(
                SettingsService::getInstance()->getOptionName($settings->id, [
                    SettingGroups::CREDENTIALS()->name,
                    CredentialSettings::SANDBOX()->name,
                ])
            );
        }

        return $forcedProdEnv;
    }
}
