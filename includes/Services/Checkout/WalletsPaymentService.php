<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstract\AbstractPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SettingsService;
use PowerBoard\Util\WalletsBlock;

class WalletsPaymentService extends AbstractPaymentService
{
    public function __construct()
    {
        $settings = SettingsService::getInstance();

        $this->id = 'power_board_wallets_gateway';
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
                __('Error:', POWER_BOARD_TEXT_DOMAIN).' Afterpay returned a failure status.',
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

        update_option('paydock_fraud_' . (string) $order->get_id(), []);

        $loggerRepository = new LogRepository();
        if ('inreview' === $data['data']['status']) {
            $status = 'wc-power_board-requested';
        } elseif (
            ($data['data']['status'] === 'pending')
            || (!empty($_GET['direct_charge']) && ($_GET['direct_charge'] == 'true'))
        ) {
            $status = 'wc-power_board-authorize';
        } else {
            $status = 'wc-power_board-paid';
        }

        $order->set_status($status);
        $order->payment_complete();
        $order->save();

        unset(
            $_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY],
        );
        update_post_meta($order_id, 'power_board_charge_id', $chargeId);

        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $data['data']['id'] ?? '',
            'Charge',
            $status,
            'Successful',
                $status === 'wc-power_board-authorize' ? LogRepository::DEFAULT : LogRepository::SUCCESS);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
