<?php

namespace PowerBoard\Services;

class TemplateService
{
    private const TEMPLATE_DIR = 'templates';
    private const ADMIN_TEMPLATE_DIR = 'admin';
    private const TEMPLATE_END = '.php';
    protected $currentSection = '';
    private $settingService = null;

    private string $templateAdminDir = '';

    public function __construct($service = null)
    {
        $this->settingService = $service;
        if (isset($this->settingService->currentSection)) {
            $this->currentSection = $this->settingService->currentSection ?? $_GET['section'];
        }
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

    private function getAdminPath(string $template): string
    {

        return $this->getTemplatePath($this->templateAdminDir.DIRECTORY_SEPARATOR.$template);
    }

    private function getTemplatePath(string $template): string
    {
        return plugin_dir_path(POWER_BOARD_PLUGIN_FILE).$template.self::TEMPLATE_END;
    }
}
