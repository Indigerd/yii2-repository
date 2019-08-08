<?php

namespace Indigerd\Repository;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Hydrator\Strategy\ObjectStrategy;
use Indigerd\Repository\Config\ConfigValueInterface;
use Indigerd\Repository\Exception\InsertException;
use Indigerd\Repository\Exception\InvalidModelClassException;
use Indigerd\Repository\Query\QueryBuilderInterface;
use Indigerd\Repository\Relation\Relation;
use Indigerd\Repository\Relation\RelationCollection;

class Repository
{
    protected $queryBuilder;

    protected $hydrator;

    protected $modelClass;

    protected $relationCollection;

    public function __construct(
        QueryBuilderInterface $queryBuilder,
        Hydrator $hydrator,
        ConfigValueInterface $modelClass,
        RelationCollection $relationCollection = null
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->hydrator = $hydrator;
        $this->modelClass = $modelClass->getValue();
        $this->relationCollection = $relationCollection;
    }

    protected function getRelation($name): Relation
    {
        if (\is_null($this->relationCollection)) {
            throw new \InvalidArgumentException("Relation $name do not exist");
        }
        $relation = $this->relationCollection->getRelationByProperty($name);
        if (!($relation instanceof Relation)) {
            throw new \InvalidArgumentException("Relation $name do not exist");
        }
        return $relation;
    }

    protected function normalizeResultSet(array $data, array $relations): array
    {
        /**
         * @var string $property
         * @var Relation $relation
         */
        foreach ($relations as $property => $relation) {
            $relationData = [];
            foreach ($data as $field => $value) {
                if (\strpos($field, $relation->getRelatedCollection() . '_relation_') === 0) {
                    $fieldName = \str_replace($relation->getRelatedCollection() . '_relation_', '', $field);
                    $relationData[$fieldName] = $value;
                }
            }
            $data[$property] = $relationData;
        }
        return $data;
    }

    protected function applyRelationStrategies(array $relations)
    {
        /**
         * @var string $property
         * @var Relation $relation
         */
        foreach ($relations as $property => $relation) {
            $this->hydrator->addStrategy($property, new ObjectStrategy($this->hydrator, $relation->getRelatedModel()));
        }
    }

    public function findOne(array $conditions = [], array $with = []): ?object
    {
        $result = null;
        $relations = [];
        foreach ($with as $relationName) {
            $relations[$relationName] = $this->getRelation($relationName);
        }
        $data = $this->queryBuilder->queryOne($conditions, $relations);
        if (\is_array($data)) {
            if (!empty($relations)) {
                $data = $this->normalizeResultSet($data, $relations);
                $this->applyRelationStrategies($relations);
            }
            $result = $this->hydrator->hydrate($this->modelClass, $data);
        }
        return $result;
    }

    public function findAll(array $conditions = [], array $order = [], int $limit = 0, int $offset = 0, array $with = []): array
    {
        $result = [];
        $relations = [];
        foreach ($with as $relationName) {
            $relations[$relationName] = $this->getRelation($relationName);
        }
        if (!empty($relations)) {
            $this->applyRelationStrategies($relations);
        }
        $data = $this->queryBuilder->queryAll($conditions, $order, $limit, $offset);
        foreach ($data as $row) {
            if (!empty($relations)) {
                $row = $this->normalizeResultSet($row, $relations);
            }
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

    public function updateAll(array $data, array $conditions): int
    {
        return $this->queryBuilder->updateAll($data, $conditions);
    }

    public function deleteAll(array $conditions): int
    {
        return $this->queryBuilder->deleteAll($conditions);
    }
}
