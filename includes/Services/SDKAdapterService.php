<?php

namespace PowerBoard\Services;

use PowerBoard\API\ChargeService;
use PowerBoard\API\ConfigService;
use PowerBoard\API\CustomerService;
use PowerBoard\API\GatewayService;
use PowerBoard\API\NotificationService;
use PowerBoard\API\ServiceService;
use PowerBoard\API\TokenService;
use PowerBoard\API\VaultService;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\CredentialsTypes;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;

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

    public function searchServices(array $parameters = []): array
    {
        $serviceService = new ServiceService();

        return $serviceService->search($parameters)->call();
    }

    public function searchNotifications(array $parameters = []): array
    {
        $notificationService = new NotificationService();

        return $notificationService->search($parameters)->call();
    }

    public function createNotification(array $parameters = []): array
    {
        $notificationService = new NotificationService();

        return $notificationService->create($parameters)->call();
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

    public function createWalletCharge(array $params, ?bool $directCharge): array
    {
        $chargeService = new ChargeService;

        return $chargeService->walletsInitialize($params, $directCharge)->call();
    }

    public function standaloneFraudCharge(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->standaloneFraud($params)->call();
    }

    public function standalone3DsCharge(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->standalone3Ds($params)->call();
    }

    public function capture(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->capture($params)->call();
    }

    public function cancelAuthorised(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->cancelAuthorised($params)->call();
    }

    public function refunds(array $params): array
    {
        $chargeService = new ChargeService;

        return $chargeService->refunds($params)->call();
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

    public function errorMessageToString(array $responce): string
    {
        $result = !empty($responce['error']['message']) ? ' '.$responce['error']['message'] : '';
        if (isset($responce['error']['details'])) {
            $firstDetail = reset($responce['error']['details']);
            if (is_array($firstDetail)) {
                $result .= ' '.implode(',', $firstDetail);
            } else {
                $result .= ' '.implode(',', $responce['error']['details']);
            }
        }

        return $result;
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
