<?php

namespace Paydock\Repositories;

use Paydock\Abstract\AbstractRepository;
use Paydock\Contracts\Repository;

class LogRepository extends AbstractRepository implements Repository
{

    protected string $table = 'logs';

    protected function getTableDeclaration(): string
    {
        $fullTableName = $this->getFullTableName($this->table);
        $indexTypeName = implode('_', [$fullTableName, 'type', Repository::INDEX_POSTFIX]);
        $indexCreatedAtdName = implode('_', [$fullTableName, 'created_at', Repository::INDEX_POSTFIX]);
        $indexGatewayName = implode('_', [$fullTableName, 'gateway', Repository::INDEX_POSTFIX]);

        return "
            CREATE TABLE IF NOT EXISTS `$fullTableName` (
                `type` varchar(255) NOT NULL ,
                `created_at` datetime default CURRENT_TIMESTAMP,
                `gateway` varchar(255) NOT NULL
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
        return $this->wordpressDB->get_results();
    }
}
