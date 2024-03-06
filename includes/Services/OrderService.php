<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Enums\SaveCardOptions;
use Paydock\Enums\SettingsTabs;
use Paydock\Exceptions\LoggedException;
use Paydock\Helpers\ArgsForProcessPayment;
use Paydock\Helpers\VaultTokenHelper;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\Assets\AdminAssetsService;
use WC_Order;
use WC_Order_Refund;
use Paydock\Services\TemplateService;

class OrderService
{

    protected TemplateService $templateService;

    public function __construct()
    {
        if (is_admin()) {
            $this->templateService = new TemplateService($this);
        }
    }

    public function iniPaydockOrderButtons($order)
    {
        if (in_array($order->get_status(), [
            'paydock-pending',
            'paydock-failed',
            'paydock-refunded',
            'paydock-authorize',
            'paydock-cancelled'])) {
            echo $this->templateService->getAdminHtml('hide-refund-button');
        }
        if ($order->get_status() == 'paydock-authorize') {
            echo $this->templateService->getAdminHtml('paydock-capture-block', compact('order', 'order'));
        }
    }

    public function statusChangeVerification($orderId, $oldStatusKey, $newStatusKey, $order)
    {
        if (($oldStatusKey == $newStatusKey) || !empty($GLOBALS['is_updating_paydock_order_status'])) {
            return;
        }
        $rulesForStatuses = [
            'paydock-paid' => ['paydock-refunded', 'paydock-p-refund', 'cancelled', 'refunded', 'paydock-failed', 'paydock-pending'],
            'paydock-refunded' => ['paydock-paid', 'cancelled', 'paydock-failed', 'refunded'],
            'paydock-p-refund' => ['paydock-paid', 'paydock-refunded', 'refunded', 'cancelled', 'paydock-failed'],
            'paydock-authorize' => ['paydock-paid', 'paydock-cancelled', 'paydock-failed', 'cancelled', 'paydock-pending'],
            'paydock-cancelled' => ['paydock-failed', 'cancelled'],
            'paydock-requested' => ['paydock-paid', 'paydock-failed', 'cancelled', 'paydock-pending', 'paydock-authorize']
        ];
        if (!empty($rulesForStatuses[$oldStatusKey])) {
            if (!in_array($newStatusKey, $rulesForStatuses[$oldStatusKey])) {
                $newStatusName = wc_get_order_status_name($newStatusKey);
                $oldStatusName = wc_get_order_status_name($oldStatusKey);
                $error = __('You can not change status from "' . $oldStatusName . '"  to "' . $newStatusName . '"', 'woocommerce');
                $GLOBALS['is_updating_paydock_order_status'] = true;
                $order->update_status($oldStatusKey, $error);
                update_option('paydock_status_change_error', $error);
                unset($GLOBALS['is_updating_paydock_order_status']);
                throw new \Exception($error);
            }
        }
    }

    public function displayStatusChangeError()
    {
        $screen = get_current_screen();
        if ($screen->id == 'woocommerce_page_wc-orders') {
            $message = get_option('paydock_status_change_error', '');
            if (!empty($message)) {
                echo "<div class='notice notice-error is-dismissible'><p>{$message}</p></div>";
                delete_option('paydock_status_change_error');
            }
        }
    }
}
