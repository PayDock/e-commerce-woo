<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Services\Assets\AdminAssetsService;
use PowerBoard\Services\TemplateService;
use WC_Payment_Gateway;

abstract class AbstractSettingService extends WC_Payment_Gateway {
	public $current_section = null;
	protected $template_service;

	public function __construct() {
		$available_sections = array_map(
			function ( $item ) {
				return strtolower( $item->value );
			},
			SettingsTabs::allCases()
		);

		$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );

		if ( in_array( $section, $available_sections, true ) ) {
			$this->current_section = $section;
		}

		$this->id                 = $this->getId();
		$this->enabled            = $this->get_option( 'enabled' );
		$this->method_title       = __( 'PowerBoard Gateway', 'power-board' );
		$this->method_description = __(
			'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard\'s payment orchestration.',
			'power-board'
		);

		$this->title = __( 'PowerBoard Gateway', 'power-board' );

		$this->icon = POWER_BOARD_PLUGIN_URL . 'assets/images/logo.png';

		$this->init_settings();
		$this->init_form_fields();

		$this->has_fields = is_checkout() && \WC_Blocks_Utils::has_block_in_page( wc_get_page_id( 'checkout' ), 'woocommerce/checkout' );

		if ( is_admin() ) {
			new AdminAssetsService();
			$this->template_service = new TemplateService( $this );
		}
	}

	abstract protected function getId(): string;

	public function parent_generate_settings_html( $formFields = array(), $echo = true ): ?string {
		return parent::generate_settings_html( $formFields, $echo );
	}

	public function generate_settings_html( $form_fields = array(), $echo = true ): ?string {
		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		$tabs = $this->getTabs();

		if ( $echo ) {
			$this->template_service->includeAdminHtml( 'admin', compact( 'tabs', 'form_fields' ) );
		} else {
			return $this->template_service->getAdminHtml( 'admin', compact( 'tabs', 'form_fields' ) );
		}

		return null;
	}

	protected function getTabs(): array {
		return array(
			SettingsTabs::WIDGET_CONFIGURATION()->value => array(
				'label'  => __( 'Widget Configuration', 'power-board' ),
				'active' => SettingsTabs::WIDGET_CONFIGURATION()->value === $this->current_section,
			),
			SettingsTabs::LOG()->value                  => array(
				'label'  => __( 'Logs', 'power-board' ),
				'active' => SettingsTabs::LOG()->value === $this->current_section,
			),
		);
	}

	public function generate_label_html( $key, $value ) {
		return $this->template_service->getAdminHtml( 'label', compact( 'key', 'value' ) );
	}

	public function generate_big_label_html( $key, $value ) {
		return $this->template_service->getAdminHtml( 'big-label', compact( 'key', 'value' ) );
	}
}
