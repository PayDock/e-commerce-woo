<?php

namespace PowerBoard\Services\ProcessPayment;

use PowerBoard\Enums\SaveCardOptions;
use PowerBoard\Exceptions\LoggedException;
use PowerBoard\Helpers\ArgsForProcessPayment;
use PowerBoard\Helpers\VaultTokenHelper;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\SDKAdapterService;
use WC_Order;
use WC_Order_Refund;

class BankAccountProcessor
{
    private VaultTokenHelper $vaultTokenHelper;
    protected bool|WC_Order|WC_Order_Refund $order;
    protected array $args;
    protected array $tokenData = [];
    protected ?string $orderId;
    private ?LogRepository $logger;

    public function __construct(bool|WC_Order|WC_Order_Refund $order, $args)
    {
        $this->order = $order;
        $this->logger = new LogRepository();
        $this->args = ArgsForProcessPayment::prepare($args);
        $this->vaultTokenHelper = new VaultTokenHelper($this->args);
    }

    public function run(string $orderId = null): array
    {
        $this->orderId = $orderId;
        if (
            $this->args['bankaccountsaveaccount']
            && (
                SaveCardOptions::WITH_GATEWAY()->name === $this->getSaveType()
                || SaveCardOptions::WITHOUT_GATEWAY()->name === $this->getSaveType()
            )
        ) {
            return $this->chargeWithCustomerId();
        }

        return $this->directCharge();
    }

    public function chargeWithCustomerId(): array
    {
        $request = [
            'reference' => (string) $this->orderId,
            'first_name' => $this->order->get_billing_first_name(),
            'last_name' => $this->order->get_billing_last_name(),
            'email' => $this->order->get_billing_email(),
            'phone' => $this->order->get_billing_phone(),
            'payment_source' => array_merge([
                'amount' => $this->args['amount'],
                'type' => 'bank_account',
                'vault_token' => $this->getVaultToken()
            ], $this->getAdditionalFields('amount'))
        ];

        if (!empty($this->args['gatewayid']) && SaveCardOptions::WITH_GATEWAY()->name === $this->getSaveType()) {
            $request['payment_source']['gateway_id'] = $this->args['gatewayid'];
        }

        if (!empty($this->tokenData) && !empty($this->tokenData['customer_id'])) {
            $customerId = $this->tokenData['customer_id'];
        } else {
            $response = SDKAdapterService::getInstance()->createCustomer($request);

            if (!empty($response['error']) || empty($response['resource']['data']['_id'])) {
                $this->logger->createLogRecord(
                    '',
                    'Create customer',
                    'Error',
                    $response['error']['message'],
                    LogRepository::ERROR
                );
                LoggedException::throw($response);
            }

            $this->logger->createLogRecord(
                $response['resource']['data']['_id'],
                'Create customer',
                'Success',
                '',
                LogRepository::SUCCESS
            );

            if ($this->vaultTokenHelper->shouldSaveVaultToken()) {
                (new UserTokenRepository())->updateUserToken($this->args['selectedtoken'], [
                    'customer_id' => $response['resource']['data']['_id']
                ]);
            }

            $customerId = $response['resource']['data']['_id'];
        }

        return $this->directCharge($customerId);
    }

    /**
     * @throws LoggedException
     */
    protected function directCharge(?string $customerId = null): array
    {
        $addPaymentSource = $this->getAdditionalFields('amount');

        $paymentSource = [
            'vault_token' => !empty($this->args['selectedtoken']) ? $this->args['selectedtoken'] : $this->getVaultToken(),
            'type' => 'bank_account'
        ];

        if (!empty($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }

        $request = [
            'reference' => (string) $this->orderId,
            'amount' => $this->args['amount'],
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($addPaymentSource, $paymentSource)
            ]
        ];

        if (!empty($customerId)) {
            $request['customer_id'] = $customerId;
        }

        $response = SDKAdapterService::getInstance()->createCharge($request);

        if (!empty($response['error'])) {
            LoggedException::throw($response);
        }

        return $response;
    }

    /**
     * @throws LoggedException
     */
    protected function getVaultToken(): string
    {
        if (!empty($this->args['selectedtoken'])) {
            $this->tokenData = (new UserTokenRepository())->getUserToken($this->args['selectedtoken']);

            return $this->args['selectedtoken'];
        }

        $token = $this->vaultTokenHelper->get($this->getAdditionalFields());

        if (!empty($token)) {
            $this->args['selectedtoken'] = $token;
        }

        return $token;
    }

    protected function getSaveType(): ?string
    {
        return match ($this->args['bankaccountsaveaccountoption']) {
            SaveCardOptions::VAULT()->name => SaveCardOptions::VAULT()->name,
            SaveCardOptions::WITH_GATEWAY()->name => SaveCardOptions::WITH_GATEWAY()->name,
            SaveCardOptions::WITHOUT_GATEWAY()->name => SaveCardOptions::WITHOUT_GATEWAY()->name,
            default => null
        };
    }

    protected function getAdditionalFields(array|string $exclude = []): array
    {
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
