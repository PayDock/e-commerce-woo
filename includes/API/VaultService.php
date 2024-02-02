<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class VaultService extends AbstractApiService
{
    const ENDPOINT = 'vault/payment_sources';

    protected array $allowedAction = [
        'create' => 'POST'
    ];

    public function create(array $params): self
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