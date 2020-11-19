<?php declare(strict_types=1);

namespace Indigerd\Repository\Hydrator;

use Indigerd\Hydrator\Hydrator;
use yii\helpers\Inflector;

/**
 * Class UnderscoreToCamelCaseHydrator
 * @package Infrastructure\Hydrator
 */
class UnderscoreToCamelCaseHydrator extends Hydrator
{
    /**
     * @param $target
     * @param array $data
     * @return object
     */
    public function hydrate($target, array $data): object
    {
        $result = [];
        foreach ($data as $name => $value) {
            $result[Inflector::variablize($name)] = $value;
        }
        return parent::hydrate($target, $result);
    }

    /**
     * @param object $object
     * @param array $fields
     * @return array
     */
    public function extract(object $object, array $fields = []): array
    {
        $result = parent::extract($object, $fields);
        $data = [];
        foreach ($result as $name => $value) {
            $data[Inflector::underscore($name)] = $value;
        }
        return $data;
    }
}
