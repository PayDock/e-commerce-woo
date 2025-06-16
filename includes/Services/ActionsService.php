<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

declare( strict_types=1 );

namespace WooPlugin\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WooPlugin\Controllers\Admin\WidgetController;
use WooPlugin\Controllers\Integrations\PaymentController;
use WooPlugin\Enums\SettingsSectionEnum;
use WooPlugin\Helpers\OrderHelper;
use WooPlugin\Helpers\PaymentMethodHelper;
use WooPlugin\Util\MasterWidgetBlock;
use WC_Data_Exception;
use WC_Order;

class ActionsService {
	protected static ?ActionsService $instance = null;
	protected string $last_shipping_id         = '';

	protected const SECTION_HOOK = 'woocommerce_get_sections';

	public static function get_instance(): ActionsService {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function __construct() {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'before_woocommerce_init', [ $this, 'init_before_woocommerce' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_blocks_loaded', [ $this, 'register_payment_method' ] );
	}

	public function init_before_woocommerce() {
		$this->add_compatibility_with_woocommerce();
		$this->add_cart_hooks();
		$this->add_settings_actions();
		$this->add_order_actions();
		$this->add_edit_order_actions();
	}

	protected function add_compatibility_with_woocommerce(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', PLUGIN_FILE );
			FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', PLUGIN_FILE );
		}
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	public function add_cart_hooks(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'cart_item_quantity_changed' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_add_to_cart', [ $this, 'add_to_cart' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_removed_coupon', [ $this, 'remove_coupon' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_applied_coupon', [ $this, 'add_coupon' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-update-shipping', [ $this, 'form_order_update_shipping' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-update-shipping', [ $this, 'form_order_update_shipping' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-update-order-notes', [ $this, 'classic_order_update_notes' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-update-order-notes', [ $this, 'classic_order_update_notes' ] );
	}

	public function register_master_widget_block( PaymentMethodRegistry $registry ) {
		$registry->register( new MasterWidgetBlock() );
	}

	public function cart_item_quantity_changed() {
		$this->calculate_totals_and_save_cookie();
	}

	public function add_to_cart() {
		$this->calculate_totals_and_save_cookie();
	}

	public function cart_item_removed() {
		$this->calculate_totals_and_save_cookie();
	}

	public function remove_coupon() {
		$this->calculate_totals_and_save_cookie();
		$this->update_order_cart_hash();
	}

	public function add_coupon() {
		$this->calculate_totals_and_save_cookie();
		$this->update_order_cart_hash();
	}

	public function update_order_cart_hash() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;
		/* @noinspection PhpUndefinedFunctionInspection */
		$session = WC()->session;
		if ( ! empty( $session ) ) {
			$order_id = $session->get( 'store_api_draft_order' );
			if ( ! empty( $order_id ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$order = wc_get_order( $order_id );
				if ( ! empty( $order ) && $order instanceof WC_Order ) {
					$test = $cart->get_cart_hash();
					$order->set_cart_hash( $test );
					$order->calculate_totals();
					$order->save();
				}
			}
		}
	}

	public function form_order_update_shipping() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, PLUGIN_TEXT_DOMAIN . '-update-shipping' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) ] );

			return;
		}

		$this->order_update_shipping();
		$this->update_order_cart_hash();
	}

	/**
	 * Updates customer notes from order when order notes field is updated.
	 *
	 * @throws WC_Data_Exception If trying to save invalid data to customer notes.
	 */
	public function classic_order_update_notes() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, PLUGIN_TEXT_DOMAIN . '-update-order-notes' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) ] );

			return;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$order_notes = isset( $_REQUEST['value'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['value'] ) ) : '';

		/* @noinspection PhpUndefinedFunctionInspection */
		WC()->session->set( 'order_comments', $order_notes );
		/* @noinspection PhpUndefinedFunctionInspection */
		$custom_order_id = (string) WC()->session->get( PLUGIN_PREFIX . '_draft_order' );

		if ( ! empty( $custom_order_id ) ) {
			$order_id = $custom_order_id;
			OrderHelper::update_order_customer_notes( $order_id, $order_notes );
		}
	}

	public function order_update_shipping() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$session = WC()->session;
		$current_shipping = null;

		if ( ! empty( $session ) ) {
			$chosen_methods   = $session->get( 'chosen_shipping_methods' );
			$current_shipping = is_array( $chosen_methods ) && ! empty( $chosen_methods ) ? $chosen_methods[0] : null;

			if ( $current_shipping !== null && $current_shipping !== $this->last_shipping_id ) {
				$this->last_shipping_id = $current_shipping;

				/* @noinspection PhpUndefinedFunctionInspection */
				setcookie(
					PLUGIN_PREFIX . '_selected_shipping',
					$current_shipping,
					[
						'expires'  => time() + 3600,
						'path'     => '/',
						'domain'   => $_SERVER['HTTP_HOST'],
						'secure'   => is_ssl(),
						'httponly' => false,
						'samesite' => 'Lax',
					]
				);
			}
		}

		$this->calculate_totals_and_save_cookie();

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$cart_total = (string) WC()->cart->get_total( false );
			echo "<script>
				const pluginPrefix = window.widgetSettings.pluginPrefix;
				document.dispatchEvent(new CustomEvent(pluginPrefix + '_cart_total_changed', {
					detail: {
						cartTotal: '" . esc_js( $cart_total ) . "',
						shippingId: '" . esc_js( $current_shipping ) . "'
					}
				}));
			</script>";
		}
	}

	/**
	 * Uses a function (WC) from WooCommerce
	 */
	private function calculate_totals_and_save_cookie() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;

		if ( ! empty( $cart ) ) {
			$cart->calculate_totals();
			$cart_total  = (string) $cart->get_total( false );

			/* @noinspection PhpUndefinedFunctionInspection */
			setcookie(
				'woo_plugin_cart_total',
				$cart_total,
				[
					'expires'  => time() + 3600,
					'path'     => '/',
					'domain'   => $_SERVER['HTTP_HOST'],
					'secure'   => is_ssl(),
					'httponly' => false,
					'samesite' => 'Lax',
				]
			);
		}
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_settings_actions(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			self::SECTION_HOOK,
			function ( $system_tabs ) {
				return array_merge( $system_tabs, [ SettingsSectionEnum::WIDGET_CONFIGURATION => '' ] );
			}
		);
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_order_actions(): void {
		$order_service      = new OrderService();
		$payment_controller = new PaymentController();
		$widget_controller  = new WidgetController();

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_item_add_action_buttons', [ $order_service, 'init_woo_plugin_order_buttons' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_status_changed', [ $order_service, 'status_change_verification' ], 20, 4 );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_create_refund', [ $payment_controller, 'refund_process' ], 10, 2 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_refunded', [ $payment_controller, 'after_refund_process' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-create-charge-intent', [ $widget_controller, 'create_checkout_intent' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-create-charge-intent', [ $widget_controller, 'create_checkout_intent' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'admin_init', [ $order_service, 'remove_bulk_action_message' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-process-payment-result', [ $this, 'process_payment_result_callback' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-process-payment-result', [ $this, 'process_payment_result_callback' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-check-postcode', [ $this, 'check_postcode_callback' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-check-postcode', [ $this, 'check_postcode_callback' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_woo-plugin-check-email', [ $this, 'check_is_valid_email_callback' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_woo-plugin-check-email', [ $this, 'check_is_valid_email_callback' ] );
	}

	public function process_payment_result_callback() {
		PaymentMethodHelper::invoke_gateway_method( 'process_payment_result' );
	}

	public function check_postcode_callback(): void {
		PaymentMethodHelper::invoke_gateway_method( 'check_postcode' );
	}

	public function check_is_valid_email_callback(): void {
		PaymentMethodHelper::invoke_gateway_method( 'check_is_valid_email' );
	}

	public function add_edit_order_actions() {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'disable_payment_method_custom_field_on_order_page' ] );
	}

	public function disable_payment_method_custom_field_on_order_page( WC_Order $order ): void {
		$meta_data       = $order->get_meta_data();
		$meta_data_count = count( $meta_data );
		$id              = '';
		for ( $i = 0; $i < $meta_data_count; $i++ ) {
			if ( $meta_data[ $i ]->key === PLUGIN_PREFIX . '_payment_method' ) {
				$id = $meta_data[ $i ]->id;
				break;
			}
		}
		echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
        		if ( ' . $id . ' !== "" ) {
					$("#meta-' . $id . '-key").prop("disabled", true);
					$("#meta-' . $id . '-value").prop("disabled", true);
        		}
			});
		</script>';
	}

	/**
	 * Add new payment method on checkout page
	 * Uses a function (add_action, plugin_dir_path) from WordPress
	 */
	public function register_payment_method(): void {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			require_once plugin_dir_path( PLUGIN_FILE ) . 'includes/Util/MasterWidgetBlock.php';
			/* @noinspection PhpUndefinedFunctionInspection */
			add_action( 'woocommerce_blocks_payment_method_type_registration', [ $this, 'register_master_widget_block' ] );
		}
	}
}
