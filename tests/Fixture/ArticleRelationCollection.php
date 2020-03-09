<?php

namespace Indigerd\Repository\Test\Fixture;

use Indigerd\Repository\Relation\RelationCollection;

class ArticleRelationCollection extends RelationCollection
{
    public function __construct(ArticleCategoryRelation $relation)
    {
        parent::__construct($relation);
    }
}
