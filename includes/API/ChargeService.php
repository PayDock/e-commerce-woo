<?php
declare( strict_types=1 );

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;
use PowerBoard\Enums\APIActionEnum;

class ChargeService extends AbstractApiService {
	const ENDPOINT               = 'charges';
	const CREATE_INTENT_ENDPOINT = 'checkouts/intent';
	const GET_TEMPLATES_ENDPOINT = 'checkouts/templates';
	const REFUNDS_ENDPOINT       = 'refunds';

	protected array $allowed_action = [
		'create-intent' => self::METHOD_POST,
		'templates'     => self::METHOD_GET,
		'refunds'       => self::METHOD_POST,
	];

	public function create_checkout_intent( array $params ): self {
		$this->parameters     = $params;
		$this->request_action = APIActionEnum::CREATE_INTENT;

		$this->set_action( 'create-intent' );

		return $this;
	}

	public function get_configuration_templates_ids( string $version ): self {
		$this->parameters = [
			'type'    => 'configuration',
			'version' => $version,
		];

		$this->set_action( 'templates' );
		$this->request_action = APIActionEnum::GET_CONFIGURATION_TEMPLATE_IDS;

		return $this;
	}

	public function get_configuration_templates_for_validation(): self {
		$this->parameters = [ 'type' => 'configuration' ];
		$this->set_action( 'templates' );
		$this->request_action = APIActionEnum::GET_CONFIGURATION_TEMPLATES_FOR_VALIDATION;

		return $this;
	}

	public function get_customisation_templates_ids( string $version ): self {
		$this->parameters     = [
			'type'    => 'customisation',
			'version' => $version,
		];
		$this->request_action = APIActionEnum::GET_CUSTOMISATION_TEMPLATE_IDS;

		$this->set_action( 'templates' );

		return $this;
	}


	public function refunds( array $params ): self {
		$this->parameters     = $params;
		$this->request_action = APIActionEnum::REFUND;

		$this->set_action( 'refunds' );

		return $this;
	}

	protected function build_endpoint(): ?string {
		switch ( $this->action ) {
			case 'refunds':
				$result = self::ENDPOINT . '/' . $this->parameters['charge_id'] . '/' . self::REFUNDS_ENDPOINT;
				unset( $this->parameters['charge_id'] );
				break;
			case 'create-intent':
				$result = self::CREATE_INTENT_ENDPOINT;
				break;
			case 'templates':
				$result = self::GET_TEMPLATES_ENDPOINT . '?type=' . $this->parameters['type'];
				$version = $this->parameters['version'];
				if (! empty( $version ) ) {
					$result .= '&version=' . $version;
				}
				unset( $this->parameters['charge_id'] );
				break;
			default:
				$result = self::ENDPOINT;
		}

		return $result;
	}
}
