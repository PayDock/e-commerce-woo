<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

use PowerBoard\Enums\CredentialSettingsEnum;

class CredentialSettingsHelper {
	public static function get_input_type( string $key ): string {
		switch ( $key ) {
			case CredentialSettingsEnum::ACCESS_KEY:
				return 'password';
			default:
				return '';
		}
	}

	public static function get_label( string $key ): string {
		switch ( $key ) {
			case CredentialSettingsEnum::ACCESS_KEY:
				return 'API Access Token';
			default:
				return '';
		}
	}

	public static function get_description( string $key ): string {
		switch ( $key ) {
			case CredentialSettingsEnum::ACCESS_KEY:
				return 'Enter your API Access Token. This token is used to securely authenticate your payment operations. It is also used to retrieve the values for the Checkout Template ID fields shown below.';
			default:
				return '';
		}
	}
}
