<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstract\AbstractPaymentService;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\ProcessPayment\WalletsProcessor;
use Paydock\Services\SettingsService;

class WalletsPaymentService extends AbstractPaymentService
{
    public function __construct()
    {
        $settings = SettingsService::getInstance();

        $this->id = 'paydock_wallets_gateway';
        $this->title = $settings->getWidgetPaymentWalletTitle();
        $this->description = $settings->getWidgetPaymentWalletDescription();

        parent::__construct();
    }

    public function is_available()
    {
        return SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::PAY_PAL_SMART_BUTTON())
            || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::AFTERPAY())
            || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::APPLE_PAY())
            || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::GOOGLE_PAY());
    }

    public function payment_scripts()
    {
        return SettingsService::getInstance()->getWidgetScriptUrl();
    }

    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        $order = wc_get_order($order_id);
        $data = [];

        if (!empty($_POST['payment_response'])) {
            $data = json_decode($_POST['payment_response'], true);
        }

        $loggerRepository = new LogRepository();

        $order->set_status('wc-paydock-paid');
        $order->payment_complete();
        $order->save();

        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $data['data']['id'] ?? '',
            'Wallet Payment',
            'Payment Successful',
            'Successful',
            LogRepository::SUCCESS);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
