<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class ChargeService extends AbstractApiService {
	const ENDPOINT = 'charges';
	const CREATE_INTENT_ENDPOINT = 'checkouts/intent';
	const GET_TEMPLATES_ENDPOINT = 'checkouts/templates';
	const CAPTURE_ENDPOINT = 'capture';
	const REFUNDS_ENDPOINT = 'refunds';

	protected $directCharge = null;

	protected $id = null;
	protected $allowedAction = [
		'create-intent' => self::METHOD_POST,
		'templates' => self::METHOD_GET,
		'capture' => self::METHOD_POST,
		'refunds' => self::METHOD_POST,
		'cancel-authorised' => self::METHOD_DELETE,
	];

	public function createCheckoutIntent( array $params): self {
		$this->parameters = $params;

		$this->setAction( 'create-intent' );

		return $this;
	}

	public function get_configuration_templates_ids( string $version ): self {
		$this->parameters = ['type' => 'configuration', 'version' => $version ];

		$this->setAction( 'templates' );

		return $this;
	}

	public function get_customisation_templates_ids( string $version ): self {
		$this->parameters = ['type' => 'customisation', 'version' => $version ];

		$this->setAction( 'templates' );

		return $this;
	}

	public function capture( array $params ): self {
		$this->parameters = $params;

		$this->setAction( 'capture' );

		return $this;
	}


	public function refunds( array $params ): self {
		$this->parameters = $params;

		$this->setAction( 'refunds' );

		return $this;
	}

	public function cancelAuthorised( array $params ): self {
		$this->parameters = $params;

		$this->setAction( 'cancel-authorised' );

		return $this;
	}

	protected function buildEndpoint(): ?string {
		switch ( $this->action ) {
			case 'refunds':
				$result = self::ENDPOINT . '/' . $this->parameters['charge_id'] . '/' . self::REFUNDS_ENDPOINT;
				unset( $this->parameters['charge_id'] );
				break;
			case 'capture':
			case 'cancel-authorised':
				$result = self::ENDPOINT . '/' . $this->parameters['charge_id'] . '/' . self::CAPTURE_ENDPOINT;
				unset( $this->parameters['charge_id'] );
				break;
			case 'create-intent':
				$result = self::CREATE_INTENT_ENDPOINT;
				break;
			case 'templates':
				$result = self::GET_TEMPLATES_ENDPOINT . '?type=' . $this->parameters['type'];
				unset( $this->parameters['charge_id'] );
				break;
			default:
				$result = self::ENDPOINT;
		}

		return $result;
	}
}
