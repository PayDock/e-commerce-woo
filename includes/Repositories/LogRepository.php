<?php

namespace Paydock\Repositories;

use Paydock\Abstract\AbstractRepository;
use Paydock\Contracts\Repository;

class LogRepository extends AbstractRepository implements Repository
{

    protected string $table = 'logs';

    public const DEFAULT = 0;

    public const SUCCESS = 1;

    public const ERROR = 2;

    public const AVAILABLE_TYPES = [
        self::DEFAULT,
        self::SUCCESS,
        self::ERROR,
    ];

    protected function getTableDeclaration(): string
    {
        $fullTableName = $this->getFullTableName($this->table);
        $indexTypeName = implode('_', [$fullTableName, 'type', Repository::INDEX_POSTFIX]);
        $indexCreatedAtdName = implode('_', [$fullTableName, 'created_at', Repository::INDEX_POSTFIX]);
        $indexGatewayName = implode('_', [$fullTableName, 'gateway', Repository::INDEX_POSTFIX]);

        return "
            CREATE TABLE IF NOT EXISTS `$fullTableName` (
                `status` varchar(255) NOT NULL ,
                `created_at` datetime default CURRENT_TIMESTAMP,
                `operation` varchar(255) NOT NULL,
                `type` integer NOT NULL,
                `message`  varchar(255),
                `id`  varchar(255)
            );
            CREATE INDEX `$indexTypeName`
            ON `$fullTableName` (`type`);

            CREATE INDEX `$indexCreatedAtdName`
            ON `$fullTableName` (`created_at`);

            CREATE INDEX `$indexGatewayName`
            ON `$fullTableName` (`gateway`);";
    }

    public function getLogs(int $page = 1, int $perPage = 50): array
    {
        $page = $page > 1 ? $page : 1;
        $fullTableName = $this->getFullTableName($this->table);
        $offset = --$page * $perPage;

        return $this->wordpressDB->get_results(
            "SELECT * FROM $fullTableName ORDER BY `created_at` DESC LIMIT $perPage OFFSET $offset;"
        );
    }

    public function createLogRecord(
        string $id,
        string $operation,
        string $status,
        string $message,
        int    $type = self::DEFAULT
    ): void
    {
        if (!in_array($type, self::AVAILABLE_TYPES, true)) {
            $type = self::DEFAULT;
        }

        $this->wordpressDB->insert(
            $this->getFullTableName($this->table),
            compact('operation', 'status', 'type', 'id', 'message')
        );
    }
}
