<?php declare(strict_types=1);

namespace Indigerd\Repository\DataProvider;

use Indigerd\Repository\Repository;

class DataProviderFactory
{
    public function create(Repository $repository, array $conditions = [], $with = [], array $sortFields = [])
    {
        return new DataProvider($repository, $conditions, $with, $sortFields);
    }
}
