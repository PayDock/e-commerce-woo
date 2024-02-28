<?php

namespace PowerBoard\API;

use PowerBoard\Abstract\AbstractApiService;

class VaultService extends AbstractApiService
{
    const ENDPOINT = 'vault/payment_sources';

    protected array $allowedAction = [
        'create' => self::METHOD_POST
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