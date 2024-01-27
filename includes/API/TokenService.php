<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class TokenService extends AbstractApiService
{
    const ENDPOINT = 'payment_sources/tokens';

    protected array $allowedAction = [
        'create' => 'POST',
    ];

    public function create(array $params): TokenService
    {
        $this->parameters = $params;

        $this->setAction('create');

        return $this;
    }

    protected function buildEndpoint(): ?string
    {
        return self::ENDPOINT;
    }
}
