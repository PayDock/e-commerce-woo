<?php

namespace Paydock\Exceptions;

use Exception;

class LoggedException extends Exception {
	public $response = [];

	public function __construct( string $message = '', int $code = 0, $previous = null, array $response = [] ) {
		$this->response = $response;

		parent::__construct( __( $message, 'pay_dock' ), $code, $previous );
	}

	public static function throw( array $response = [] ): void {
		throw new self(
			__( 'Oops! Something went wrong. Please check the information provided and try again. ', 'pay_dock' ),
			0,
			null,
			$response
		);
	}
}
