<?php

namespace PowerBoard\Services;

class TemplateService {
	private const TEMPLATE_DIR = 'templates';
	private const ADMIN_TEMPLATE_DIR = 'admin';
	private const TEMPLATE_END = '.php';
	protected $currentSection = '';
	private $settingService = null;

	private $templateAdminDir = '';

	public function __construct( $service = null ) {
		$this->settingService = $service;
		if ( isset( $this->settingService->currentSection ) ) {
			$section = !empty($_GET['section']) ? sanitize_text_field($_GET['section']) : null;
			$this->currentSection = $this->settingService->currentSection ?? $section;
		}
		$this->templateAdminDir = implode( DIRECTORY_SEPARATOR, [ self::TEMPLATE_DIR, self::ADMIN_TEMPLATE_DIR ] );
	}

	public function includeAdminHtml( string $template, array $data = [] ): void {
		$data['templateService'] = $this;

		if ( ! empty( $data ) ) {
			extract( $data );
		}

		$path = $this->getAdminPath( $template );

		if ( file_exists( $path ) ) {
			include $path; // nosemgrep: audit.php.lang.security.file.inclusion-arg  --  the following require is safe because we are checking if the file exists and it is not a user input.
		}
	}

	public function getAdminHtml( string $template, array $data = [] ): string {
		ob_start();

		$this->includeAdminHtml( $template, $data );

		return ob_get_clean();
	}

	private function getAdminPath( string $template ): string {

		return $this->getTemplatePath( $this->templateAdminDir . DIRECTORY_SEPARATOR . $template );
	}

	private function getTemplatePath( string $template ): string {
		return plugin_dir_path( POWER_BOARD_PLUGIN_FILE ) . $template . self::TEMPLATE_END;
	}
}
