<?php

namespace PowerBoard\API;

use PowerBoard\Abstract\AbstractApiService;

class CustomerService extends AbstractApiService
{
    const ENDPOINT = 'customers';

    protected array $allowedAction = [
        'create' => self::METHOD_POST
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