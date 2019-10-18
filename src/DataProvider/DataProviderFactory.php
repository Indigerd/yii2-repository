<?php

namespace Indigerd\Repository\DataProvider;

use Indigerd\Repository\RepositoryInterface;

class DataProviderFactory
{
    public function create(RepositoryInterface $repository, array $conditions = [], $with = [])
    {
        return new DataProvider($repository, $conditions, $with);
    }
}
