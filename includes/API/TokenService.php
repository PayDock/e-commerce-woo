<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class TokenService extends AbstractApiService
{
    const ENDPOINT = 'payment_sources/tokens';

    protected array $allowedAction = [
        'create' => 'GET'
    ];

    public function create(): TokenService
    {
        $this->setAction('create');

        return $this;
    }

    protected function buildEndpoint(): ?string
    {
        return self::ENDPOINT . '?public_key=' . urlencode(ConfigService::$publicKey);
    }
}