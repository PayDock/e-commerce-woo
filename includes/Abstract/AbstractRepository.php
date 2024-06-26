<?php

namespace Paydock\Abstract;

use Paydock\Contracts\Repository;
use Paydock\PaydockPlugin;

abstract class AbstractRepository implements Repository
{
    protected \wpdb $wordpressDB;
    protected string $tablePrefix;

    public function __construct()
    {
        global $table_prefix, $wpdb;

        $this->wordpressDB = $wpdb;
        $this->tablePrefix = $table_prefix;
    }

    public function getFullTableName(string $table): string
    {
        return $this->tablePrefix . PaydockPlugin::PLUGIN_PREFIX . '_' . $table;
    }

    public function createTable(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($this->getTableDeclaration());
    }

    public function dropTable(): void
    {
        $this->wordpressDB->query('DROP TABLE IF EXISTS ' . $this->getFullTableName($this->table));
    }

    abstract protected function getTableDeclaration(): string;
}
