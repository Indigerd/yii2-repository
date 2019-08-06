<?php

namespace Indigerd\Repository;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Config\ConfigValueInterface;
use Indigerd\Repository\Exception\InsertException;
use Indigerd\Repository\Exception\InvalidModelClassException;
use Indigerd\Repository\Query\QueryBuilder;
use yii\db\Connection;
use yii\db\Expression;

class Repository
{
    protected $connection;

    protected $queryBuilder;

    protected $hydrator;

    protected $collectionName;

    protected $modelClass;

    public function __construct(
        Connection $connection,
        QueryBuilder $queryBuilder,
        Hydrator $hydrator,
        ConfigValueInterface $collectionName,
        ConfigValueInterface $modelClass
    ) {
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
        $this->hydrator = $hydrator;
        $this->collectionName = $collectionName->getValue();
        $this->modelClass = $modelClass->getValue();
    }

    public function findOne(array $conditions = []): ?object
    {
        $result = null;
        $query = $this->queryBuilder->create();
        $data = $query->from($this->collectionName)->where($conditions)->one($this->connection);
        if (\is_array($data)) {
            $result = $this->hydrator->hydrate($this->modelClass, $data);
        }
        return $result;
    }

    public function findAll(array $conditions = [], int $limit = 0, int $offset = 0): array
    {
        $result = [];
        $query = $this->queryBuilder->create();
        $query->from($this->collectionName)->where($conditions);
        if ($limit > 0) {
            $query->limit($limit)->offset($offset);
        }
        $data = $query->all($this->connection);
        foreach ($data as $row) {
            $result[] = $this->hydrator->hydrate($this->modelClass, $row);
        }
        return $result;
    }

    public function aggregate(Expression $expression, array $conditions)
    {
        $query = $this->queryBuilder->create();
        return $query->select($expression)->from($this->collectionName)->where($conditions)->scalar($this->connection);
    }

    public function aggregateCount(array $conditions, $q = '*')
    {
        return $this->aggregate(new Expression("COUNT($q)"), $conditions);
    }

    public function aggregateSum(string $field, array $conditions)
    {
        return $this->aggregate(new Expression("SUM($field)"), $conditions);
    }

    public function aggregateAverage(string $field, array $conditions)
    {
        return $this->aggregate(new Expression("AVG($field)"), $conditions);
    }

    public function aggregateMin(string $field, array $conditions)
    {
        return $this->aggregate(new Expression("MIN($field)"), $conditions);
    }

    public function aggregateveMax(string $field, array $conditions)
    {
        return $this->aggregate(new Expression("MAX($field)"), $conditions);
    }

    public function insert(object $model)
    {
        if (!($model instanceof $this->modelClass)) {
            throw new InvalidModelClassException('Invalid model class: ' . get_class($model) . '. Expected ' . $this->modelClass);
        }
        $data = $this->hydrator->extract($model);
        $primaryKeys = $this->queryBuilder->insert($data, $this->collectionName, $this->connection);
        if (!\is_array($primaryKeys)) {
            throw new InsertException($data, $this->collectionName);
        }
        $this->hydrator->hydrate($model, $primaryKeys);
    }
}
