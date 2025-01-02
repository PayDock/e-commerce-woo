<?php

namespace PowerBoard\Services;

use PowerBoard\Enums\SettingsTabs;

class TemplateService {
	private const TEMPLATE_DIR = 'templates';
	private const ADMIN_TEMPLATE_DIR = 'admin';
	private const CHECKOUT_TEMPLATE_DIR = 'checkout';

	private const TEMPLATE_END = '.php';
	protected $current_section = '';
	private $setting_service;

	private $templateAdminDir;

	public function __construct( $service = null ) {
		$this->setting_service = $service;
		$section              = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
		$available_sections   = array_map( function ( $item ) {
			return strtolower( $item->value );
		}, SettingsTabs::allCases() );
		if ( isset( $this->setting_service->current_section ) || in_array( $section, $available_sections ) ) {
			$this->current_section = $this->setting_service->current_section ?? $section;
		}
		$this->templateAdminDir = implode( DIRECTORY_SEPARATOR, [ self::TEMPLATE_DIR, self::ADMIN_TEMPLATE_DIR ] );
		$this->templateCheckoutDir = implode( DIRECTORY_SEPARATOR, [ self::TEMPLATE_DIR, self::CHECKOUT_TEMPLATE_DIR ] );
	}

	public function include_admin_html( string $template, array $data = [] ): void {

		$settings = SettingsService::get_instance();
		$data['settings'] = $settings;

		$data['template_service'] = $this;

		if ( ! empty( $data ) ) {
			extract( $data );
		}

		$path = $this->get_admin_path( $template );

		if ( file_exists( $path ) ) {
			include $path; // nosemgrep: audit.php.lang.security.file.inclusion-arg  --  the following require is safe because we are checking if the file exists, and it is not a user input.
		}
	}

	public function get_admin_html( string $template, array $data = [] ): string {
		ob_start();

		$this->include_admin_html( $template, $data );

		return ob_get_clean();
	}

	private function get_admin_path( string $template ): string {

		return $this->get_template_path( $this->templateAdminDir . DIRECTORY_SEPARATOR . $template );
	}

	private function get_template_path( string $template ): string {
		return plugin_dir_path( POWER_BOARD_PLUGIN_FILE ) . $template . self::TEMPLATE_END;
	}

	public function include_checkout_html( string $template, array $data = [] ): void {
		$data['template_service'] = $this;

		if ( ! empty( $data ) ) {
			extract( $data );
		}

		$path = $this->get_checkout_path( $template );

		if ( file_exists( $path ) ) {
			include $path; // nosemgrep: audit.php.lang.security.file.inclusion-arg  --  the following require is safe because we are checking if the file exists, and it is not a user input.
		}
	}

	private function get_checkout_path( string $template ): string {

		return $this->get_template_path( $this->templateCheckoutDir . DIRECTORY_SEPARATOR . $template );
	}
}
