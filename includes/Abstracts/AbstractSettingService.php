<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Enums\SettingsTabsEnum;
use PowerBoard\Services\Assets\AdminAssetsService;
use PowerBoard\Services\TemplateService;
use PowerBoard\Repositories\LogRepository;
use WC_Blocks_Utils;
use WC_Payment_Gateway;

abstract class AbstractSettingService extends WC_Payment_Gateway {
	public $current_section = null;
	protected $template_service;

	public function __construct() {
		$available_sections = array_map(
			function ( $item ) {
				return strtolower( $item->value );
			},
			SettingsTabsEnum::cases()
		);

		$section = wp_strip_all_tags( filter_input( INPUT_GET, 'section' ) );

		if ( in_array( $section, $available_sections, true ) ) {
			$this->current_section = $section;
		}

		$this->id                 = $this->get_id();
		$this->method_title       = __( 'PowerBoard Gateway', 'power-board' );
		$this->method_description = __(
			'PowerBoard simplify how you manage your payments. Reduce costs, technical headaches & streamline compliance using PowerBoard\'s payment orchestration.',
			'power-board'
		);

		$this->title = __( 'PowerBoard Gateway', 'power-board' );

		$this->icon = POWER_BOARD_PLUGIN_URL . 'assets/images/logo.png';

		$this->init_settings();
		$this->init_form_fields();

		$this->has_fields = is_checkout() && WC_Blocks_Utils::has_block_in_page( wc_get_page_id( 'checkout' ), 'woocommerce/checkout' );

		if ( is_admin() ) {
			new AdminAssetsService();
			$this->template_service = new TemplateService( $this );
		}
	}

	abstract protected function get_id(): string;

	public function parent_generate_settings_html( $form_fields = [], $should_echo = true ): ?string {
		return parent::generate_settings_html( $form_fields, $should_echo );
	}

	public function generate_settings_html( $form_fields = [], $should_echo = true ): ?string {
		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		$tabs = $this->getTabs();

		$args = compact( 'tabs', 'form_fields' );

		if ( $this->current_section === SettingsTabsEnum::LOG ) {
			$args['records'] = $this->getLogs();
		}

		if ( $should_echo ) {
			$this->template_service->include_admin_html( 'admin', $args );
		} else {
			return $this->template_service->get_admin_html( 'admin', $args );
		}

		return null;
	}

	protected function getTabs(): array {
		return [
			SettingsTabsEnum::WIDGET_CONFIGURATION => [
				'label'  => __( 'Widget Configuration', 'power-board' ),
				'active' => SettingsTabsEnum::WIDGET_CONFIGURATION === $this->current_section,
			],
			SettingsTabsEnum::LOG                  => [
				'label'  => __( 'Logs', 'power-board' ),
				'active' => SettingsTabsEnum::LOG === $this->current_section,
			],
		];
	}

	protected function getLogs(): array {
		$page     = get_query_var( 'page_number' );
		$page     = ! empty( $page ) ? sanitize_text_field( $page ) : 1;
		$per_page = get_query_var( 'per_page' );
		$per_page = ! empty( $per_page ) ? sanitize_text_field( $per_page ) : 50;
		$order_by = get_query_var( 'orderBy' );
		$order_by = ! empty( $order_by ) ? sanitize_text_field( $order_by ) : 'created_at';
		$order    = get_query_var( 'order' );
		$order    = ! empty( $order ) ? sanitize_text_field( $order ) : 'desc';

		$records = ( new LogRepository() )->getLogs( $page, $per_page, $order_by, $order );

		return $records;
	}
}
