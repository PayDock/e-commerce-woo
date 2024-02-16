<?php

namespace Paydock\Services\ProcessPayment;

use Exception;
use Paydock\Helpers\ArgsForProcessPayment;
use Paydock\Repositories\UserCustomerRepository;
use Paydock\Services\SDKAdapterService;

class ApmProcessor
{
    const CHARGE_METHOD = 'charge';
    const CUSTOMER_CHARGE_METHOD = 'customerCharge';
    const DIRECT_CHARGE_METHOD = 'directCharge';
    const FRAUD_CHARGE_METHOD = 'fraudCharge';
    const DIRECT_FRAUD_CHARGE_METHOD = 'directFraudCharge';
    const DIRECT_IN_REVIEW_FRAUD_CHARGE_METHOD = 'directInReviewFraudCharge';
    const ALLOWED_METHODS = [
        self::CHARGE_METHOD,
        self::CUSTOMER_CHARGE_METHOD,
        self::DIRECT_CHARGE_METHOD,
        self::FRAUD_CHARGE_METHOD,
        self::DIRECT_FRAUD_CHARGE_METHOD,
        self::DIRECT_IN_REVIEW_FRAUD_CHARGE_METHOD
    ];

    protected array $args = [];
    protected array $tokenData = [];
    private ?string $runMethod;

    public function __construct(array $args = [])
    {
        $this->args = ArgsForProcessPayment::prepare($args);
    }

    public function run(): array
    {
        $this->setRunMethod();

        if (!in_array($this->runMethod, self::ALLOWED_METHODS)) {
            throw new Exception(__('Undefined run method', PAY_DOCK_TEXT_DOMAIN));
        }

        return call_user_func([$this, $this->runMethod]);
    }

    private function setRunMethod()
    {
        switch (true) {
            case $this->args['directcharge'] && $this->args['fraud']:
                $this->runMethod = self::DIRECT_FRAUD_CHARGE_METHOD;
                break;
            case $this->args['fraud']:
                $this->runMethod = self::FRAUD_CHARGE_METHOD;
                break;
            case $this->args['directcharge']:
                $this->runMethod = self::DIRECT_CHARGE_METHOD;
                break;
            case $this->args['apmsavecard']:
                $this->runMethod = self::CUSTOMER_CHARGE_METHOD;
                break;
            default:
                $this->runMethod = self::CHARGE_METHOD;
        }
    }

    public function getRunMethod()
    {
        return $this->runMethod;
    }

    private function charge(): array
    {
        $chargeArgs = [
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'token' => $this->args['paymentsourcetoken']
        ];

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function customerCharge(): array
    {
        $customerArgs = [
            'token' => $this->args['paymentsourcetoken']
        ];

        $customer = SDKAdapterService::getInstance()->createCustomer($customerArgs);

        if (!empty($customer['error']) || empty($customer['resource']['data']['_id'])) {
            $message = !empty($customer['error']['message']) ? ' ' . $customer['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        if ($this->args['apmsavecardchecked']) {
            $res = (new UserCustomerRepository)->saveUserCustomer($customer['resource']['data']);
        }

        $customer_id = $customer['resource']['data']['_id'];

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer_id' => $customer_id,
        ]);
    }

    private function directCharge(): array
    {
        $chargeArgs = [
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'capture' => false,
            'token' => $this->args['paymentsourcetoken']
        ];

        $charge = SDKAdapterService::getInstance()->createCharge($chargeArgs);

        if (!empty($charge['error']) || empty($charge['resource']['data']['_id'])) {
            $message = SDKAdapterService::getInstance()->errorMessageToString($charge);
            throw new Exception(__('Can\'t charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return SDKAdapterService::getInstance()->capture([
            'charge_id' => $charge['resource']['data']['_id']
        ]);
    }

    private function fraudCharge(): array
    {
        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'capture' => true,
            'token' => $this->args['paymentsourcetoken'],
            'fraud' => [
                'service_id' => $this->args['fraudserviceid'] ?? '',
                'mode' => 'passive',
                'data' => new \StdClass
            ]
        ]);
    }

    private function directFraudCharge(): array
    {
        $charge = SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'capture' => false,
            'token' => $this->args['paymentsourcetoken'],
            'fraud' => [
                'service_id' => $this->args['fraudserviceid'] ?? '',
                'mode' => 'passive',
                'data' => new \StdClass
            ]
        ]);

        if (!empty($charge['error']) || empty($charge['resource']['data']['_id'])) {
            $message = SDKAdapterService::getInstance()->errorMessageToString($charge);
            throw new Exception(__('Can\'t charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return SDKAdapterService::getInstance()->capture([
            'charge_id' => $charge['resource']['data']['_id']
        ]);
    }

    private function directInReviewFraudCharge(): array
    {
        $chargeArgs = [
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'capture' => false,
            'token' => $this->args['paymentsourcetoken'],
            'fraud' => [
                'service_id' => $this->args['fraudserviceid'] ?? '',
                'mode' => 'passive',
                'data' => new \StdClass
            ]
        ];

        $charge = SDKAdapterService::getInstance()->createCharge($chargeArgs);

        if (!empty($charge['error']) || empty($charge['resource']['data']['_id'])) {
            $message = SDKAdapterService::getInstance()->errorMessageToString($charge);
            throw new Exception(__('Can\'t charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return SDKAdapterService::getInstance()->capture([
            'charge_id' => $charge['resource']['data']['_id']
        ]);
    }
}
