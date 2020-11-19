<?php declare(strict_types=1);

namespace Indigerd\Repository\TableGateway\ConditionBuilder;

class SqlConditionBuilder extends ConditionBuilder
{
    protected function resolveConditionMethodName($fieldName): string
    {
        if (\strpos($fieldName, '.') !== false) {
            $a = \explode('.', $fieldName);
            $fieldName = $a[1];
        }
        return parent::resolveConditionMethodName($fieldName);
    }
}
