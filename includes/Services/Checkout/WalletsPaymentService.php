<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstract\AbstractPaymentService;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\SettingsService;
use Paydock\Util\WalletsBlock;

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
        return
            SettingsService::getInstance()->isEnabledPayment()
            && (
                SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::PAY_PAL_SMART_BUTTON())
                || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::AFTERPAY())
                || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::APPLE_PAY())
                || SettingsService::getInstance()->isWalletEnabled(WalletPaymentMethods::GOOGLE_PAY())
            );
    }

    public function payment_scripts()
    {
        return SettingsService::getInstance()->getWidgetScriptUrl();
    }

    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        if (!empty($_GET['afterpay_success']) && ($_GET['afterpay_success'] == 'false')) {
            wc_add_notice(
                __('Error:', PAY_DOCK_TEXT_DOMAIN).' Afterpay returned a failure status.',
                'error'
            );

            return [
                'result' => 'fail',
                'error' => 'Afterpay returned a failure status.'
            ];
        }

        $order = wc_get_order($order_id);
        $data = [];
        $chargeId = null;
        if (!empty($_POST['payment_response'])) {
            $data = json_decode($_POST['payment_response'], true);
        }

        if ((json_last_error() !== JSON_ERROR_NONE) && !empty($_POST['payment_response'])) {
            $chargeId = $data['data']['id'];
        }

        $loggerRepository = new LogRepository();
        $status = ((
                ($data['data']['status'] === 'pending')
                || (!empty($_GET['direct_charge']) && ($_GET['direct_charge'] == 'true'))
            ) ? 'wc-paydock-authorize' : 'wc-paydock-paid');

        $order->set_status($status);
        $order->payment_complete();
        $order->save();

        unset(
            $_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY],
            $_SESSION[WalletsBlock::WALLETS_SESSION_KEY.$order_id]
        );
        update_post_meta($order_id, 'paydock_charge_id', $chargeId);

        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $data['data']['id'] ?? '',
                'Charge',
                $status,
            'Successful',
                $status === 'wc-paydock-authorize' ? LogRepository::DEFAULT : LogRepository::SUCCESS);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
