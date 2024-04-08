<?php

namespace Paydock\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\SettingsService;

abstract class AbstractWalletPaymentService extends AbstractPaymentService
{
    public function __construct()
    {
        $settings = SettingsService::getInstance();
        $paymentMethod = $this->getWalletType();

        $this->id = 'paydock_'.$paymentMethod->getId().'_wallets_gateway';
        $this->title = $settings->getWidgetPaymentWalletTitle($paymentMethod);
        $this->description = $settings->getWidgetPaymentWalletDescription($paymentMethod);

        parent::__construct();
    }

    abstract protected function getWalletType(): WalletPaymentMethods;

    public function is_available()
    {
        return SettingsService::getInstance()->isEnabledPayment()
            && SettingsService::getInstance()->isWalletEnabled($this->getWalletType());
    }

    public function payment_scripts()
    {
        return SettingsService::getInstance()->getWidgetScriptUrl();
    }

    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        if (!empty($_GET['afterpay_success']) && ($_GET['afterpay_success'] == 'false')) {
            throw new RouteException(
                'woocommerce_rest_checkout_process_payment_error',
                __('Error:', PAY_DOCK_TEXT_DOMAIN).' '.__('Afterpay returned a failure status.'),
                PAY_DOCK_TEXT_DOMAIN
            );
        }

        $order = wc_get_order($order_id);
        $data = [];
        $chargeId = null;
        if (!empty($_POST['payment_response'])) {
            $data = json_decode($_POST['payment_response'], true);
        }

        if ((json_last_error() === JSON_ERROR_NONE) && !empty($_POST['payment_response'])) {
            $chargeId = $data['data']['id'];
        }

        $wallets = [];
        if (!empty($_POST['wallets'])) {
            $wallets = json_decode($_POST['wallets'], true);
            if ($wallets === null) {
                $wallets = [];
            }
        }

        $wallet = reset($wallets);
        $isFraud = !empty($wallet['fraud']) && $wallet['fraud'];
        if ($isFraud) {
            update_option('paydock_fraud_'.(string) $order->get_id(), []);
        }

        $loggerRepository = new LogRepository();
        if ('inreview' === $data['data']['status']) {
            $status = 'wc-paydock-requested';
        } elseif (
            ($data['data']['status'] === 'pending')
            || (!empty($_GET['direct_charge']) && ($_GET['direct_charge'] == 'true'))
        ) {
            $status = 'wc-paydock-authorize';
        } else {
            $status = 'wc-paydock-paid';
        }

        $order->set_status($status);
        $order->payment_complete();
        $order->save();

        unset($_SESSION[AbstractWalletBlock::AFTERPAY_SESSION_KEY]);

        update_post_meta($order_id, 'paydock_charge_id', $chargeId);

        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $data['data']['id'] ?? '',
            'Charge',
            $status,
            'Successful',
            $status === 'wc-paydock-authorize' ? LogRepository::DEFAULT : LogRepository::SUCCESS
        );

        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        ];
    }

    public function webhook()
    {
    }
}
