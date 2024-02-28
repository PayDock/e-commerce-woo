<?php

namespace PowerBoard\Abstract;

use PowerBoard\API\ConfigService;
use WP_Error;

abstract class AbstractApiService
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

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

        if (!empty(ConfigService::$publicKey)) {
            $args['headers']['x-user-public-key'] = ConfigService::$publicKey;
        }

        $args['headers']['X-'.POWER_BOARD_TEXT_DOMAIN.'-Meta'] = 'V'
            .POWER_BOARD_PLUGIN_VERSION
            .'_woocommerce_'
            .WC()->version;

        switch ($this->allowedAction[$this->action]) {
            case 'POST':
                $args['body'] = json_encode($this->parameters);
                $parsed_args = wp_parse_args($args, [
                    'method' => 'POST',
                    'timeout' => 10
                ]);
                break;
            case 'DELETE':
                $parsed_args = wp_parse_args($args, [
                    'method' => 'DELETE',
                    'timeout' => 10
                ]);
                break;
            default:
                $parsed_args = wp_parse_args($args, [
                    'method' => 'GET',
                    'timeout' => 10
                ]);
        }

        $request = _wp_http_get_object()->request($url, $parsed_args);

        if ($request instanceof WP_Error) {
            return ['status' => 403, 'error' => $request];
        }

        $body = json_decode($request['body'], true);

        if ($body === null && json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 403, 'error' => ['message' => 'PowerBoard api not response'], 'body' => $request['body']];
        }

        return $body;
    }

    protected function setAction($action): void
    {
        if (empty($this->allowedAction[$action])) {
            throw new \LogicException(__('Not allowed action '.$action));
        }

        $this->action = $action;
    }
}
