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
        unset($data['_index'], $data['_type'], $data['_score']);
        if (isset($data['_id'])) {
            $data['id'] = (string)$data['_id'];
            unset($data['_id']);
        }

        if (isset($data['_source'])) {
            $data += $data['_source'];
            unset($data['_source']);
        }

        return parent::hydrate($target, $data);
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
