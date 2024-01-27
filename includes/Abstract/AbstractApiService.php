<?php

namespace Paydock\Abstract;

use Paydock\API\ConfigService;
use WP_Error;

abstract class AbstractApiService
{
    protected ?string $action;
    protected array $parameters = [];
    protected array $allowedAction = [];

    public function call(): array
    {
        $url = ConfigService::buildApiUrl($this->buildEndpoint());
        $args = [
            'headers' => [
                'content-type' => 'application/json',
            ],
        ];


        if (!empty(ConfigService::$secretKey)) {
            $args['headers']['x-user-secret-key'] = ConfigService::$secretKey;
        }

        if (!empty(ConfigService::$accessToken)) {
            $args['headers']['x-access-token'] = ConfigService::$accessToken;
        }

        if (!empty(ConfigService::$publicKey)) {
            $args['headers']['x-user-public-key'] = ConfigService::$publicKey;
        }

        if ($this->allowedAction[$this->action] === 'POST') {
            $args['body'] = json_encode($this->parameters);
            $request = wp_remote_post($url, $args);
        } else {
            $request = wp_remote_get($url, $args);
        }

        if ($request instanceof WP_Error) {
            return ['status' => 403, 'error' => $request];
        }

        return json_decode($request['body'], true);
    }

    protected function setAction($action): void
    {
        if (empty($this->allowedAction[$action])) {
            throw new \LogicException(__('Not allowed action ' . $action));
        }

        $this->action = $action;
    }
}
