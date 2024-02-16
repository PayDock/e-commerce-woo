<?php

namespace Paydock\Services\ProcessPayment;

use Exception;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\SaveCardOptions;
use Paydock\Helpers\ArgsForProcessPayment;
use Paydock\Helpers\VaultTokenHelper;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\SDKAdapterService;

class CardProcessor
{
    const FRAUD_3DS_CHARGE_METHOD = 'fraud3DsCharge';
    const THREE_DS_CHARGE_METHOD = 'threeDsCharge';
    const FRAUD_CHARGE_METHOD = 'fraudCharge';
    const DIRECT_CHARGE_METHOD = 'directCharge';
    const CUSTOMER_CHARGE_METHOD = 'customerCharge';
    const ALLOWED_METHODS = [
        self::FRAUD_3DS_CHARGE_METHOD,
        self::THREE_DS_CHARGE_METHOD,
        self::FRAUD_CHARGE_METHOD,
        self::DIRECT_CHARGE_METHOD,
        self::CUSTOMER_CHARGE_METHOD
    ];

    protected VaultTokenHelper $vaultTokenHelper;

    protected array $args = [];
    protected array $tokenData = [];
    private ?string $runMethod;

    public function __construct(array $args = [])
    {
        $this->args = ArgsForProcessPayment::prepare($args);
        $this->vaultTokenHelper = new VaultTokenHelper($this->args);
    }

    public function run(): array
    {
        $this->setRunMethod();

        if (!in_array($this->runMethod, self::ALLOWED_METHODS)) {
            throw new Exception(__('Undefined run method', PAY_DOCK_TEXT_DOMAIN));
        }

        return call_user_func([$this, $this->runMethod]);
    }

    public function getRunMethod()
    {
        return $this->runMethod;
    }

    private function setRunMethod()
    {
        switch (true) {
            case !empty($this->args['card3ds']) && !empty($this->args['cardfraud']):
                $this->runMethod = self::FRAUD_3DS_CHARGE_METHOD;
                break;
            case !empty($this->args['card3ds']):
                $this->runMethod = self::THREE_DS_CHARGE_METHOD;
                break;
            case !empty($this->args['cardfraud']):
                $this->runMethod = self::FRAUD_CHARGE_METHOD;
                break;
            case $this->args['cardsavecard']:
                $this->runMethod = self::CUSTOMER_CHARGE_METHOD;
                break;
            default:
                $this->runMethod = self::DIRECT_CHARGE_METHOD;
        }
    }

    private function fraud3DsCharge(): array
    {
        switch (true) {
            case($this->args['card3ds'] === DSTypes::IN_BUILD()->name && $this->args['cardfraud'] === FraudTypes::IN_BUILD()->name):
                return $this->fraud3DsInBuildCharge();
            case($this->args['card3ds'] === DSTypes::STANDALONE()->name && $this->args['cardfraud'] === FraudTypes::STANDALONE()->name):
                return $this->fraud3DsStandaloneCharge();
        }

        return ['error' => ['message' => 'In-built fraud & 3ds error']];
    }

    private function fraud3DsInBuildCharge(): array
    {
        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            '_3ds' => [
                'id' => $this->args['charge3dsid'] ?? '',
                'service_id' => $this->args['card3dsserviceid'] ?? ''
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'mode' => 'active',
                'data' => []
            ]
        ]);
    }

    private function fraud3DsStandaloneCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $faudCharge = SDKAdapterService::getInstance()->standaloneFraudCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'data' => []
            ]
        ]);

        if (!empty($faudCharge['error']) || empty($faudCharge['resource']['data']['_id'])) {
            $message = !empty($faudCharge['error']['message']) ? ' ' . $faudCharge['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock fraud charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            '_3ds_charge_id' => $this->args['charge3dsid'],
            'fraud_charge_id' => $faudCharge['resource']['data']['_id']
        ]);
    }

    private function threeDsCharge(): array
    {
        if ($this->args['card3ds'] === DSTypes::IN_BUILD()->name) {
            return $this->threeDsInBuildCharge();
        }

        return $this->threeDsStandaloneCharge();
    }

    private function threeDsInBuildCharge(): array
    {
        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            '_3ds' => [
                'id' => $this->args['charge3dsid'] ?? '',
                'service_id' => $this->args['card3dsserviceid'] ?? ''
            ]
        ]);
    }

    private function threeDsStandaloneCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            '_3ds_charge_id' => $this->args['charge3dsid']
        ]);
    }

    private function fraudCharge(): array
    {
        if ($this->args['cardfraud'] === FraudTypes::IN_BUILD()->name) {
            return $this->fraudInBuildCharge();
        }

        return $this->fraudStandaloneCharge();
    }

    private function fraudInBuildCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'mode' => 'active',
                'data' => new \StdClass
            ]
        ]);
    }

    private function fraudStandaloneCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $faudCharge = SDKAdapterService::getInstance()->standaloneFraudCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'],
                'data' => []
            ]
        ]);

        if (!empty($faudCharge['error']) || empty($faudCharge['resource']['data']['_id'])) {
            $message = !empty($faudCharge['error']['message']) ? ' ' . $faudCharge['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock fraud charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            'fraud_charge_id' => $faudCharge['resource']['data']['_id']
        ]);
    }

    private function customerCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $customerArgs = [
            'payment_source' => [
                'amount' => $this->args['amount'],
                'vault_token' => $vaultToken
            ]
        ];

        $customer = SDKAdapterService::getInstance()->createCustomer($customerArgs);

        if (!empty($customer['error']) || empty($customer['resource']['data']['_id'])) {
            $message = !empty($customer['error']['message']) ? ' ' . $customer['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        if ($this->vaultTokenHelper->shouldSaveVaultToken()) {
            (new UserTokenRepository())->updateUserToken($this->args['selectedtoken'], [
                'customer_id' => $customer['resource']['data']['_id']
            ]);
        }

        $params = [
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer_id' => $customer['resource']['data']['_id']
        ];

        if (isset($this->args['gatewayid']) && in_array($this->args['cardsavecardoption'], [
            SaveCardOptions::WITH_GATEWAY()->name,
            SaveCardOptions::VAULT()->name
        ])) {
            $params['customer'] = [
                'payment_source' => [
                    'gateway_id' => $this->args['gatewayid']
                ]
            ];
        }

        $responce = SDKAdapterService::getInstance()->createCharge($params);

        if (!empty($responce['error'])) {
            $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return $responce;
    }

    private function directCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ]
        ]);
    }

    public function createCustomer($force = false): void
    {
        if ($this->shouldNotCreateCustomer() || $force) {
            return;
        }

        $customerArgs = [
            'payment_source' => [
                'amount' => $this->args['amount'],
                'vault_token' => $this->args['selectedtoken']
            ]
        ];

        if ($this->args['cardsavecardoption'] === SaveCardOptions::WITH_GATEWAY()->value && isset($this->args['gatewayid'])) {
            $customerArgs['payment_source']['gateway_id'] = $this->args['gatewayid'];
        }

        $customer = SDKAdapterService::getInstance()->createCustomer($customerArgs);
        if (!empty($customer['error']) || empty($customer['resource']['data']['_id'])) {
            $message = !empty($customer['error']['message']) ? ' ' . $customer['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        (new UserTokenRepository())->updateUserToken($this->args['selectedtoken'], [
            'customer_id' => $customer['resource']['data']['_id']
        ]);
    }

    public function getStandalone3dsToken(): string
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = [
            'vault_token' => $vaultToken,
        ];

        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $threeDsCharge = SDKAdapterService::getInstance()->standalone3DsCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            '_3ds' => [
                'service_id' => $this->args['card3dsserviceid'] ?? '',
                'authentication' => [
                    'type' => '01',
                    "date" => date('Y-m-d\TH:i:s.000\Z')
                ]
            ]
        ]);

        if (!empty($threeDsCharge['error']) || empty($threeDsCharge['resource']['data']['_3ds']['token'])) {
            $message = !empty($threeDsCharge['error']['message']) ? ' ' . $threeDsCharge['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock 3ds charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return $threeDsCharge['resource']['data']['_3ds']['token'];
    }

    public function getVaultToken(): string
    {
        $token = $this->vaultTokenHelper->get();

        if (!empty($token)) {
            $this->args['selectedtoken'] = $token;
        }

        return $token;
    }

    private function shouldCreateCustomer(): bool
    {
        return $this->vaultTokenHelper->shouldSaveVaultToken() &&
            in_array($this->args['cardsavecardoption'], [
                SaveCardOptions::WITH_GATEWAY()->name,
                SaveCardOptions::WITHOUT_GATEWAY()->name
            ]) &&
            $this->runMethod !== self::CUSTOMER_CHARGE_METHOD;
    }

    private function shouldNotCreateCustomer(): bool
    {
        return !$this->shouldCreateCustomer();
    }
}
