<?php

namespace Indigerd\Repository\Query;

use yii\db\Connection;
use yii\db\Query;
use yii\db\QueryInterface;
use yii\db\Expression;

class SqlQueryBuilder extends AbstractQueryBuilder
{
    protected function createQuery(): QueryInterface
    {
        return new Query();
    }

    public function queryOne(array $conditions): ?array
    {
        /** @var Query $query */
        $query = $this->createQuery();
        return $query->from($this->collectionName)->where($conditions)->one($this->connection);
    }

    public function queryAll(array $conditions, array $order = [], int $limit = 0, int $offset = 0): array
    {
        /** @var Query $query */
        $query = $this->createQuery();
        $query->from($this->collectionName)->where($conditions);
        if ($limit > 0) {
            $query->limit($limit)->offset($offset);
        }
        return $query->all($this->connection);
    }

    public function insert(array $data): ?array
    {
        return $this->connection->schema->insert($this->collectionName, $data);
    }

    public function aggregate(string $expression, array $conditions): string
    {
        /** @var Query $query */
        $query = $this->createQuery();
        return (string)$query->select(new Expression($expression))->from($this->collectionName)->where($conditions)->scalar($this->connection);
    }

    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        if (empty($field)) {
            $field = '*';
        }
        return $this->aggregate("COUNT($field)", $conditions);
    }

    public function aggregateSum(string $field, array $conditions = []): string
    {
        return $this->aggregate("SUM($field)", $conditions);
    }

    public function aggregateAverage(string $field, array $conditions = []): string
    {
        return $this->aggregate("AVG($field)", $conditions);
    }

    public function aggregateMin(string $field, array $conditions = []): string
    {
        return $this->aggregate("MIN($field)", $conditions);
    }

    public function aggregateMax(string $field, array $conditions = []): string
    {
        return $this->aggregate("MAX($field)", $conditions);
    }
}
