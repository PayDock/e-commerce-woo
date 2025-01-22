<?php

namespace PowerBoard\Services;

use PowerBoard\Services\Checkout\MasterWidgetPaymentService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class FiltersService {
	protected static ?FiltersService $instance = null;

	protected function __construct() {
		$this->add_woocommerce_filters();
		$this->add_settings_link();
	}

	public static function get_instance(): FiltersService {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Uses a function (add_filter) from WordPress
	 */
	protected function add_woocommerce_filters(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_in_woocommerce_payment_class' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'plugins_loaded', [ $this, 'woo_text_override' ] );
	}

	/**
	 * Uses functions (add_filter, plugin_basename) from WordPress
	 */
	protected function add_settings_link(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'plugin_action_links_' . plugin_basename( POWER_BOARD_PLUGIN_FILE ), [ $this, 'get_setting_link' ] );
	}

	/**
	 * Uses a function (is_admin) from WordPress
	 */
	public function register_in_woocommerce_payment_class( array $methods ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_admin() ) {
			$methods[] = WidgetConfigurationSettingService::class;
		} else {
			$methods[] = MasterWidgetPaymentService::class;
		}

		return $methods;
	}

	/**
	 * Uses functions (admin_url and __) from WordPress
	 */
	public function get_setting_link( array $links ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		array_unshift(
			$links,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . PLUGIN_PREFIX ),
				__( 'Settings', 'power-board' )
			)
		);

		return $links;
	}

	/**
	 * Uses functions (plugin_dir_path and load_textdomain) from WordPress
	 */
	public function woo_text_override() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$mofile = plugin_dir_path( __FILE__ ) . 'languages/woo-override-en_US.mo';
		/* @noinspection PhpUndefinedFunctionInspection */
		load_textdomain( 'woocommerce', $mofile );
	}
}
