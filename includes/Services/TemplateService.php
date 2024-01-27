<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;

class TemplateService
{
    private const TEMPLATE_DIR = 'templates';
    private const ADMIN_TEMPLATE_DIR = 'admin';

    private const TEMPLATE_END = '.php';

    private string $templateAdminDir = '';

    public function __construct(protected readonly AbstractSettingService $settingService)
    {
        $this->templateAdminDir = implode(DIRECTORY_SEPARATOR, [self::TEMPLATE_DIR, self::ADMIN_TEMPLATE_DIR]);
    }

    public function getAdminHtml(string $template, array $data = []): string
    {
        ob_start();

        if (!empty($data)) {
            extract($data);
        }

        include $this->getAdminPath($template);

        return ob_get_clean();
    }

    private function getTemplatePath(string $template): string
    {
        return plugin_dir_path(PAY_DOCK_PLUGIN_FILE) . $template . self::TEMPLATE_END;
    }

    private function getAdminPath(string $template): string
    {
        return $this->getTemplatePath($this->templateAdminDir . DIRECTORY_SEPARATOR . $template);
    }
}
