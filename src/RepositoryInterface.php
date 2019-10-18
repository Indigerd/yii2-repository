<?php

namespace Indigerd\Repository;

interface RepositoryInterface
{
    public function findOne(array $conditions = [], array $with = []): ?object;

    public function findAll(array $conditions = [], array $order = [], int $limit = 0, int $offset = 0, array $with = []): array;

    public function aggregate(string $expression, array $conditions);

    public function aggregateCount(string $field = '', array $conditions = []);

    public function aggregateSum(string $field, array $conditions);

    public function aggregateAverage(string $field, array $conditions);

    public function aggregateMin(string $field, array $conditions);

    public function aggregateveMax(string $field, array $conditions);

    public function insert(object $model);

    public function update(object $model);

    public function delete(object $model);

    public function updateAll(array $data, array $conditions): int;

    public function deleteAll(array $conditions): int;
}
