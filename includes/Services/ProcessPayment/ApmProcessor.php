<?php

namespace PowerBoard\Services\ProcessPayment;

use Exception;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Helpers\ArgsForProcessPayment;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Repositories\UserCustomerRepository;
use PowerBoard\Services\SDKAdapterService;
use WC_Order;
use WC_Order_Refund;

class ApmProcessor
{
    const CHARGE_METHOD = 'charge';
    const CUSTOMER_CHARGE_METHOD = 'customerCharge';
    const FRAUD_CHARGE_METHOD = 'fraudCharge';
    const ALLOWED_METHODS = [
        self::CHARGE_METHOD,
        self::CUSTOMER_CHARGE_METHOD,
        self::FRAUD_CHARGE_METHOD
    ];

    protected array $args = [];
    protected array $tokenData = [];
    private ?string $runMethod;
    private ?LogRepository $logger;
    protected bool|WC_Order|WC_Order_Refund $order = false;

    public function __construct(array $args = [])
    {
        $this->logger = new LogRepository();
        $this->args = ArgsForProcessPayment::prepare($args);
    }

    public function run(bool|WC_Order|WC_Order_Refund $order): array
    {
        $this->order = $order;
        $this->setRunMethod();

        if (!in_array($this->runMethod, self::ALLOWED_METHODS)) {
            throw new Exception(__('Undefined run method', POWER_BOARD_TEXT_DOMAIN));
        }

        return call_user_func([$this, $this->runMethod]);
    }

    private function setRunMethod()
    {
        switch (true) {
            case $this->args['fraud']:
                $this->runMethod = self::FRAUD_CHARGE_METHOD;
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
            'token' => $this->args['paymentsourcetoken'],
            'capture' => $this->args['gatewaytype'] === strtolower(OtherPaymentMethods::AFTERPAY()->name) ? : $this->args['directcharge'],
            'reference' => (string) $this->order->get_id(),
        ];

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function customerCharge(): array
    {
        $customerArgs = array_merge([
            'first_name' => $this->order->get_billing_last_name(),
            'last_name' => $this->order->get_billing_first_name(),
            'email' => $this->order->get_billing_email(),
            'phone' => $this->order->get_billing_phone(),
            'token' => $this->args['paymentsourcetoken'],
        ], $this->getAdditionalFields('amount'));

        $customer = SDKAdapterService::getInstance()->createCustomer($customerArgs);

        if (!empty($customer['error']) || empty($customer['resource']['data']['_id'])) {
            $message = !empty($customer['error']['message']) ? ' ' . $customer['error']['message'] : '';

            $this->logger->createLogRecord(
                '',
                'Create customer',
                'error',
                $message,
                LogRepository::ERROR
            );
            throw new Exception(__('Can\'t create PowerBoard customer.' . $message, POWER_BOARD_TEXT_DOMAIN));
        }

        $this->logger->createLogRecord(
            $customer['resource']['data']['_id'],
            'Create customer',
            'Success',
            '',
            LogRepository::SUCCESS
        );

        if ($this->args['apmsavecardchecked']) {
            (new UserCustomerRepository)->saveUserCustomer($customer['resource']['data']);
        }

        $customer_id = $customer['resource']['data']['_id'];

        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer_id' => $customer_id,
            'reference' => (string) $this->order->get_id(),
            'capture' => $this->args['gatewaytype'] === strtolower(OtherPaymentMethods::AFTERPAY()->name) ? : $this->args['directcharge'],
        ]);
    }

    private function fraudCharge(): array
    {
        return SDKAdapterService::getInstance()->createCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'capture' => $this->args['gatewaytype'] === strtolower(OtherPaymentMethods::AFTERPAY()->name) ? : $this->args['directcharge'],
            'token' => $this->args['paymentsourcetoken'],
            'reference' => (string) $this->order->get_id(),
            'fraud' => [
                'service_id' => $this->args['fraudserviceid'] ?? '',
                'data' => $this->getAdditionalFields(),
                'mode' => 'passive'
            ]
        ]);
    }

    protected function getAdditionalFields(array|string $exclude = []): array
    {
        if (!$this->order) {
            return [];
        }

        $address1 = $this->order->get_billing_address_1();
        $address2 = $this->order->get_billing_address_2();

        $result = [
            'amount' => (float) $this->order->get_total(),
            'address_country' => $this->order->get_billing_country(),
            'address_postcode' => $this->order->get_billing_postcode(),
            'address_city' => $this->order->get_billing_city(),
            'address_state' => $this->order->get_billing_state(),
            'address_line1' => $address1,
            'address_line2' => empty($address2) ? $address1 : $address2,
        ];

        if (!empty($exclude)) {
            if (!is_array($exclude)) {
                $exclude = [$exclude];
            }

            $result = array_diff_key($result, array_flip($exclude));
        }

        return $result;
    }
}
