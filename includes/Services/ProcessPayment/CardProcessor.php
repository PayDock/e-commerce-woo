<?php

namespace Paydock\Services\ProcessPayment;

use Exception;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\SaveCardOptions;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\SDKAdapterService;

class CardProcessor
{
    private array $args = [];

    public function __construct(array $args = [])
    {
        $this->args = $this->prepareArgs($args);
    }

    public function run(): array
    {
        $responce = [];

        switch (true) {
            case !empty($this->args['card3ds']) && !empty($this->args['cardfraud']):
                $responce = $this->fraud3DsCharge();
                break;
            case !empty($this->args['card3ds']):
                $responce = $this->threeDsCharge();
                break;
            case !empty($this->args['cardfraud']):
                $responce = $this->fraudCharge();
                break;
            case isset($this->args['saveaccount']) && empty($this->args['saveaccount']):
            case isset($this->args['carddirectcharge']):
                $responce = $this->directCharge();
                break;
            default:
                $responce = $this->customerCharge();
        }

        return $responce;
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

        if ($this->args['isuserloggedin'] &&
            !empty($this->args['cardsavecard']) &&
            $this->args['cardsavecardoption'] !== SaveCardOptions::VAULT()->name &&
            $this->args['cardsavecardchecked']) {
            // todo: save customer to database
        }

        $params = [
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer_id' => $customer['resource']['data']['_id']
        ];

        if (isset($this->args['gatewayid'])) {
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

    public function getStandalone3dsToken(): string
    {
        $vaultToken = $this->args['vaulttoken'];

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
        $vaultToken = !empty($this->args['selectedtoken']) ? $this->args['selectedtoken'] : null;
        $OTTtoken = !empty($this->args['paymentsourcetoken']) ? $this->args['paymentsourcetoken'] : null;
        $saveCard = !empty($this->args['cardsavecard']) ? true : false;
        $saveCardOption = !empty($this->args['cardsavecardoption']) ? $this->args['cardsavecardoption'] : null;

        if ($vaultToken !== null) {
            return $vaultToken;
        }

        if (empty($OTTtoken)) {
            throw new Exception(__('The token wasn\'t generated correctly.', PAY_DOCK_TEXT_DOMAIN));
        }

        $vaultTokenData = [
            'token' => $OTTtoken
        ];

        if (!$saveCard) {
            $vaultTokenData['vault_type'] = 'session';
        }

        $responce = SDKAdapterService::getInstance()->createVaultToken($vaultTokenData);

        if (!empty($responce['error']) || empty($responce['resource']['data']['vault_token'])) {
            $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock vault token.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        if ($this->args['isuserloggedin'] &&
            $saveCard &&
            $saveCardOption === SaveCardOptions::VAULT()->name &&
            $this->args['cardsavecardchecked']) {
            $userTokenRepository = new UserTokenRepository();
            $userTokenRepository->saveUserToken($responce['resource']['data']);
        }

        return $responce['resource']['data']['vault_token'];
    }

    private function prepareArgs(array $args = []): array
    {
        $args['isuserloggedin'] = is_user_logged_in();

        if (!empty($args['card3ds']) && $args['card3ds'] === DSTypes::DISABLE()->name) {
            $args['card3ds'] = '';
        }

        if (!empty($args['cardfraud']) && $args['cardfraud'] === FraudTypes::DISABLE()->name) {
            $args['cardfraud'] = '';
        }

        if (isset($args['cardsavecardchecked'])) {
            $args['cardsavecardchecked'] = $args['cardsavecardchecked'] === '1';
        }

        return $args;
    }
}
