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
        $tabs = $this->getTabs();
        $records = (new LogRepository())->getLogs($_GET['page_number'] ?? 1, $_GET['per_page'] ?? 50);

        $html = $this->templateService->getAdminHtml('admin', compact('tabs', 'records'));

        if ($echo) {
            echo $html;
        } else {
            return $html;
        }

        return null;
    }
}
