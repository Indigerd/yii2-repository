<?php declare(strict_types=1);

namespace Indigerd\Repository\TableGateway\ConditionBuilder;

use yii\helpers\Inflector;

class ConditionBuilder
{
    public function build(array $params): array
    {
        $conditions = [];
        foreach ($params as $name => $value) {
            $method = $this->resolveConditionMethodName($name);
            if (\method_exists($this, $method)) {
                $newCondition = $this->$method($value);
            } else {
                $newCondition = $this->buildHashCondition($name, $value);
            }

            if (empty($newCondition)) {
                continue;
            }

            if (empty($conditions)) {
                $conditions = $newCondition;
            } else {
                $conditions = ['and', $conditions, $newCondition];
            }
        }

        return $conditions;
    }

    protected function resolveConditionMethodName($fieldName): string
    {
        return 'build' . Inflector::camelize($fieldName) . 'Condition';
    }

    protected function buildHashCondition(string $name, $value): array
    {
        return [$name => $value];
    }
}
