<?php

namespace Paydock\Services\ProcessPayment;

use Exception;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\SaveCardOptions;
use Paydock\Helpers\ArgsForProcessPayment;
use Paydock\Helpers\VaultTokenHelper;
use Paydock\Repositories\LogRepository;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\SDKAdapterService;
use WC_Order;
use WC_Order_Refund;

class CardProcessor
{
    const FRAUD_3DS_CHARGE_METHOD = 'fraud3DsCharge';
    const THREE_DS_CHARGE_METHOD = 'threeDsCharge';
    const FRAUD_CHARGE_METHOD = 'fraudCharge';
    const CUSTOMER_CHARGE_METHOD = 'customerCharge';
    const CHARGE_METHOD = 'charge';
    const ALLOWED_METHODS = [
        self::FRAUD_3DS_CHARGE_METHOD,
        self::THREE_DS_CHARGE_METHOD,
        self::FRAUD_CHARGE_METHOD,
        self::CUSTOMER_CHARGE_METHOD,
        self::CHARGE_METHOD
    ];

    protected VaultTokenHelper $vaultTokenHelper;
    protected UserTokenRepository $userTokenRepository;
    private ?LogRepository $logger;

    protected array $args = [];
    protected array $tokenData = [];
    private ?string $runMethod;
    protected bool|WC_Order|WC_Order_Refund $order = false;
    private ?string $customerId = null;

    public function __construct(array $args = [])
    {
        $this->logger = new LogRepository();
        $this->args = ArgsForProcessPayment::prepare($args);
        $this->vaultTokenHelper = new VaultTokenHelper($this->args);

        if ($this->args['isuserloggedin']) {
            $this->userTokenRepository = new UserTokenRepository();
        }
    }

    public function run(bool|WC_Order|WC_Order_Refund $order): array
    {
        $this->order = $order;

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
            case($this->isSavedVaultTokenWithCustomer()):
                $this->runMethod = self::CUSTOMER_CHARGE_METHOD;
                break;
            case !empty($this->args['card3ds']) && !empty($this->args['cardfraud']):
                $this->runMethod = self::FRAUD_3DS_CHARGE_METHOD;
                break;
            case !empty($this->args['card3ds']):
                $this->runMethod = self::THREE_DS_CHARGE_METHOD;
                break;
            case !empty($this->args['cardfraud']):
                $this->runMethod = self::FRAUD_CHARGE_METHOD;
                break;
            case $this->args['cardsavecardchecked'] && $this->args['cardsavecardoption'] !== SaveCardOptions::VAULT()->name:
                $this->runMethod = self::CUSTOMER_CHARGE_METHOD;
                break;
            default:
                $this->runMethod = self::CHARGE_METHOD;
        }
    }

    private function fraud3DsCharge(): array
    {
        switch (true) {
            case($this->args['card3ds'] === DSTypes::IN_BUILD()->name && $this->args['cardfraud'] === FraudTypes::IN_BUILD()->name):
                $result = $this->fraud3DsInBuildCharge();
                break;
            case($this->args['card3ds'] === DSTypes::STANDALONE()->name && $this->args['cardfraud'] === FraudTypes::STANDALONE()->name):
                $result = $this->fraud3DsStandaloneCharge();
                break;
            case($this->args['card3ds'] === DSTypes::IN_BUILD()->name && $this->args['cardfraud'] === FraudTypes::STANDALONE()->name):
                $result = $this->fraudStandalone3DsInBuildCharge();
                break;
            case($this->args['card3ds'] === DSTypes::STANDALONE()->name && $this->args['cardfraud'] === FraudTypes::IN_BUILD()->name):
                $result = $this->fraudInBuild3DsStandaloneCharge();
                break;
            default:
                $result = ['error' => ['message' => 'In-built fraud & 3ds error']];
        }

        return $result;
    }

    private function fraudInBuild3DsStandaloneCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            '_3ds_charge_id' => $this->args['charge3dsid'],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'mode' => 'active',
                'data' => $this->getAdditionalFields()
            ],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function fraudStandalone3DsInBuildCharge(): array
    {
        $options = [
            'method' => __FUNCTION__,
            'capture' => $this->args['carddirectcharge'],
            '_3ds' => [
                'id' => $this->args['charge3dsid'] ?? '',
                'service_id' => $this->args['card3dsserviceid'] ?? ''
            ]
        ];
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
            $options['gateway_id'] = $this->args['gatewayid'];
        }

        if (!empty($this->args['cvv'])) {
            $paymentSource['card_ccv'] = $this->args['cvv'];
            $options['ccv'] = $this->args['cvv'];
        }

        $response = SDKAdapterService::getInstance()->standaloneFraudCharge([
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'],
                'data' => $this->getAdditionalFields()
            ],
        ]);

        if (empty($response['error']) && !empty($response['resource']['data']['_id'])) {
            update_option('paydock_fraud_' . (string) $this->order->get_id(), $options);
        }

        return $response;
    }

    private function fraud3DsInBuildCharge(): array
    {
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            '_3ds' => [
                'id' => $this->args['charge3dsid'] ?? '',
                'service_id' => $this->args['card3dsserviceid'] ?? ''
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'mode' => 'active',
                'data' => $this->getAdditionalFields()
            ],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function fraud3DsStandaloneCharge(): array
    {
        $options = [
            'method' => __FUNCTION__,
            'capture' => $this->args['carddirectcharge'],
            'charge3dsid' => $this->args['charge3dsid']
        ];
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
            $options['gateway_id'] = $this->args['gatewayid'];
        }

        if (!empty($this->args['cvv'])) {
            $paymentSource['card_ccv'] = $this->args['cvv'];
            $options['ccv'] = $this->args['cvv'];
        }

        $response = SDKAdapterService::getInstance()->standaloneFraudCharge([
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'reference' => (string) $this->order->get_id(),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'data' => $this->getAdditionalFields()
            ]
        ]);

        if (empty($response['error']) && !empty($response['resource']['data']['_id'])) {
            update_option('paydock_fraud_' . (string) $this->order->get_id(), $options);
        }

        return $response;
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
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            '_3ds' => [
                'id' => $this->args['charge3dsid'] ?? '',
                'service_id' => $this->args['card3dsserviceid'] ?? ''
            ],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function threeDsStandaloneCharge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            '_3ds_charge_id' => $this->args['charge3dsid'],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
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

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'] ?? '',
                'mode' => 'active',
                'data' => $this->getAdditionalFields()
            ],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    private function fraudStandaloneCharge(): array
    {
        $options = [
            'method' => __FUNCTION__,
            'capture' => $this->args['carddirectcharge']
        ];
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
            $options['gateway_id'] = $this->args['gatewayid'];
        }

        if (!empty($this->args['cvv'])) {
            $paymentSource['card_ccv'] = $this->args['cvv'];
            $options['ccv'] = $this->args['cvv'];
        }

        $response = SDKAdapterService::getInstance()->standaloneFraudCharge([
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            'fraud' => [
                'service_id' => $this->args['cardfraudserviceid'],
                'data' => $this->getAdditionalFields()
            ],
        ]);

        if (empty($response['error']) && !empty($response['resource']['data']['_id'])) {
            update_option('paydock_fraud_' . (string) $this->order->get_id(), $options);
        }

        return $response;
    }

    private function customerCharge(): array
    {
        if ($this->customerId === null) {
            $vaultToken = $this->getVaultToken();

            $customerArgs = array_merge([
                'first_name' => $this->order->get_billing_first_name(),
                'last_name' => $this->order->get_billing_last_name(),
                'email' => $this->order->get_billing_email(),
                'phone' => $this->order->get_billing_phone(),
                'payment_source' => [
                    'amount' => $this->args['amount'],
                    'vault_token' => $vaultToken
                ],
            ], $this->getAdditionalFields('amount'));

            if ($this->args['cardsavecardoption'] === SaveCardOptions::WITH_GATEWAY()->name && !empty($this->args['gatewayid'])) {
                $customerArgs['payment_source']['gateway_id'] = $this->args['gatewayid'];
            }

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
                throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
            }

            $this->logger->createLogRecord(
                $customer['resource']['data']['_id'],
                'Create customer',
                'Success',
                '',
                LogRepository::SUCCESS
            );

            if ($this->vaultTokenHelper->shouldSaveVaultToken() && in_array($this->args['cardsavecardoption'], [
                SaveCardOptions::WITH_GATEWAY()->name,
                SaveCardOptions::WITHOUT_GATEWAY()->name
            ])) {
                $this->userTokenRepository->updateUserToken($this->args['selectedtoken'], [
                    'customer_id' => $customer['resource']['data']['_id']
                ]);
            }

            $customerId = $customer['resource']['data']['_id'];
        } else {
            $customerId = $this->customerId;
        }

        $params = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer_id' => $customerId,
            'capture' => $this->args['carddirectcharge'],
            'customer' => ['payment_source' => $this->getAdditionalFields('amount')]
        ];

        if (!empty($this->args['gatewayid'])) {
            $params['customer'] = [
                'payment_source' => [
                    'gateway_id' => $this->args['gatewayid']
                ]
            ];
        }

        if (!empty($this->args['cvv'])) {
            $params['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        $responce = SDKAdapterService::getInstance()->createCharge($params);

        if (!empty($responce['error'])) {
            $message = !empty($responce['error']['message']) ? ' ' . $responce['error']['message'] : '';
            throw new Exception(__('Can\'t create Paydock charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        return $responce;
    }

    private function charge(): array
    {
        $vaultToken = $this->getVaultToken();

        $paymentSource = ['vault_token' => $vaultToken];
        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $chargeArgs = [
            'amount' => $this->args['amount'],
            'reference' => (string) $this->order->get_id(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($this->getAdditionalFields('amount'), $paymentSource)
            ],
            'capture' => $this->args['carddirectcharge']
        ];

        if (!empty($this->args['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $this->args['cvv'];
        }

        return SDKAdapterService::getInstance()->createCharge($chargeArgs);
    }

    public function createCustomer($force = false): void
    {
        if ($this->shouldNotCreateCustomer() || $force) {
            return;
        }

        $customerArgs = array_merge([
            'first_name' => $this->order->get_billing_first_name(),
            'last_name' => $this->order->get_billing_last_name(),
            'email' => $this->order->get_billing_email(),
            'phone' => $this->order->get_billing_phone(),
            'payment_source' => [
                'amount' => $this->args['amount'],
                'vault_token' => $this->args['selectedtoken']
            ]
        ], $this->getAdditionalFields('amount'));

        if ($this->args['cardsavecardoption'] === SaveCardOptions::WITH_GATEWAY()->name && !empty($this->args['gatewayid'])) {
            $customerArgs['payment_source']['gateway_id'] = $this->args['gatewayid'];
        }

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
            throw new Exception(__('Can\'t create Paydock customer.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }
        $this->logger->createLogRecord(
            $customer['resource']['data']['_id'],
            'Create customer',
            'Success',
            '',
            LogRepository::SUCCESS
        );

        $this->userTokenRepository->updateUserToken($this->args['selectedtoken'], [
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
            'reference' => '',
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
            $this->logger->createLogRecord(
                '',
                '3DS Charge',
                'error',
                $message,
                LogRepository::ERROR
            );
            throw new Exception(__('Can\'t create Paydock 3ds charge.' . $message, PAY_DOCK_TEXT_DOMAIN));
        }

        $this->logger->createLogRecord(
            '',
            '3DS Charge',
            'Success',
            '',
            LogRepository::SUCCESS
        );

        return $threeDsCharge['resource']['data']['_3ds']['token'];
    }

    public function getVaultToken(): string
    {
        $token = $this->vaultTokenHelper->get($this->getAdditionalFields('amount'));

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

    private function isSavedVaultTokenWithCustomer(): bool
    {
        if (!$this->args['isuserloggedin'] || empty($this->args['selectedtoken'])) {
            return false;
        }

        $vaultToken = $this->userTokenRepository->getUserToken($this->args['selectedtoken']);
        if (empty($vaultToken) || empty($vaultToken['customer_id'])) {
            return false;
        }

        $this->customerId = $vaultToken['customer_id'];

        return true;
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
