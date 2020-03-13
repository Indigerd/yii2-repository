<?php declare(strict_types=1);

namespace Indigerd\Repository\DataProvider;

use Indigerd\Repository\Repository;
use yii\data\BaseDataProvider;

class DataProvider extends BaseDataProvider
{
    protected $repository;

    protected $conditions;

    protected $with;

    protected $sortFields;

    public function __construct(
        Repository $repository,
        array $conditions = [],
        array $with = [],
        array $sortFields = [],
        array $config = []
    ) {
        $this->repository = $repository;
        $this->conditions = $conditions;
        $this->with = $with;
        $this->sortFields = $sortFields;
        parent::__construct($config);
    }

    protected function prepareTotalCount()
    {
        return $this->repository->aggregateCount('', $this->conditions);
    }

    protected function prepareModels()
    {
        $limit = 0;
        $offset = 0;
        $orderBy = [];

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
            $limit = $pagination->getLimit();
            $offset = $pagination->getOffset();
        }
        if (($sort = $this->getSort()) !== false) {
            foreach ($this->sortFields as $attribute) {
                $sort->attributes[$attribute] = [
                    'asc' => [$attribute => SORT_ASC],
                    'desc' => [$attribute => SORT_DESC],
                ];
            }
            $orderBy = $sort->getOrders();
        }

        return $this->repository->findAll($this->conditions, $orderBy, $limit, $offset, $this->with);
    }

    protected function prepareKeys($models)
    {
        return \array_keys($models);
    }
}
