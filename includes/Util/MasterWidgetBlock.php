<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractBlock;
use PowerBoard\Services\Checkout\MasterWidgetPaymentService;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

final class MasterWidgetBlock extends AbstractBlock {
	protected const SCRIPT = 'blocks';
	protected $name        = 'power_board';

	public function initialize() {
		$this->settings = get_option( 'woocommerce_power_board_settings', array() );
		$this->gateway  = new MasterWidgetPaymentService();
	}

	public function get_payment_method_data() {
		SDKAdapterService::get_instance();
		$settings_service = SettingsService::get_instance();

		if ( ! is_admin() ) {
			WC()->cart->calculate_totals();
		}

		return array(
			// Wordpress data.
			'widgetToken'             => $settings_service->get_widget_access_token(),
			// Woocommerce data.
			'amount'                  => WC()->cart->total,
			'currency'                => strtoupper( get_woocommerce_currency() ),
			// Widget.
			'title'                   => 'PowerBoard',
			// Keys.
			'environment'             => $settings_service->get_environment(),
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		);
	}
}
