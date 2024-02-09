<?php

namespace Paydock\API;

use Paydock\Abstract\AbstractApiService;

class ChargeService extends AbstractApiService
{
    const ENDPOINT = 'charges';
    const STANDALONE_FRAUD_ENDPOINT = 'fraud';
    const STANDALONE_3DS_ENDPOINT = 'standalone-3ds';

    protected array $allowedAction = [
        'create' => 'POST',
        'standalone-fraud' => 'POST',
        'standalone-3ds' => 'POST'
    ];

    public function create(array $params): self
    {
        $this->parameters = $params;

        $this->setAction('create');

        return $this;
    }
    public function standaloneFraud(array $params): self
    {
        $this->parameters = $params;

        $this->setAction('standalone-fraud');

        return $this;
    }

    public function standalone3Ds(array $params): self
    {
        $this->parameters = $params;

        $this->setAction('standalone-3ds');

        return $this;
    }

    protected function buildEndpoint(): ?string
    {
        switch ($this->action) {
            case 'standalone-fraud':
                $result = self::ENDPOINT . '/' . self::STANDALONE_FRAUD_ENDPOINT;
                break;
            case 'standalone-3ds':
                $result = self::ENDPOINT . '/' . self::STANDALONE_3DS_ENDPOINT;
                break;
            default:
                $result = self::ENDPOINT;
        }

        return $result;
    }
}