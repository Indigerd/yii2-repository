<?php declare(strict_types=1);

namespace Indigerd\Repository\TableGateway;

use Indigerd\Repository\Exception\DeleteException;
use Indigerd\Repository\Exception\UpdateException;
use Indigerd\Repository\Query\ElasticQueryFactory;
use Indigerd\Repository\TableGateway\ConditionBuilder\ConditionBuilder;
use yii\elasticsearch\Connection;
use yii\elasticsearch\Query;

class ElasticTableGateway implements TableGatewayInterface
{
    protected $connection;

    protected $queryFactory;

    protected $conditionBuilder;

    protected $collectionName;

    protected $documentType;

    public function __construct(
        Connection $connection,
        ElasticQueryFactory $queryFactory,
        ConditionBuilder $conditionBuilder,
        string $collectionName,
        string $documentType
    ) {
        $this->connection = $connection;
        $this->queryFactory = $queryFactory;
        $this->conditionBuilder = $conditionBuilder;
        $this->collectionName = $collectionName;
        $this->documentType = $documentType;
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

    public function insert(array $data, array $options = []): ?array
    {
        /** @var \yii\elasticsearch\Connection $connection */
        $connection = $this->connection;

        if (\array_key_exists('_id', $data)) {
            $id = $data['_id'];
            unset($data['_id']);
        }
        $result = $connection->createCommand()->insert(
            $this->collectionName,
            $this->documentType,
            $data,
            (!empty($id) ? $id : null),
            $options
        );
        return ['_id' => $result['_id']];
    }

    public function updateOne(array $data, array $options = []): void
    {
        if (empty($data['_id'])) {
            throw new UpdateException($data, "Primary key _id not provided");
        }
        $key = $data['_id'];
        unset($data['_id']);
        $this->connection->createCommand()->update(
            $this->collectionName,
            $this->documentType,
            $key,
            $data,
            $options
        );
    }

    public function updateAll(array $data, array $conditions, array $options = []): int
    {
        throw new \RuntimeException('Not implemented');
    }

    public function deleteOne(array $data, array $options = []): void
    {
        if (empty($data['_id'])) {
            throw new DeleteException($data, "Primary key _id not provided");
        }
        $this->connection->createCommand()->delete(
            $this->collectionName,
            $this->documentType,
            $data['_id'],
            $options
        );
    }

    public function deleteAll(array $conditions): int
    {
        throw new \RuntimeException('Not implemented');
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
        throw new \RuntimeException('Not implemented');
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
        throw new \RuntimeException('Not implemented');
    }

    public function aggregateAverage(string $field, array $conditions = []): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function aggregateMin(string $field, array $conditions = []): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function aggregateMax(string $field, array $conditions = []): string
    {
        throw new \RuntimeException('Not implemented');
    }
}
