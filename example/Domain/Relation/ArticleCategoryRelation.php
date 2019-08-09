<?php

namespace Indigerd\Repository\Example\Domain\Relation;

use Indigerd\Repository\Config\ConfigValue;
use Indigerd\Repository\Relation\Relation;
use Indigerd\Repository\Example\Domain\Model\ArticleCategory;

class ArticleCategoryRelation extends Relation
{
    public function __construct() {
        parent::__construct(
            new ConfigValue('category'),
            new ConfigValue('category_id'),
            new ConfigValue('id'),
            new ConfigValue('article_categories'),
            new ConfigValue(ArticleCategory::class),
            new ConfigValue('inner')
        );
    }
}
