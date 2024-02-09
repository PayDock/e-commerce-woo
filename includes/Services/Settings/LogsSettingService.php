<?php

namespace Paydock\Services\Settings;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Enums\SettingsTabs;
use Paydock\Repositories\LogRepository;

class LogsSettingService extends AbstractSettingService
{
    protected function getId(): string
    {
        return SettingsTabs::LOG()->value;
    }

    public function generate_settings_html($form_fields = [], $echo = true): ?string
    {
        $page = $_GET['page_number'] ?? 1;
        $perPage = $_GET['per_page'] ?? 50;
        $order = $_GET['order'] ?? 'desc';
        $orderBy = $_GET['orderBy'] ?? 'created_at';

        $tabs = $this->getTabs();
        $records = (new LogRepository())->getLogs($page, $perPage, $orderBy, $order);

        $html = $this->templateService->getAdminHtml('admin', compact('tabs', 'records'));

        if ($echo) {
            echo $html;
        } else {
            return $html;
        }

        return null;
    }
}
