<?php declare(strict_types=1);

namespace Indigerd\Repository\TableGateway;

interface TableGatewayInterface
{
    public function queryOne(array $conditions, array $relations = []): ?array;

    public function queryAll(array $conditions, array $order = [], int $limit = 0, int $offset = 0, array $relations = []): array;

    /**
     * @return array Array of primary keys
     */
    public function insert(array $data): ?array;

    public function updateOne(array $data): void;

    public function deleteOne(array $data): void;

    public function updateAll(array $data, array $conditions, array $options = []): int;

    public function deleteAll(array $conditions): int;

    public function aggregate(string $expression, array $conditions): string;

    public function aggregateCount(string $field = '', array $conditions = []): string;

    public function aggregateSum(string $field, array $conditions = []): string;

    public function aggregateAverage(string $field, array $conditions = []): string;

    public function aggregateMin(string $field, array $conditions = []): string;

    public function aggregateMax(string $field, array $conditions = []): string;
}
