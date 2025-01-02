<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class CustomerService extends AbstractApiService {
	const ENDPOINT = 'customers';

	protected $allowed_action = [ 
		'create' => self::METHOD_POST,
	];

	public function create( array $params ): CustomerService {
		$this->parameters = $params;

		$this->set_action( 'create' );

		return $this;
	}

	protected function build_endpoint(): ?string {
		return self::ENDPOINT;
	}
}
