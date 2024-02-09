<?php

namespace Paydock\Services\ProcessPayment;

use Paydock\Enums\SaveCardOptions;
use Paydock\Exceptions\LoggedException;
use Paydock\Services\SDKAdapterService;
use WC_Order;
use WC_Order_Refund;

class BankAccountProcessor
{
    protected const OTT_POST_KEY = 'paymentsourcetoken';
    protected bool|WC_Order|WC_Order_Refund $order;
    protected array $args;

    public function __construct(bool|WC_Order|WC_Order_Refund $order)
    {
        $this->order = $order;
        $this->args = $_POST;
    }

    public function chargeWithCustomerId(): array
    {
        $addPaymentSource = $this->getAdditionalFields();

        $amount = $addPaymentSource['amount'];
        unset($addPaymentSource['amount']);

        $request = [
            'payment_source' => array_merge([
                'amount' => $amount,
                'type' => 'bank_account',
                'vault_token' => $this->getVault(false)
            ], $addPaymentSource)
        ];

        if (isset($this->args['gatewayid']) && SaveCardOptions::WITH_GATEWAY()->name === $this->getSaveType()) {
            $request['payment_source']['gateway_id'] = $this->args['gatewayid'];
        }

        $response = SDKAdapterService::getInstance()->createCustomer($request);

        if (!empty($response['error']) || empty($response['resource']['data']['_id'])) {
            LoggedException::throw($response);
        }

        if (!empty($this->args['saveaccount']) && !empty($this->args['savevault'])) {
            $this->save($request['payment_source']['vault_token'], $response['resource']['data']['_id']);
        }

        return $this->directCharge($response['resource']['data']['_id'], $request['payment_source']['vault_token']);
    }

    /**
     * @throws LoggedException
     */
    protected function directCharge(string $customer = '', string $vaultToken = ''): array
    {
        $addPaymentSource = $this->getAdditionalFields();

        $amount = $addPaymentSource['amount'];
        unset($addPaymentSource['amount']);

        $paymentSource = [
            'vault_token' => !empty($vaultToken) ? $vaultToken : $this->getVault(),
            'type' => 'bank_account'
        ];

        if (isset($this->args['gatewayid'])) {
            $paymentSource['gateway_id'] = $this->args['gatewayid'];
        }
        $request = [
            'amount' => $amount,
            'currency' => strtoupper(get_woocommerce_currency()),
            'customer' => [
                'payment_source' => array_merge($addPaymentSource, $paymentSource)
            ]
        ];

        if (!empty($customer)) {
            $request['customer_id'] = $customer;
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
    protected function getVault(bool $saveVaultIfSave = true): string
    {
        $request = [
            'token' => $this->args[self::OTT_POST_KEY] ?? null,
        ];

        $save = !empty($this->args['saveaccount']);

        if (!$save) {
            $request['vault_type'] = 'session';
        }

        $request = array_merge($request, $this->getAdditionalFields());

        $response = SDKAdapterService::getInstance()->createVaultToken($request);

        $token = $response['resource']['data']['vault_token'];

        if (!empty($response['error']) || empty($token)) {
            LoggedException::throw($response);
        }

        if ($save) {
            $this->save($token);
        }

        return $token;
    }

    public function run(): array
    {
        if (
            !empty($this->args['saveaccount'])
            && (
                SaveCardOptions::WITH_GATEWAY()->name === $this->getSaveType()
                || SaveCardOptions::WITHOUT_GATEWAY()->name === $this->getSaveType()
            )
        ) {
            return $this->chargeWithCustomerId();
        }

        return $this->directCharge();
    }

    protected function save(string $vault, ?string $customer = null): void
    {
        $user_id = $this->order->get_customer_id();
        if ($user_id > 0) {
            $meta_value = get_user_meta($user_id, 'paydock_customers', true);

            $meta_value = empty($meta_value) ? [] : json_decode($meta_value, true);

            $meta_value[$vault] = $customer;

            update_user_meta($user_id, 'paydock_customers', json_encode($meta_value));
        }
    }

    protected function getSaveType(): ?string
    {
        return match ($this->args['saveaccounttype']) {
            SaveCardOptions::VAULT()->name => SaveCardOptions::VAULT()->name,
            SaveCardOptions::WITH_GATEWAY()->name => SaveCardOptions::WITH_GATEWAY()->name,
            SaveCardOptions::WITHOUT_GATEWAY()->name => SaveCardOptions::WITHOUT_GATEWAY()->name,
            default => null
        };
    }

    protected function getAdditionalFields(): array
    {
        $address1 = $this->order->get_billing_address_1();
        $address2 = $this->order->get_billing_address_2();

        return [
            'amount' => (float) $this->order->get_total(),
            'address_country' => $this->order->get_billing_country(),
            'address_postcode' => $this->order->get_billing_postcode(),
            'address_city' => $this->order->get_billing_city(),
            'address_state' => $this->order->get_billing_state(),
            'address_line1' => $address1,
            'address_line2' => empty($address2) ? $address1 : $address2,
        ];
    }
}
