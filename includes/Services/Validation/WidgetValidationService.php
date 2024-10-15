<?php

namespace WooPlugin\Services\Validation;

use WooPlugin\Enums\CustomStylesElements;
use WooPlugin\Enums\WidgetSettings;
use WooPlugin\Services\Settings\WidgetSettingService;
use WooPlugin\Services\SettingsService;

class WidgetValidationService {
	private const VALIDATED_FIELDS = [
		'PAYMENT_CARD_TITLE',
		'PAYMENT_CARD_DESCRIPTION',
		'PAYMENT_BANK_ACCOUNT_TITLE',
		'PAYMENT_BANK_ACCOUNT_DESCRIPTION',
		'PAYMENT_WALLET_APPLE_PAY_DESCRIPTION',
		'PAYMENT_WALLET_APPLE_PAY_DESCRIPTION',
		'PAYMENT_WALLET_GOOGLE_PAY_TITLE',
		'PAYMENT_WALLET_GOOGLE_PAY_DESCRIPTION',
		'PAYMENT_WALLET_AFTERPAY_V2_TITLE',
		'PAYMENT_WALLET_AFTERPAY_V2_DESCRIPTION',
		'PAYMENT_WALLET_PAYPAL_TITLE',
		'PAYMENT_WALLET_PAYPAL_DESCRIPTION',
		'PAYMENT_A_P_M_S_AFTERPAY_V1_TITLE',
		'PAYMENT_A_P_M_S_AFTERPAY_V1_DESCRIPTION',
		'PAYMENT_A_P_M_S_AFTERPAY_V1_MIN',
		'PAYMENT_A_P_M_S_AFTERPAY_V1_MAX',
		'PAYMENT_A_P_M_S_ZIP_TITLE',
		'PAYMENT_A_P_M_S_ZIP_DESCRIPTION',
		'PAYMENT_A_P_M_S_ZIP_MIN',
		'PAYMENT_A_P_M_S_ZIP_MAX',
	];


	private const ENABLED_CONDITION = 'yes';
	private $errors = [];

	private $result = [];

	private $data = [];

	private $service;

	public function __construct( WidgetSettingService $service ) {
		$this->service = $service;
		$this->prepareFormData();
		$this->validate();

		$option_key = $service->get_option_key();
		do_action( 'woocommerce_update_option', [ 'id' => $option_key ] );

		return update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $service->id, $service->settings ),
			'yes'
		);
	}

	private function prepareFormData(): void {
		$post_data = $this->service->get_post_data();
		foreach ( $this->service->get_form_fields() as $key => $field ) {
			try {
				$isMin = false !== stripos( strtolower( $key ), 'min' );
				$isMax = false !== stripos( strtolower( $key ), 'max' );

				if ( $isMax || $isMin ) {
					$number             = (float) str_replace( ',', '.',
						$this->service->get_field_value( $key, $field, $post_data ) );
					$this->data[ $key ] = round( $number, 2 );
				} else {
					$this->data[ $key ] = $this->service->get_field_value( $key, $field, $post_data );
				}

				$this->result[ $key ] = $this->data[ $key ];

				if ( 'select' === $field['type'] || 'checkbox' === $field['type'] ) {
					do_action( 'woocommerce_update_non_option_setting', [
						'id'    => $key,
						'type'  => $field['type'],
						'value' => $this->data[ $key ],
					] );
				}
			} catch ( \Exception $e ) {
				$this->service->add_error( $e->getMessage() );
			}
		}
	}

	private function validate(): void {
		$versionKey       = SettingsService::getInstance()->getOptionName( $this->service->id, [
			WidgetSettings::VERSION()->name,
		] );
		$customVersionKey = SettingsService::getInstance()->getOptionName( $this->service->id, [
			WidgetSettings::CUSTOM_VERSION()->name,
		] );
		$customStyleKey   = SettingsService::getInstance()->getOptionName( $this->service->id, [
			WidgetSettings::STYLE_CUSTOM()->name,
		] );
		$validated        = $this->getValidatedKeys();
		foreach ( $this->data as $key => $value ) {
			$settingName = array_search( $key, $validated );
			$isMin       = false !== stripos( strtolower( $settingName ), 'min' );
			$isMax       = false !== stripos( strtolower( $settingName ), 'max' );

			if (
				( $settingName && $isMin && ( $value != (string) floatval( $value ) ) )
				|| ( $settingName && $isMax && ! empty( $value ) && ( $value != (string) floatval( $value ) ) )
			) {
				/* translators: %s: Title of form field. */
				$this->errors[] = sprintf( __( "%s must be numeric.", PLUGIN_TEXT_DOMAIN ),
					WidgetSettings::{$settingName}()->getFullTitle() );
			} elseif ( $settingName && $isMin && ( 0 > (float) $value ) ) {
				/* translators: %s: Title of form field. */
				$this->errors[] = sprintf( __( "%s cannot be negative.", PLUGIN_TEXT_DOMAIN ),
					WidgetSettings::{$settingName}()->getFullTitle() );
			} elseif (
				$settingName
				&& $isMax
				&& ! empty( $value )
				&& ( (float) $value < (float) $this->data[ str_replace( 'MAX', 'MIN', $key ) ] ) ) {
				/* translators: %s: Title of form field. */
				$this->errors[] = sprintf( __( "%s cannot be less than the min value.", PLUGIN_TEXT_DOMAIN ),
					WidgetSettings::{$settingName}()->getFullTitle() );
			} elseif ( $settingName && empty( $value ) && ! $isMin && ! $isMax ) {
				/* translators: %s: Title of form field. */
				$this->errors[] = sprintf( __( "%s can't be empty.", PLUGIN_TEXT_DOMAIN ),
					WidgetSettings::{$settingName}()->getFullTitle() );
			}

			$decoded = json_decode( $value, true );

			if (
				( $key == $customStyleKey )
				&& ! empty( $value )
				&& (
					(
						! $decoded
						&& ( JSON_ERROR_NONE !== json_last_error() )
					)
					|| ! $this->validateCustomStyles( $decoded )
				)
			) {
				$this->errors[] = __( 'Custom styles must be a valid JSON.', PLUGIN_TEXT_DOMAIN );
			}
		}

		if ( 'custom' == $this->data[ $versionKey ] && empty( $this->data[ $customVersionKey ] ) ) {
			$this->errors[] = __( "Version can't be empty.", PLUGIN_TEXT_DOMAIN );
		}
	}

	private function getValidatedKeys(): array {
		$service = SettingsService::getInstance();
		$result  = [];

		foreach ( self::VALIDATED_FIELDS as $field ) {
			$result[ $field ] = $service->getOptionName( $this->service->id, [
				WidgetSettings::{$field}()->name,
			] );
		}

		return $result;
	}

	private function validateCustomStyles( array $customStyles ): bool {
		$elements = CustomStylesElements::getElements();
		foreach ( $customStyles as $element => $styles ) {
			if ( ! in_array( $element, $elements ) ) {
				return false;
			}

			foreach ( $styles as $style => $value ) {
				if ( ! in_array( $style, CustomStylesElements::getElementFor( $element )->getStyleKeys() ) ) {
					return false;
				}
			}
		}

		return true;
	}

	public function getResult(): array {
		return $this->result;
	}

	public function getErrors(): array {
		return $this->errors;
	}
}
