<?php

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;

class ChargeService extends AbstractApiService {
	const ENDPOINT = 'charges';
	const CREATE_INTENT_ENDPOINT = 'checkouts/intent';
	const GET_TEMPLATES_ENDPOINT = 'checkouts/templates';
	const CAPTURE_ENDPOINT = 'capture';
	const REFUNDS_ENDPOINT = 'refunds';
	protected $allowed_action = [
		'create-intent' => self::METHOD_POST,
		'templates' => self::METHOD_GET,
		'capture' => self::METHOD_POST,
		'refunds' => self::METHOD_POST,
		'cancel-authorised' => self::METHOD_DELETE,
	];

	public function create_checkout_intent( array $params): self {
		$this->parameters = $params;

		$this->set_action( 'create-intent' );

		return $this;
	}

	public function get_configuration_templates_ids( string $version ): self {
		$this->parameters = ['type' => 'configuration', 'version' => $version ];

		$this->set_action( 'templates' );

		return $this;
	}

	public function get_customisation_templates_ids( string $version ): self {
		$this->parameters = ['type' => 'customisation', 'version' => $version ];

		$this->set_action( 'templates' );

		return $this;
	}

	public function capture( array $params ): self {
		$this->parameters = $params;

		$this->set_action( 'capture' );

		return $this;
	}


	public function refunds( array $params ): self {
		$this->parameters = $params;

		$this->set_action( 'refunds' );

		return $this;
	}

	public function cancel_authorised( array $params ): self {
		$this->parameters = $params;

		$this->set_action( 'cancel-authorised' );

		return $this;
	}

	protected function build_endpoint(): ?string {
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
