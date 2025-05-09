<?php
declare( strict_types=1 );

namespace WooPlugin\Helpers;

use WooPlugin\Enums\MasterWidgetSettingsEnum;
use WooPlugin\Enums\SettingGroupsEnum;
use WooPlugin\Services\Settings\APIAdapterService;

class MasterWidgetSettingsHelper {
	public static function get_input_type( string $key ): string {
		switch ( $key ) {
			case MasterWidgetSettingsEnum::VERSION:
			case MasterWidgetSettingsEnum::CONFIGURATION_ID:
			case MasterWidgetSettingsEnum::CUSTOMISATION_ID:
			default:
				return 'select';
		}
	}

	public static function get_label( string $key ): string {
		switch ( $key ) {
			case MasterWidgetSettingsEnum::VERSION:
				return 'Version';
			case MasterWidgetSettingsEnum::CONFIGURATION_ID:
				return 'Configuration Template ID';
			case MasterWidgetSettingsEnum::CUSTOMISATION_ID:
				return 'Customisation Template ID (optional)';
			default:
				return ucfirst( strtolower( str_replace( '_', ' ', $key ) ) );
		}
	}

	public static function get_options_for_ui( string $key, $env, $access_token, $version ): array {
		switch ( $key ) {
			case MasterWidgetSettingsEnum::VERSION:
				return self::get_checkout_versions_for_ui( $env, $access_token );
			case MasterWidgetSettingsEnum::CONFIGURATION_ID:
				return self::get_configuration_ids_for_ui( $env, $access_token, $version );
			case MasterWidgetSettingsEnum::CUSTOMISATION_ID:
				return self::get_customisation_ids_for_ui( $env, $access_token, $version );
			default:
				return [];
		}
	}

	public static function get_checkout_versions_for_ui( $env, $access_token ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! self::is_plugin_settings_page() || ! empty( get_transient( PLUGIN_PREFIX . '_is_fetching_versions' ) ) ) {
			return [];
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		set_transient( PLUGIN_PREFIX . '_is_fetching_versions', true, 60 );

		/* @noinspection PhpUndefinedFunctionInspection */
		$stored_checkout_versions = get_transient( PLUGIN_PREFIX . '_checkout_versions' );
		if ( ! empty( $stored_checkout_versions ) ) {
			$checkout_versions_for_ui = $stored_checkout_versions;
		} else {
			$api_adapter_service      = self::init_api_adapter( $env, $access_token );
			$plugin_configuration     = $api_adapter_service->get_plugin_configuration_by_version();
			$checkout_versions_for_ui = $plugin_configuration['checkout_versions'];
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_checkout_versions', $plugin_configuration['checkout_versions'], 60 );
			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_environment_url', 'https://widget.paydock.com/sdk/v1.112.30-beta/widget.umd.js', 60 );
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		delete_transient( PLUGIN_PREFIX . '_is_fetching_versions' );
		return [ '' => 'Select a checkout version' ] + ( $checkout_versions_for_ui ?? [] );
	}

	/**
	 * Uses functions (get_transient, delete_transient and set_transient) from WordPress
	 */
	public static function get_configuration_ids_for_ui( $env, $access_token, $version ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! self::is_plugin_settings_page() || ! empty( get_transient( PLUGIN_PREFIX . '_is_fetching_configuration_templates' ) ) ) {
			return [];
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		set_transient( PLUGIN_PREFIX . '_is_fetching_configuration_templates', true, 60 );

		/* @noinspection PhpUndefinedFunctionInspection */
		$stored_configuration_templates = get_transient( PLUGIN_PREFIX . '_configuration_templates_' . $env );
		$has_error                      = false;
		if ( ! empty( $stored_configuration_templates ) ) {
			$configuration_templates = $stored_configuration_templates;
		} else {
			$api_adapter_service     = self::init_api_adapter( $env, $access_token );
			$result                  = $api_adapter_service->get_configuration_templates_ids( $version );
			$has_error               = $result['error'];
			$data                    = $result['resource']['data'] ?? [];
			$configuration_templates = MasterWidgetTemplatesHelper::map_templates( $data, ! empty( $has_error ) );

			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_configuration_templates_' . $env, $configuration_templates, 60 );
		}

		$configuration_id_key = SettingsHelper::get_option_name(
				PLUGIN_PREFIX,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CONFIGURATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $configuration_templates, ! empty( $has_error ), $configuration_id_key, MasterWidgetSettingsEnum::CONFIGURATION_ID );

		/* @noinspection PhpUndefinedFunctionInspection */
		delete_transient( PLUGIN_PREFIX . '_is_fetching_configuration_templates' );
		return $configuration_templates;
	}

	/**
	 * Uses functions (set_transient, delete_transient and get_transient) from WordPress
	 */
	public static function get_customisation_ids_for_ui( $env, $access_token, $version ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! self::is_plugin_settings_page() || ! empty( get_transient( PLUGIN_PREFIX . '_is_fetching_customisation_templates' ) ) ) {
			return [];
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		set_transient( PLUGIN_PREFIX . '_is_fetching_customisation_templates', true, 60 );

		/* @noinspection PhpUndefinedFunctionInspection */
		$stored_customisation_templates = get_transient( PLUGIN_PREFIX . '_customisation_templates_' . $env );
		$has_error                      = false;
		if ( ! empty( $stored_customisation_templates ) ) {
			$customisation_templates = $stored_customisation_templates;
		} else {
			$api_adapter_service     = self::init_api_adapter( $env, $access_token );
			$result                  = $api_adapter_service->get_customisation_templates_ids( $version );
			$has_error               = $result['error'];
			$data                    = $result['resource']['data'] ?? [];
			$customisation_templates = MasterWidgetTemplatesHelper::map_templates( $data, ! empty( $has_error ), true );

			/* @noinspection PhpUndefinedFunctionInspection */
			set_transient( PLUGIN_PREFIX . '_customisation_templates_' . $env, $customisation_templates, 60 );
		}

		$customisation_id_key = SettingsHelper::get_option_name(
			PLUGIN_PREFIX,
				[
					SettingGroupsEnum::CHECKOUT,
					MasterWidgetSettingsEnum::CUSTOMISATION_ID,
				]
			);
		MasterWidgetTemplatesHelper::validate_or_update_template_id( $customisation_templates, ! empty( $has_error ), $customisation_id_key, MasterWidgetSettingsEnum::CUSTOMISATION_ID );

		/* @noinspection PhpUndefinedFunctionInspection */
		delete_transient( PLUGIN_PREFIX . '_is_fetching_customisation_templates' );
		return $customisation_templates;
	}

	public static function init_api_adapter( $env, $access_token ): APIAdapterService {
		$api_adapter_service = APIAdapterService::get_instance();
		$api_adapter_service->initialise( $env, $access_token );
		return $api_adapter_service;
	}

	/**
	 * Uses functions (sanitize_text_field and wp_unslash) from WordPress
	 * It is safe to ignore NonceVerification because we are sanitizing the strings and not using them to submit data
	 * phpcs:disable WordPress.Security.NonceVerification -- processed through the WooCommerce form handler
	 */
	public static function is_plugin_settings_page(): bool {
		/* @noinspection PhpUndefinedFunctionInspection */
		return isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === 'wc-settings' && isset( $_GET['tab'] ) && sanitize_text_field( wp_unslash( $_GET['tab'] ) ) === 'checkout' && isset( $_GET['section'] ) && sanitize_text_field( wp_unslash( $_GET['section'] ) ) === PLUGIN_PREFIX;
	}
	// phpcs:enable
}
