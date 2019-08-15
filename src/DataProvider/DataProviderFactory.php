<?php

namespace Indigerd\Repository\DataProvider;

use Indigerd\Repository\Repository;

class DataProviderFactory
{
    public function create(Repository $repository, array $conditions = [], $with = [])
    {
        return new DataProvider($repository, $conditions, $with);
    }
}
