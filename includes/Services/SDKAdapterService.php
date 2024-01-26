<?php

namespace Paydock\Services;

use Paydock\API\ConfigService;
use Paydock\API\GatewayService;
use Paydock\API\TokenService;

class SDKAdapterService
{
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

    public function token(): array
    {
        $tokenService = new TokenService;

        return $tokenService->create()->call();
    }

    public function getGatewayById(string $id): array
    {
        $gatewayService = new GatewayService;

        return $gatewayService->get()->setId($id)->call();
    }

    public function initialise(): void
    {
        $section = $_GET['section'] ?? 'production';
        $environment = $section == 'pay_dock_sandbox' ? 'sandbox' : 'production';
        if ($environment == 'sandbox') {
            $options = get_option('woocommerce_pay_dock_sandbox_settings');
            $publicKey = $_POST['woocommerce_pay_dock_sandbox_pay_dock_sandbox_Credentials_PublicKey'] ?? "";
            $secretKey = $_POST['woocommerce_pay_dock_sandbox_pay_dock_sandbox_Credentials_SecretKey'] ?? "";
            if(!empty($options) && !$publicKey && !$secretKey){
                $publicKey = $options['pay_dock_sandbox_Credentials_PublicKey'];
                $secretKey = $options['pay_dock_sandbox_Credentials_SecretKey'];
            }
        } else {
            $options = get_option('woocommerce_pay_dock_settings');
            $publicKey = $_POST['woocommerce_pay_dock_pay_dock_Credentials_PublicKey'] ?? "";
            $secretKey = $_POST['woocommerce_pay_dock_pay_dock_Credentials_SecretKey'] ?? "";
            if(!empty($options) && !$publicKey && !$secretKey){
                $publicKey = $options['pay_dock_Credentials_PublicKey'];
                $secretKey = $options['pay_dock_Credentials_SecretKey'];
            }
        }

        ConfigService::init($environment, $secretKey, $publicKey);
    }
}
