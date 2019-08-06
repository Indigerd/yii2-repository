<?php

namespace Indigerd\Repository;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Config\ConfigValueInterface;
use Indigerd\Repository\Exception\InsertException;
use Indigerd\Repository\Exception\InvalidModelClassException;
use Indigerd\Repository\Query\QueryBuilderInterface;

class Repository
{
    protected $queryBuilder;

    protected $hydrator;

    protected $modelClass;

    public function __construct(
        QueryBuilderInterface $queryBuilder,
        Hydrator $hydrator,
        ConfigValueInterface $modelClass
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->hydrator = $hydrator;
        $this->modelClass = $modelClass->getValue();
    }

    public function findOne(array $conditions = []): ?object
    {
        $result = null;
        $data = $this->queryBuilder->queryOne($conditions);
        if (\is_array($data)) {
            $result = $this->hydrator->hydrate($this->modelClass, $data);
        }
        return $result;
    }

    public function findAll(array $conditions = [], array $order = [], int $limit = 0, int $offset = 0): array
    {
        $result = [];
        $data = $this->queryBuilder->queryAll($conditions, $order, $limit, $offset);
        foreach ($data as $row) {
            $result[] = $this->hydrator->hydrate($this->modelClass, $row);
        }
        return $result;
    }

    public function aggregate(string $expression, array $conditions)
    {
        return $this->queryBuilder->aggregate($expression, $conditions);
    }

    public function aggregateCount(string $field = '', array $conditions = [])
    {
        return $this->aggregateCount($field, $conditions);
    }

    public function aggregateSum(string $field, array $conditions)
    {
        return $this->aggregateSum($field, $conditions);
    }

    public function aggregateAverage(string $field, array $conditions)
    {
        return $this->aggregateAverage($field, $conditions);
    }

    public function aggregateMin(string $field, array $conditions)
    {
        return $this->aggregateMin($field, $conditions);
    }

    public function aggregateveMax(string $field, array $conditions)
    {
        return $this->aggregateveMax($field, $conditions);
    }

    protected function validateModelClass(object $model)
    {
        if (!($model instanceof $this->modelClass)) {
            throw new InvalidModelClassException('Invalid model class: ' . get_class($model) . '. Expected ' . $this->modelClass);
        }
    }

    public function insert(object $model)
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $primaryKeys = $this->queryBuilder->insert($data);
        if (!\is_array($primaryKeys)) {
            throw new InsertException($data);
        }
        $this->hydrator->hydrate($model, $primaryKeys);
    }

    public function update(object $model)
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $this->queryBuilder->updateOne($data);
    }

    public function delete(object $model)
    {
        $this->validateModelClass($model);
        $data = $this->hydrator->extract($model);
        $this->queryBuilder->deleteOne($data);
    }
}
