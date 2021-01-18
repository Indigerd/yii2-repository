<?php declare(strict_types=1);

namespace Indigerd\Repository\Query;

use yii\elasticsearch\Query;

class ElasticQueryFactory
{
    public function create(): Query
    {
        return new Query();
    }
}
