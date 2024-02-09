<?php

namespace Paydock\Exceptions;

class LoggedException extends \Exception
{
    public array $response = [];

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $response = [])
    {
        $this->response = $response;

        parent::__construct(__($message, PAY_DOCK_TEXT_DOMAIN), $code, $previous);
    }

    /**
     * @throws LoggedException
     */
    public static function throw(array $response = []): void
    {
        throw new self(
            __('Something want wrong. Check data, refresh page and try again.', PAY_DOCK_TEXT_DOMAIN),
            0,
            null,
            $response
        );
    }
}
