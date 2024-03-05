<?php

namespace PowerBoard\Services;

use PowerBoard\Abstract\AbstractSettingService;
use PowerBoard\Enums\SaveCardOptions;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Exceptions\LoggedException;
use PowerBoard\Helpers\ArgsForProcessPayment;
use PowerBoard\Helpers\VaultTokenHelper;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\Assets\AdminAssetsService;
use WC_Order;
use WC_Order_Refund;
use PowerBoard\Services\TemplateService;

class OrderService
{

    protected TemplateService $templateService;

    public function __construct()
    {
        if (is_admin()) {
            $this->templateService = new TemplateService($this);
        }
    }

    public function iniPowerBoardOrderButtons($order)
    {
        if (in_array($order->get_status(), [
            'power_board-pending',
            'power_board-failed',
            'power_board-refunded',
            'power_board-authorize',
            'power_board-cancelled'])) {
            echo $this->templateService->getAdminHtml('hide-refund-button');
        }
        if ($order->get_status() == 'power_board-authorize') {
            echo $this->templateService->getAdminHtml('power_board-capture-block', compact('order', 'order'));
        }
    }

    public function statusChangeVerification($orderId, $oldStatusKey, $newStatusKey, $order)
    {
        if (($oldStatusKey == $newStatusKey) || !empty($GLOBALS['is_updating_power_board_order_status'])) {
            return;
        }
        $rulesForStatuses = [
            'power_board-paid' => ['power_board-refunded', 'power_board-p-refund', 'cancelled', 'refunded', 'power_board-failed', 'power_board-pending'],
            'power_board-refunded' => ['power_board-paid', 'cancelled', 'power_board-failed', 'refunded'],
            'power_board-p-refund' => ['power_board-paid', 'power_board-refunded', 'refunded', 'cancelled', 'power_board-failed'],
            'power_board-authorize' => ['power_board-paid', 'power_board-cancelled', 'power_board-failed', 'cancelled', 'power_board-pending'],
            'power_board-cancelled' => ['power_board-failed', 'cancelled'],
            'power_board-requested' => ['power_board-paid', 'power_board-failed', 'cancelled', 'power_board-pending', 'power_board-authorize']
        ];
        if (!empty($rulesForStatuses[$oldStatusKey])) {
            if (!in_array($newStatusKey, $rulesForStatuses[$oldStatusKey])) {
                $newStatusName = wc_get_order_status_name($newStatusKey);
                $oldStatusName = wc_get_order_status_name($oldStatusKey);
                $error = __('You can not change status from "' . $oldStatusName . '"  to "' . $newStatusName . '"', 'woocommerce');
                $GLOBALS['is_updating_power_board_order_status'] = true;
                $order->update_status($oldStatusKey, $error);
                update_option('power_board_status_change_error', $error);
                unset($GLOBALS['is_updating_power_board_order_status']);
                throw new \Exception($error);
            }
        }
    }

    public function displayStatusChangeError()
    {
        $screen = get_current_screen();
        if ($screen->id == 'woocommerce_page_wc-orders') {
            $message = get_option('power_board_status_change_error', '');
            if (!empty($message)) {
                echo "<div class='notice notice-error is-dismissible'><p>{$message}</p></div>";
                delete_option('power_board_status_change_error');
            }
        }
    }
}
