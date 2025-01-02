<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class NotificationService extends AbstractApiService {
	const ENDPOINT = 'notifications';

	protected $allowed_action = [
		'create' => self::METHOD_POST,
		'search' => self::METHOD_GET,
	];

	public function create( $params ): NotificationService {
		$this->set_action( 'create' );
		$this->parameters = $params;

		return $this;
	}

	public function search( array $parameters = [] ): NotificationService {
		$this->set_action( 'search' );
		$this->parameters = $parameters;

		return $this;
	}

	protected function build_endpoint(): ?string {
		switch ( $this->action ) {
			case 'create':
				$result = self::ENDPOINT;
				break;
			default:
				$result = self::ENDPOINT . '?limit=-1';
		}

		return $result;
	}
}
