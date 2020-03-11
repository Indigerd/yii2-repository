<?php declare(strict_types=1);

namespace Indigerd\Repository;

use Indigerd\Repository\Exception\NotFoundException;

interface RepositoryInterface
{
    /**
     * @param array $conditions
     * @param array $with
     * @return object
     * @throws NotFoundException
     */
    public function findOne(array $conditions = [], array $with = []): object;

    /**
     * @param array $conditions
     * @param array $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll(
        array $conditions = [],
        array $order = [],
        int $limit = 0,
        int $offset = 0,
        array $with = []
    ): array ;

    /**
     * @param string $expression
     * @param array $conditions
     * @return string
     */
    public function aggregate(string $expression, array $conditions): string;

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateCount(string $field = '', array $conditions = []): string;

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateSum(string $field, array $conditions): string;

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateAverage(string $field, array $conditions): string;

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateMin(string $field, array $conditions): string;

    /**
     * @param string $field
     * @param array $conditions
     * @return string
     */
    public function aggregateMax(string $field, array $conditions): string;

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data= []): object;

    /**
     * @param object $entity
     * @param array $data
     */
    public function populate(object $entity, array $data): void;

    /**
     * @param object $entity
     */
    public function insert(object $entity): void;

    /**
     * @param object $entity
     */
    public function update(object $entity): void;

    /**
     * @param object $entity
     */
    public function delete(object $entity): void;

    /**
     * @param array $data
     * @param array $conditions
     * @return int
     */
    public function updateAll(array $data, array $conditions): int;

    /**
     * @param array $conditions
     * @return int
     */
    public function deleteAll(array $conditions): int;
}