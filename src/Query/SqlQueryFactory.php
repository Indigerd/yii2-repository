<?php declare(strict_types=1);

namespace Indigerd\Repository\Query;

use yii\db\Query;

class SqlQueryFactory
{
    public function create(): Query
    {
        return new Query();
    }
}
