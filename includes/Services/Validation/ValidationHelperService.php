<?php

namespace PayDock\Services\Validation;

use Paydock\Enums\OtherPaymentMethods;

class ValidationHelperService {
	private $value;

	public function __construct( $value ) {
		$this->value = $value;
	}

	public function isFloat(): bool {
		return $this->value == (float) $this->value;
	}

	public function isUUID(): bool {
		return is_string( $this->value ) && preg_match( '/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $this->value );
	}

	public function isServiceId(): bool {
		return is_string( $this->value ) && preg_match( '/^[a-f\d]{24}$/i', $this->value );
	}

	public function isValidAPMId(): bool {
		return in_array( $this->value, [
			OtherPaymentMethods::AFTERPAY()->getId(),
			OtherPaymentMethods::ZIPPAY()->getId(),
		] );
	}
}
