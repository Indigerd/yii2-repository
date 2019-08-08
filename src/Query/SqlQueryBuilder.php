<?php

namespace Indigerd\Repository\Query;

use yii\db\Query;
use yii\db\QueryInterface;
use yii\db\Expression;
use Indigerd\Repository\Exception\UpdateException;
use Indigerd\Repository\Exception\DeleteException;
use Indigerd\Repository\Relation\Relation;
use yii\db\TableSchema;

class SqlQueryBuilder extends AbstractQueryBuilder
{
    protected $schemas = [];

    protected function createQuery(): QueryInterface
    {
        return new Query();
    }

    protected function getSchema($collectionName): TableSchema
    {
        if (!isset($this->schemas[$collectionName])) {
            $this->schemas[$collectionName] = $this->connection->getSchema()->getTableSchema($collectionName);
        }
        return $this->schemas[$collectionName];
    }

    protected function getPrimaryKeys(): array
    {
        return $this->getSchema($this->collectionName)->primaryKey;
    }

    public function queryOne(array $conditions, array $relations = []): ?array
    {
        $select = [$this->collectionName . '*'];
        foreach ($relations as $relation) {
            /** @var Relation $relation */
            $columns = $this->getSchema($relation->getRelatedCollection())->getColumnNames();
            foreach ($columns as $column) {
                $select[] = $relation->getRelatedCollection() . '.' . $column . ' as ' . $relation->getRelatedCollection() . '_relation_' . $column;
            }
        }
        /** @var Query $query */
        $query = $this->createQuery();
        $query
            ->select(\implode(',', $select))
            ->from($this->collectionName)
            ->where($conditions);

        foreach ($relations as $relation) {
            $joinCondition = $this->collectionName . '.' . $relation->getField() . '=' . $relation->getRelatedCollection() . '.' . $relation->getRelatedField();
            $query->join($relation->getRelationType(), $relation->getRelatedCollection(), $joinCondition);
        }

        return $query->one($this->connection);
    }

    public function queryAll(array $conditions, array $order = [], int $limit = 0, int $offset = 0, array $relations = []): array
    {
        $select = [$this->collectionName . '*'];
        foreach ($relations as $relation) {
            /** @var Relation $relation */
            $columns = $this->getSchema($relation->getRelatedCollection())->getColumnNames();
            foreach ($columns as $column) {
                $select[] = $relation->getRelatedCollection() . '.' . $column . ' as ' . $relation->getRelatedCollection() . '_relation_' . $column;
            }
        }
        /** @var Query $query */
        $query = $this->createQuery();
        $query
            ->select(\implode(',', $select))
            ->from($this->collectionName)
            ->where($conditions);

        if ($limit > 0) {
            $query->limit($limit)->offset($offset);
        }

        if (!empty($order)) {
            $query->orderBy($order);
        }

        foreach ($relations as $relation) {
            $joinCondition = $this->collectionName . '.' . $relation->getField() . '=' . $relation->getRelatedCollection() . '.' . $relation->getRelatedField();
            $query->join($relation->getRelationType(), $relation->getRelatedCollection(), $joinCondition);
        }

        return $query->all($this->connection);
    }

    public function insert(array $data): ?array
    {
        return $this->connection->schema->insert($this->collectionName, $data);
    }

    public function updateOne(array $data): void
    {
        $primaryKeys = $this->getPrimaryKeys();
        $conditions = [];
        foreach ($primaryKeys as $key) {
            if (empty($data[$key])) {
                throw new UpdateException($data, "Primary key $key not provided");
            }
            $conditions[$key] = $data[$key];
            unset($data[$key]);
        }
        $this->updateAll($data, $conditions);
    }

    public function updateAll(array $data, array $conditions): int
    {
        $command = $this->connection->createCommand();
        $command->update($this->collectionName, $data, $conditions);
        return $command->execute();
    }

    public function deleteOne(array $data): void
    {
        $primaryKeys = $this->getPrimaryKeys();
        $conditions = [];
        foreach ($primaryKeys as $key) {
            if (empty($data[$key])) {
                throw new DeleteException($data, "Primary key $key not provided");
            }
            $conditions[$key] = $data[$key];
        }
        $this->deleteAll($conditions);
    }

    public function deleteAll(array $conditions): int
    {
        $command = $this->connection->createCommand();
        $command->delete($this->collectionName, $conditions);
        return $command->execute();
    }

    public function aggregate(string $expression, array $conditions): string
    {
        /** @var Query $query */
        $query = $this->createQuery();
        return (string)$query
            ->select(new Expression($expression))
            ->from($this->collectionName)
            ->where($conditions)
            ->scalar($this->connection);
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
