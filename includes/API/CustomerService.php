<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class CustomerService extends AbstractApiService
{
    const ENDPOINT = 'customers';

    protected array $allowedAction = [
        'create' => 'POST'
    ];

    public function create(array $params): CustomerService
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