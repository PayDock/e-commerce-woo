<?php

namespace Paydock\Repositories;

class UserTokenRepository
{
    const CARD_TOKENS_KEY = 'paydock_card_tokens';

    private $cache;
    private int $userId;

    public function __construct()
    {
        if (!is_user_logged_in()) {
            throw new \Exception('User not logged in');
        }

        $this->userId = get_current_user_id();
    }

    public function getUserTokens(): array
    {
        if ($this->cache === null) {
            $this->cache = get_user_meta($this->userId, self::CARD_TOKENS_KEY, true);
            
            if (empty($this->cache)) {
                $this->cache = [];
            }
        }

        return $this->cache;
    }

    public function saveUserToken(array $token): int|bool
    {
        $tokens = $this->getUserTokens();
        $tokens[] = $token;

        $result = update_user_meta($this->userId, self::CARD_TOKENS_KEY, $tokens);

        $this->cleanCache();

        return $result;
    }

    private function cleanCache(): void
    {
        $this->cache = null;
    }
}