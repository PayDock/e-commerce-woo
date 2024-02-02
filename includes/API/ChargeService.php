<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class ChargeService extends AbstractApiService
{
    const ENDPOINT = 'charges';

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