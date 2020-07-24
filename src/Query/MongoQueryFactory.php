<?php declare(strict_types=1);

namespace Indigerd\Repository\Query;

use yii\mongodb\Query;

class MongoQueryFactory
{
    use ConditionFactory;

    public function create(): Query
    {
        return new Query();
    }
}
