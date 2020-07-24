<?php declare(strict_types=1);

namespace Indigerd\Repository\Query;

use yii\db\conditions\AndCondition;
use yii\db\conditions\OrCondition;

trait ConditionFactory
{
    public function createOrCondition($expressions): OrCondition
    {
        return new OrCondition($expressions);
    }

    public function createAndCondition($expressions): AndCondition
    {
        return new AndCondition($expressions);
    }
}
