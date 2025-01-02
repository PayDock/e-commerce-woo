<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class TokenService extends AbstractApiService {
	const ENDPOINT = 'payment_sources/tokens';

	protected $allowed_action = [ 
		'create' => self::METHOD_POST,
	];

	public function create( array $params ): TokenService {
		$this->parameters = $params;

		$this->set_action( 'create' );

		return $this;
	}

	protected function build_endpoint(): ?string {
		return self::ENDPOINT;
	}
}
