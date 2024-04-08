<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Contracts\Repository;
use PowerBoard\PowerBoardPlugin;

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

    public function createTable(): void
    {
        require_once(ABSPATH.'wp-admin/includes/upgrade.php');

        dbDelta($this->getTableDeclaration());
    }

    abstract protected function getTableDeclaration(): string;

    public function dropTable(): void
    {
        $this->wordpressDB->query('DROP TABLE IF EXISTS '.$this->getFullTableName($this->table));
    }

    public function getFullTableName(string $table): string
    {
        return $this->tablePrefix.PowerBoardPlugin::PLUGIN_PREFIX.'_'.$table;
    }
}
