<?php

namespace Indigerd\Repository\Example\Domain\Query;

use yii\db\Connection;
use Indigerd\Repository\Query\SqlQueryBuilder;
use Indigerd\Repository\Example\Domain\Query\ConfigValue\ArticleCollectionConfigValue;

class ArticleQueryBuilder extends SqlQueryBuilder
{
    public function __construct(Connection $connection, ArticleCollectionConfigValue $collectionName)
    {
        parent::__construct($connection, $collectionName);
    }
}
