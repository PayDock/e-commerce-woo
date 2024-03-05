<?php

namespace PowerBoard\Repositories;

class UserCustomerRepository
{
    const USER_CUSTOMERS_KEY = 'power_board_card_customers';

    private $cache;
    private int $userId;

    public function __construct()
    {
        if (!is_user_logged_in()) {
            throw new \Exception('User not logged in');
        }

        $this->userId = get_current_user_id();
    }

    public function getUserCustomers(): array
    {
        if ($this->cache === null) {
            $this->cache = get_user_meta($this->userId, self::USER_CUSTOMERS_KEY, true);
            
            if (empty($this->cache)) {
                $this->cache = [];
            }
        }

        return $this->cache;
    }

    public function getUserCustomer(string $customers): array
    {
        $customers = $this->getUserCustomers();

        $customer = [];

        foreach($customers as $item)
        {
            if ($item['_id'] === $customers) {
                $customer = $item;
                break;
            }
        }

        return $customer;
    }

    public function saveUserCustomer(array $customer): int|bool
    {
        $customers = $this->getUserCustomers();
        $customers[] = $customer;

        $result = update_user_meta($this->userId, self::USER_CUSTOMERS_KEY, $customers);

        $this->cleanCache();

        return $result;
    }

    public function updateUserCustomer(string $customerId, $data): int|bool
    {
        $customers = $this->getUserCustomers();

        $customers = array_map(function($value) use ($customerId, $data) {
            if ($value['_id'] === $customerId) {
                $value = array_merge($value, $data);
            }
            return $value;
        }, $customers);

        $result = update_user_meta($this->userId, self::USER_CUSTOMERS_KEY, $customers);

        $this->cleanCache();

        return $result;
    }

    public function deleteAllUserCustomers(): int|bool
    {
        $result = delete_user_meta($this->userId, self::USER_CUSTOMERS_KEY);

        $this->cleanCache();

        return $result;
    }

    private function cleanCache(): void
    {
        $this->cache = null;
    }
}