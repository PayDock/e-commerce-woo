<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class VaultService extends AbstractApiService {
	const ENDPOINT = 'vault/payment_sources';

	protected $allowed_action = [ 
		'create' => self::METHOD_POST,
	];

	public function create( array $params ): self {
		$this->parameters = $params;

		$this->set_action( 'create' );

		return $this;
	}

	protected function build_endpoint(): ?string {
		return self::ENDPOINT;
	}
}
