<?php
declare( strict_types=1 );

namespace PowerBoard\API;

use PowerBoard\Abstracts\AbstractApiService;
use PowerBoard\Enums\APIActionEnum;

class TokenService extends AbstractApiService {
	const ENDPOINT = 'payment_sources/tokens';

	protected array $allowed_action = [
		'create' => self::METHOD_POST,
	];

	public function create( array $params ): TokenService {
		$this->parameters     = $params;
		$this->request_action = APIActionEnum::CREATE_TOKEN;

		$this->set_action( 'create' );

		return $this;
	}

	protected function build_endpoint(): ?string {
		return self::ENDPOINT;
	}
}
