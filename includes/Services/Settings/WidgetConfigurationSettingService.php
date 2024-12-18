<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\MasterWidgetSettings;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\EnvironmentSettings;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Services\HashService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\Validation\ConnectionValidationService;

class WidgetConfigurationSettingService extends AbstractSettingService {
	protected $service = null;

	public function __construct() {
		$this->service = SettingsService::get_instance();
		parent::__construct();

		foreach ( CredentialSettings::cases() as $credential_settings ) {
			if ( in_array( $credential_settings->name, CredentialSettings::get_hashed(), true ) ) {
				$key = $this->service->get_option_name(
					$this->id,
					array(
						SettingGroups::CREDENTIALS()->name,
						$credential_settings->name,
					)
				);

				if ( ! empty( $this->settings[ $key ] ) ) {
					$this->settings[ $key ] = HashService::decrypt( $this->settings[ $key ] );
				}
			}
		}
	}

	public function init_form_fields(): void {
		foreach ( SettingGroups::cases() as $setting_group ) {
			$key = PLUGIN_PREFIX . '_' . $this->service->get_option_name(
				$this->id,
				array(
					$setting_group->name,
					'label',
				)
			);

			$this->form_fields[ $key ] = array(
				'type'  => 'big_label',
				'title' => $setting_group->get_label(),
			);

			switch ( $setting_group->name ) {
				case SettingGroups::ENVIRONMENT()->name:
					$merged_options = $this->get_environment_options();
					break;
				case SettingGroups::CREDENTIALS()->name:
					$merged_options = $this->get_credential_options();
					break;
				case SettingGroups::CHECKOUT()->name:
					$merged_options = $this->get_checkout_options();
					break;
				default:
					$merged_options = array();
					break;
			}

			$this->form_fields = array_merge( $this->form_fields, $merged_options );
		}
	}

	private function get_credential_options(): array {
		$fields = array();

		foreach ( CredentialSettings::cases() as $credential_settings ) {
			$key            = $this->service->get_option_name(
				$this->id,
				array(
					SettingGroups::CREDENTIALS()->name,
					$credential_settings->name,
				)
			);
			$fields[ $key ] = array(
				'type'  => $credential_settings->get_input_type(),
				'title' => $credential_settings->get_label(),
			);
			$description    = $credential_settings->get_description();
			if ( $description ) {
				$fields[ $key ]['description'] = $description;
				$fields[ $key ]['desc_tip']    = true;
			}
		}

		return $fields;
	}

	private function get_checkout_options(): array {
		$fields              = array();
		$access_token        = $this->get_access_token();
		$widget_access_token = $this->get_widget_access_token();
		$environment         = $this->get_environment();
		$version             = $this->get_version();

		foreach ( MasterWidgetSettings::cases() as $checkout_settings ) {
			$key = $this->service->get_option_name(
				$this->id,
				array(
					SettingGroups::CHECKOUT()->name,
					$checkout_settings->name,
				)
			);

			if ( MasterWidgetSettings::VERSION()->name !== $checkout_settings->name ) {
				$fields[ $key ] = array(
					'type'  => $checkout_settings->get_input_type(),
					'title' => preg_replace( array( '/ Id/', '/ id/' ), ' ID', $checkout_settings->get_label() ),
				);

				if ( ! empty( $environment ) ) {
					$options = $checkout_settings->get_options( $environment, $access_token, $widget_access_token, $version );

					if ( ! empty( $options ) && ( 'select' === $checkout_settings->get_input_type() ) ) {
						$fields[ $key ]['options'] = $options;
						$fields[ $key ]['default'] = $checkout_settings->get_default();
					}
				}
			}
		}

		return $fields;
	}

	private function get_environment_options(): array {
		$fields = array();
		foreach ( EnvironmentSettings::cases() as $environment_settings ) {
			$key = $this->service->get_option_name(
				$this->id,
				array(
					SettingGroups::ENVIRONMENT()->name,
					$environment_settings->name,
				)
			);

			$fields[ $key ] = array(
				'type'  => $environment_settings->get_input_type(),
				'title' => preg_replace( array( '/ Id/', '/ id/' ), ' ID', $environment_settings->get_label() ),
			);

			$options = $environment_settings->getOptions();

			if ( ! empty( $options ) && ( 'select' === $environment_settings->get_input_type() ) ) {
				$fields[ $key ]['options'] = $options;
				$fields[ $key ]['default'] = $environment_settings->get_default();
			}
		}

		return $fields;
	}

	public function get_access_token() {
		return HashService::decrypt(
			$this->settings[ $this->service->get_option_name(
				$this->id,
				array(
					SettingGroups::CREDENTIALS()->name,
					CredentialSettings::ACCESS_KEY()->name,
				)
			) ]
		);
	}

	public function get_widget_access_token() {
		return HashService::decrypt(
			$this->settings[ $this->service->get_option_name(
				$this->id,
				array(
					SettingGroups::CREDENTIALS()->name,
					CredentialSettings::WIDGET_KEY()->name,
				)
			) ]
		);
	}

	public function get_environment() {
		return $this->settings[ $this->service->get_option_name(
			$this->id,
			array(
				SettingGroups::ENVIRONMENT()->name,
				EnvironmentSettings::ENVIRONMENT()->name,
			)
		) ];
	}

	public function get_version() {
		return $this->settings[ $this->service->get_option_name(
			$this->id,
			array(
				SettingGroups::CHECKOUT()->name,
				MasterWidgetSettings::VERSION()->name,
			)
		) ];
	}

	public function process_admin_options() {
		$this->init_settings();
		$validation_service = new ConnectionValidationService( $this );

		$hashed_credential_keys = array();
		foreach ( CredentialSettings::cases() as $credential_settings ) {
			if ( in_array( $credential_settings->name, CredentialSettings::get_hashed(), true ) ) {
				$key                            = $this->service->get_option_name(
					$this->id,
					array(
						SettingGroups::CREDENTIALS()->name,
						$credential_settings->name,
					)
				);
				$hashed_credential_keys[ $key ] = $credential_settings;
			}
		}

		foreach ( $this->get_form_fields() as $key => $field ) {
			$type = $this->get_field_type( $field );

			$option_key = $this->plugin_id . $this->id . '_' . $key;
			$value      = isset( $_POST[ $option_key ] ) ? wc_clean( wp_unslash( $_POST[ $option_key ] ) ) : null;

			if ( method_exists( $this, 'validate_' . $type . '_field' ) ) {
				$value = $this->{'validate_' . $type . '_field'}( $key, $value );
			} else {
				$value = $this->validate_text_field( $key, $value );
			}

			if ( array_key_exists( $key, $hashed_credential_keys ) ) {
				if ( '********************' === $value || null === $value ) {
					$value = $this->get_option( $key );
				}
			}

			$this->settings[ $key ] = $value;
		}

		foreach ( $hashed_credential_keys as $key => $credential_settings ) {
			$is_encrypted = HashService::decrypt( $this->settings[ $key ] ) !== $this->settings[ $key ];

			if ( ! empty( $this->settings[ $key ] ) && ! $is_encrypted ) {
				$this->settings[ $key ] = HashService::encrypt( $this->settings[ $key ] );
			}
		}

		foreach ($validation_service->get_errors() as $error ) {
			$this->add_error( $error );
			\WC_Admin_Settings::add_error( $error );
		}

		$option_key = $this->get_option_key();
		do_action( 'woocommerce_update_option', array( 'id' => $option_key ) );

		return update_option(
			$option_key,
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ),
			'yes'
		);
	}

	protected function getId(): string {
		return SettingsTabs::WIDGET_CONFIGURATION()->value;
	}

	public function generate_settings_html( $form_fields = array(), $echo = true ): ?string {

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		foreach ( CredentialSettings::cases() as $credential_settings ) {
			if ( in_array( $credential_settings->name, CredentialSettings::get_hashed() ) ) {
				$key = $this->service->get_option_name(
					$this->id,
					array(
						SettingGroups::CREDENTIALS()->name,
						$credential_settings->name,
					)
				);

				if ( ! empty( $this->settings[ $key ] ) ) {
					$this->settings[ $key ] = '********************';
				} else {
					$this->settings[ $key ] = '';
				}
			}
		}

		return parent::generate_settings_html( $form_fields, $echo );
	}

	public function validate_big_label_field( $key, $value ) {
		return '';
	}

	public function validate_label_field( $key, $value ) {
		return '';
	}
}
