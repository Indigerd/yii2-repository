<?php

namespace Indigerd\Repository\Query;

use yii\db\Connection;
use yii\db\Query;

class QueryBuilder
{
    public function create(): Query
    {
        return new Query();
    }

    public function insert(array $data, string $collectionName, Connection $connection): ?array
    {
        return $connection->schema->insert($collectionName, $data);
    }
}
