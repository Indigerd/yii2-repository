<?php declare(strict_types=1);

namespace Indigerd\Repository\TableGateway;

use Indigerd\Repository\Query\MongoQueryFactory;
use Indigerd\Repository\TableGateway\ConditionBuilder\ConditionBuilder;
use MongoDB\BSON\ObjectId;
use yii\mongodb\Connection;
use yii\mongodb\Query;
use Indigerd\Repository\Exception\DeleteException;
use Indigerd\Repository\Exception\UpdateException;

class MongoTableGateway implements TableGatewayInterface
{
    protected $connection;

    protected $queryFactory;

    protected $conditionBuilder;

    protected $collectionName;

    public function __construct(
        Connection $connection,
        MongoQueryFactory $queryFactory,
        ConditionBuilder $conditionBuilder,
        string $collectionName
    ) {
        $this->connection = $connection;
        $this->queryFactory = $queryFactory;
        $this->conditionBuilder = $conditionBuilder;
        $this->collectionName = $collectionName;
    }

    public function queryOne(array $conditions, array $relations = []): ?array
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        $query
            ->from($this->collectionName)
            ->where($conditions);
        $result = $query->one($this->connection);
        return $result ? $result : null;
    }

    public function insert(array $data): ?array
    {
        $data['_id'] = new ObjectId($data['_id'] ?? null);
        /** @var \yii\mongodb\Connection $connection */
        $connection = $this->connection;
        $connection->getCollection($this->collectionName)->insert($data);
        return ['_id' => $data['_id']];
    }

    public function updateOne(array $data): void
    {
        if (empty($data['_id'])) {
            throw new UpdateException($data, "Primary key _id not provided");
        }
        $conditions = ['_id' => $data['_id']];
        unset($data['_id']);
        $this->updateAll($data, $conditions);
    }

    public function updateAll(array $data, array $conditions): int
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \yii\mongodb\Connection $connection */
        $connection = $this->connection;
        return $connection->getCollection($this->collectionName)->update($conditions, $data);
    }

    public function deleteOne(array $data): void
    {
        if (empty($data['_id'])) {
            throw new DeleteException($data, "Primary key _id not provided");
        }
        $conditions = ['_id' => $data['_id']];
        $this->deleteAll($conditions);

    }

    public function deleteAll(array $conditions): int
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var \yii\mongodb\Connection $connection */
        $connection = $this->connection;
        return $connection->getCollection($this->collectionName)->remove($conditions);
    }

    public function queryAll(array $conditions, array $order = [], int $limit = 0, int $offset = 0, array $relations = []): array
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        $query
            ->from($this->collectionName)
            ->where($conditions);

        if ($limit > 0) {
            $query->limit($limit)->offset($offset);
        }

        if (!empty($order)) {
            $query->orderBy($order);
        }

        return $query->all($this->connection);
    }

    public function aggregate(string $expression, array $conditions): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->select($expression)->where($conditions)->scalar($this->connection);
    }

    public function aggregateCount(string $field = '', array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->where($conditions)->count('*', $this->connection);
    }

    public function aggregateSum(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->where($conditions)->sum($field, $this->connection);
    }

    public function aggregateAverage(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->where($conditions)->average($field, $this->connection);
    }

    public function aggregateMin(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->where($conditions)->min($field, $this->connection);
    }

    public function aggregateMax(string $field, array $conditions = []): string
    {
        $conditions = $this->conditionBuilder->build($conditions);
        /** @var Query $query */
        $query = $this->queryFactory->create();
        return (string)$query->from($this->collectionName)->where($conditions)->max($field, $this->connection);
    }
}
