<?php

declare(strict_types=1);

namespace Indigerd\Repository\Hydrator;

use Indigerd\Hydrator\Hydrator;

class ElasticHydrator extends Hydrator
{
    /**
     * @param $target
     * @param array $data
     * @return object
     */
    public function hydrate($target, array $data): object
    {
        $entityData = [];
        if (isset($data['_id'])) {
            $entityData['id'] = (string)$data['_id'];
        }

        $entityData += $data['_source'] ?? [];

        return parent::hydrate($target, $entityData);
    }

    /**
     * @param object $object
     * @param array $fields
     * @return array
     */
    public function extract(object $object, array $fields = []): array
    {
        $result = parent::extract($object, $fields);
        if (isset($result['id'])) {
            $result['_id'] = $result['id'];
            unset($result['id']);
        }

        return $result;
    }
}
