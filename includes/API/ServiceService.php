<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class ServiceService extends AbstractApiService {
	const ENDPOINT = 'services';

	protected $allowed_action = [ 
		'get' => self::METHOD_GET,
		'search' => self::METHOD_GET,
	];

	private $id;

	public function get(): ServiceService {
		$this->set_action( 'get' );

		return $this;
	}

	public function setId( $id ): ServiceService {
		$this->id = $id;

		return $this;
	}

	public function search( array $parameters = [] ): ServiceService {
		$this->set_action( 'search' );
		$this->parameters = $parameters;

		return $this;
	}

	protected function build_endpoint(): ?string {
		switch ( $this->action ) {
			case 'get':
				$result = self::ENDPOINT . '/' . urlencode( $this->id );
				break;
			default:
				$result = self::ENDPOINT . '?limit=1000';
		}

		return $result;
	}
}
