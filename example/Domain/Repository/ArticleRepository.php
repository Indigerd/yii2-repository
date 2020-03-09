<?php declare(strict_types=1);

namespace Indigerd\Repository\Example\Domain\Repository;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Repository;
use Indigerd\Repository\Example\Domain\Relation\ArticleRelationCollection;
use Indigerd\Repository\Example\Domain\TableGateway\ArticleTableGateway;

class ArticleRepository extends Repository
{
    public function __construct(
        ArticleTableGateway $tableGateway,
        Hydrator $hydrator,
        string $modelClass,
        ArticleRelationCollection $relationCollection
    ) {
        parent::__construct($tableGateway, $hydrator, $modelClass, $relationCollection);
    }
}
