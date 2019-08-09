<?php

namespace Indigerd\Repository\Example\Domain\Repository;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Example\Domain\Query\ArticleQueryBuilder;
use Indigerd\Repository\Repository;
use Indigerd\Repository\Example\Domain\Relation\ArticleRelationCollection;
use Indigerd\Repository\Example\Domain\Repository\ConfigValue\ArticleModelConfigValue;

class ArticleRepository extends Repository
{
    public function __construct(
        ArticleQueryBuilder $queryBuilder,
        Hydrator $hydrator,
        ArticleModelConfigValue $modelClass,
        ArticleRelationCollection $relationCollection
    ) {
        parent::__construct($queryBuilder, $hydrator, $modelClass, $relationCollection);
    }
}
