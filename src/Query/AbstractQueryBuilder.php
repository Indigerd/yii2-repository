<?php

namespace Indigerd\Repository\Query;

use yii\db\Connection;
use yii\db\QueryInterface;
use Indigerd\Repository\Config\ConfigValueInterface;

abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
    protected $connection;

    protected $collectionName;

    public function __construct(Connection $connection, ConfigValueInterface $collectionName)
    {
        $this->connection = $connection;
        $this->collectionName = $collectionName->getValue();;
    }

    protected abstract function createQuery(): QueryInterface;

    public abstract function queryOne(array $conditions): ?array;

    public abstract function queryAll(array $conditions, array $order = [], int $limit = 0, int $offset = 0): array ;

    public abstract function insert(array $data): ?array;

    public abstract function updateOne(array $data): void;

    public abstract function deleteOne(array $data): void;

    public abstract function aggregate(string $expression, array $conditions): string;

    public abstract function aggregateCount(string $field = '', array $conditions = []): string ;

    public abstract function aggregateSum(string $field, array $conditions = []): string ;

    public abstract function aggregateAverage(string $field, array $conditions = []): string ;

    public abstract function aggregateMin(string $field, array $conditions = []): string ;

    public abstract function aggregateMax(string $field, array $conditions = []): string ;
}
