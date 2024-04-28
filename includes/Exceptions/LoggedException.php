<?php

namespace PowerBoard\Exceptions;

use Exception;

class LoggedException extends Exception {
	public $response = [];

	public function __construct( string $message = '', int $code = 0, $previous = null, array $response = [] ) {
		$this->response = $response;

		parent::__construct( __( $message, 'power_board' ), $code, $previous );
	}

	public static function throw( array $response = [] ): void {
		throw new self(
			__( 'Oops! Something went wrong. Please check the information provided and try again. ', 'power_board' ),
			0,
			null,
			$response
		);
	}
}
