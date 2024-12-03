<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\APMsSettings;
use PowerBoard\Enums\BankAccountSettings;
use PowerBoard\Enums\CardSettings;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\DSTypes;
use PowerBoard\Enums\FraudTypes;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Enums\SaveCardOptions;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Enums\TypeExchangeOTT;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Enums\WalletSettings;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\HashService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\Validation\ConnectionValidationService;

class LiveConnectionSettingService extends AbstractSettingService {

	private $error_message = '';

	public function init_form_fields(): void {
		$service = SettingsService::getInstance();

		foreach ( SettingGroups::cases() as $settingGroup ) {
			$key = PowerBoardPlugin::PLUGIN_PREFIX . '_' . $service->getOptionName( $this->id, [
					$settingGroup->name,
					'label',
				] );

			if ( SettingGroups::CARD() == $settingGroup ) {
				$this->form_fields[ $key . '_label' ] = [
					'type'  => 'big_label',
					'title' => __( 'Payment Methods:', 'power-board' ),
				];
			}

			$this->form_fields[ $key ] = [
				'type'  => 'big_label',
				'title' => $settingGroup->getLabel(),
			];

			switch ( $settingGroup->name ) {
				case SettingGroups::CREDENTIALS()->name:
					$mergedOptions = $this->getCredentialOptions();
					break;
				case SettingGroups::CARD()->name:
					$mergedOptions = $this->getCardOptions();
					break;
				case SettingGroups::BANK_ACCOUNT()->name:
					$mergedOptions = $this->getBankAccountOptions();
					break;
				case SettingGroups::WALLETS()->name:
					$mergedOptions = $this->getWalletsOptions();
					break;
				case SettingGroups::A_P_M_S()->name:
					$mergedOptions = $this->getAPMsOptions();
					break;
				default:
					$mergedOptions = [];
					break;
			}

			$this->form_fields = array_merge( $this->form_fields, $mergedOptions );
		}
	}

	private function getCredentialOptions(): array {
		$fields  = [];
		$service = SettingsService::getInstance();

		foreach ( CredentialSettings::cases() as $credentialSettings ) {
			if ( CredentialSettings::SANDBOX()->name != $credentialSettings->name ) {
				$key            = $service->getOptionName( $this->id, [
					SettingGroups::CREDENTIALS()->name,
					$credentialSettings->name,
				] );
				$fields[ $key ] = [
					'type'  => $credentialSettings->getInputType(),
					'title' => $credentialSettings->getLabel(),
					'custom_attributes' => $credentialSettings->getInputAttributes(),
				];
				$description    = $credentialSettings->getDescription();
				if ( $description ) {
					$fields[ $key ]['description'] = $description;
					$fields[ $key ]['desc_tip']    = true;
				}
			}
		}

		return $fields;
	}

	private function getCardOptions(): array {
		$fields  = [];
		$service = SettingsService::getInstance();

		foreach ( CardSettings::cases() as $cardSettings ) {
			$key            = $service->getOptionName( $this->id, [
				SettingGroups::CARD()->name,
				$cardSettings->name
			] );
			$fields[ $key ] = [
				'type'    => $cardSettings->getInputType(),
				'title'   => preg_replace( [ '/ Id/', '/ id/' ], ' ID', $cardSettings->getLabel() ),
				'default' => $cardSettings->getDefault(),
				'custom_attributes' => $cardSettings->getInputAttributes(),
			];

			$description = $cardSettings->getDescription();
			if ( $description ) {
				$fields[ $key ]['description'] = $description;
				$fields[ $key ]['desc_tip']    = true;
			}

			switch ( $cardSettings->name ) {
				case CardSettings::DS()->name:
					$fields[ $key ]['options'] = DSTypes::toArray();
					break;
				case CardSettings::FRAUD()->name:
					$fields[ $key ]['options'] = FraudTypes::toArray();
					break;
				case CardSettings::SAVE_CARD_OPTION()->name:
					$fields[ $key ]['options'] = SaveCardOptions::toArray();
					break;
				case CardSettings::TYPE_EXCHANGE_OTT()->name:
					$fields[ $key ]['options'] = TypeExchangeOTT::toArray();
					break;
				default:
					$fields[ $key ]['options'] = [];
					break;
			}
		}

		return $fields;
	}

	private function getBankAccountOptions(): array {
		$fields  = [];
		$service = SettingsService::getInstance();

		foreach ( BankAccountSettings::cases() as $bankAccountSettings ) {
			$key = $service->getOptionName( $this->id, [
				SettingGroups::BANK_ACCOUNT()->name,
				$bankAccountSettings->name,
			] );

			$fields[ $key ] = [
				'type'  => $bankAccountSettings->getInputType(),
				'title' => $bankAccountSettings->getLabel(),
			];

			$description = $bankAccountSettings->getDescription();
			if ( $description ) {
				$fields[ $key ]['description'] = $description;
				$fields[ $key ]['desc_tip']    = true;
			}

			if ( BankAccountSettings::SAVE_CARD_OPTION() == $bankAccountSettings ) {
				$fields[ $key ]['options'] = SaveCardOptions::toArray();
			}
		}

		return $fields;
	}

	private function getWalletsOptions(): array {
		$fields  = [];
		$service = SettingsService::getInstance();

		foreach ( WalletPaymentMethods::cases() as $walletPaymentMethods ) {
			$fields[ $service->getOptionName( $this->id, [
				SettingGroups::WALLETS()->name,
				$walletPaymentMethods->name,
				'label',
			] ) ] = [
				'type'  => 'label',
				'title' => $walletPaymentMethods->getLabel(),
			];

			foreach ( WalletSettings::cases() as $walletSettings ) {
				$key = $service->getOptionName( $this->id, [
					SettingGroups::WALLETS()->name,
					$walletPaymentMethods->name,
					$walletSettings->name,
				] );

				$fields[ $key ] = [
					'type'  => $walletSettings->getInputType(),
					'title' => preg_replace( [ '/ Id/', '/ id/' ], ' ID', $walletSettings->getLabel() ),
					'custom_attributes' => $walletSettings->getInputAttributes(),
				];

				$description = $walletSettings->getDescription();
				if ( $description ) {
					$fields[ $key ]['description'] = $description;
					$fields[ $key ]['desc_tip']    = true;
				}
			}

			if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $walletPaymentMethods->name ) {
				$key            = $service->getOptionName( $this->id, [
					SettingGroups::WALLETS()->name,
					$walletPaymentMethods->name,
					'pay_later',
				] );
				$fields[ $key ] = [
					'type'  => 'checkbox',
					'title' => __( 'Pay Later', 'power-board' ),
					'custom_attributes' => [],
				];
			}
		}

		return $fields;
	}

	public function getAPMsOptions(): array {
		$fields  = [];
		$service = SettingsService::getInstance();

		foreach ( OtherPaymentMethods::cases() as $otherPaymentMethods ) {
			$fields[ $service->getOptionName( $this->id, [
				SettingGroups::A_P_M_S()->name,
				$otherPaymentMethods->name,
				'label',
			] ) ] = [
				'type'  => 'label',
				'title' => $otherPaymentMethods->getLabel(),
			];

			foreach ( APMsSettings::cases() as $APMsSettings ) {
				if ( OtherPaymentMethods::AFTERPAY()->name === $otherPaymentMethods->name &&
					 APMsSettings::DIRECT_CHARGE()->name === $APMsSettings->name ) {
					continue;
				}

				$key = $service->getOptionName( $this->id, [
					SettingGroups::A_P_M_S()->name,
					$otherPaymentMethods->name,
					$APMsSettings->name,
				] );

				$fields[ $key ] = [
					'type'  => $APMsSettings->getInputType(),
					'title' => $APMsSettings->getLabel(),
					'custom_attributes' => $APMsSettings->getInputAttributes(),
				];

				$description = $APMsSettings->getDescription();
				if ( $description ) {
					$fields[ $key ]['description'] = $description;
					$fields[ $key ]['desc_tip']    = true;
				}

				if ( APMsSettings::SAVE_CARD_OPTION() == $APMsSettings ) {
					$fields[ $key ]['options'] = SaveCardOptions::toArray();
				}
			}
		}

		return $fields;
	}

	public function process_admin_options() {

		$this->init_settings();
		$validationService = new ConnectionValidationService( $this );

		$service = SettingsService::getInstance();
		$encryptedFields = [];

		foreach ( CredentialSettings::cases() as $credentialSettings ) {
			if ( in_array( $credentialSettings->name, CredentialSettings::getHashed() ) ) {
				$key = $service->getOptionName( $this->id, [
					SettingGroups::CREDENTIALS()->name,
					$credentialSettings->name,
				] );
				$encryptedFields[ $key ] = $credentialSettings;
			}
		}

		foreach ( CardSettings::cases() as $cardSettings ) {
			if ( in_array( $cardSettings->name, [
				CardSettings::GATEWAY_ID()->name,
				CardSettings::DS_SERVICE_ID()->name,
				CardSettings::FRAUD_SERVICE_ID()->name
			] ) ) {
				$key = $service->getOptionName( $this->id, [
					SettingGroups::CARD()->name,
					$cardSettings->name,
				] );
				$encryptedFields[ $key ] = $cardSettings;
			}
		}

		foreach ( WalletPaymentMethods::cases() as $walletPaymentMethods ) {
			foreach ( WalletSettings::cases() as $walletSettings ) {
				if ( in_array( $walletSettings->name, [
					WalletSettings::GATEWAY_ID()->name,
					WalletSettings::FRAUD_SERVICE_ID()->name
				] ) ) {
					$key = $service->getOptionName( $this->id, [
						SettingGroups::WALLETS()->name,
						$walletPaymentMethods->name,
						$walletSettings->name,
					] );
					$encryptedFields[ $key ] = $walletSettings;
				}
			}
		}

		foreach ( OtherPaymentMethods::cases() as $otherPaymentMethods ) {
			foreach ( APMsSettings::cases() as $APMsSetting ) {
				if ( in_array( $APMsSetting->name, [
					APMsSettings::GATEWAY_ID()->name,
					APMsSettings::FRAUD_SERVICE_ID()->name
				] ) ) {
					$key = $service->getOptionName( $this->id, [
						SettingGroups::A_P_M_S()->name,
						$otherPaymentMethods->name,
						$APMsSetting->name,
					] );
					$encryptedFields[ $key ] = $APMsSetting;
				}
			}
		}

		if ( $validationService->hasErrors() ) {
			foreach ( $validationService->getErrors() as $error ) {
				$this->add_error( $error );
				\WC_Admin_Settings::add_error( $error );
			}
			return false;
		}

		$postedValues = [];

		foreach ( $this->get_form_fields() as $key => $field ) {
			$type = $this->get_field_type( $field );

			$option_key = $this->plugin_id . $this->id . '_' . $key;
			$value = isset( $_POST[ $option_key ] ) ? wc_clean( wp_unslash( $_POST[ $option_key ] ) ) : null;

			if ( method_exists( $this, 'validate_' . $type . '_field' ) ) {
				$value = $this->{'validate_' . $type . '_field'}( $key, $value );
			} else {
				$value = $this->validate_text_field( $key, $value );
			}

			$postedValues[ $key ] = $value;
		}

		foreach ( $postedValues as $key => $value ) {
			if ( array_key_exists( $key, $encryptedFields ) ) {
				if ( $value === '********************' || $value === null ) {
					$value = $this->get_option( $key );
				} elseif ( $value === '' ) {
					$value = '';
				} else {
					try {
						$value = HashService::encrypt( $value );
					} catch ( \Exception $e ) {
						$this->error_message = $e->getMessage();
						add_action( 'admin_notices', array( $this, 'display_error_message' ) );
						return;
					}
				}
			}

			$this->settings[ $key ] = $value;
		}

		$option_key = $this->get_option_key();
		do_action( 'woocommerce_update_option', [ 'id' => $option_key ] );

		return update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ),
			'yes'
		);

	}

	public function generate_settings_html( $form_fields = [], $echo = true ): ?string {

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		$service = SettingsService::getInstance();

		foreach ( CredentialSettings::cases() as $credentialSettings ) {
			if ( in_array( $credentialSettings->name, CredentialSettings::getHashed() ) ) {
				$key = $service->getOptionName( $this->id, [
					SettingGroups::CREDENTIALS()->name,
					$credentialSettings->name,
				]);

				if ( ! empty( $this->settings[ $key ] ) ) {
					$form_fields[ $key ]['default'] = '********************';
				} else {
					$form_fields[ $key ]['default'] = '';
				}
			}
		}

		foreach ( CardSettings::cases() as $cardSettings ) {
			if ( in_array( $cardSettings->name, [
				CardSettings::GATEWAY_ID()->name,
				CardSettings::DS_SERVICE_ID()->name,
				CardSettings::FRAUD_SERVICE_ID()->name
			] ) ) {
				$key = $service->getOptionName( $this->id, [
					SettingGroups::CARD()->name,
					$cardSettings->name,
				]);

				if ( ! empty( $this->settings[ $key ] ) ) {
					$form_fields[ $key ]['default'] = '********************';
				} else {
					$form_fields[ $key ]['default'] = '';
				}
			}
		}

		foreach ( WalletPaymentMethods::cases() as $walletPaymentMethods ) {
			foreach ( WalletSettings::cases() as $walletSettings ) {
				if ( in_array( $walletSettings->name, [
					WalletSettings::GATEWAY_ID()->name,
					WalletSettings::FRAUD_SERVICE_ID()->name
				] ) ) {
					$key = $service->getOptionName( $this->id, [
						SettingGroups::WALLETS()->name,
						$walletPaymentMethods->name,
						$walletSettings->name,
					] );

					if ( ! empty( $this->settings[ $key ] ) ) {
						$form_fields[ $key ]['default'] = '********************';
					} else {
						$form_fields[ $key ]['default'] = '';
					}
				}
			}
		}

		foreach ( OtherPaymentMethods::cases() as $otherPaymentMethods ) {
			foreach ( APMsSettings::cases() as $APMsSetting ) {
				if ( in_array( $APMsSetting->name, [
					APMsSettings::GATEWAY_ID()->name,
					APMsSettings::FRAUD_SERVICE_ID()->name
				] ) ) {
					$key = $service->getOptionName( $this->id, [
						SettingGroups::A_P_M_S()->name,
						$otherPaymentMethods->name,
						$APMsSetting->name,
					] );

					if ( ! empty( $this->settings[ $key ] ) ) {
						$form_fields[ $key ]['default'] = '********************';
					} else {
						$form_fields[ $key ]['default'] = '';
					}
				}
			}
		}

		return parent::generate_settings_html( $form_fields, $echo );

	}

	protected function getId(): string {
		return SettingsTabs::LIVE_CONNECTION()->value;
	}

	public function validate_big_label_field( $key, $value ) {
		return '';
	}

	public function validate_label_field( $key, $value ) {
		return '';
	}

	public function display_error_message() {

		if ( ! empty( $this->error_message ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $this->error_message ) . '</p></div>';
		}

	}

}
