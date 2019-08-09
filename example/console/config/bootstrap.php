<?php

Yii::setAlias('@console', realpath(__DIR__ . '/../../console'));

Yii::$container->set(
    'Indigerd\Hydrator\Accessor\AccessorInterface',
    'Indigerd\Hydrator\Accessor\PropertyAccessor'
);

Yii::$container->set('Indigerd\Hydrator\Hydrator');
Yii::$container->set('Indigerd\Repository\Example\Domain\Relation\ArticleCategoryRelation');
Yii::$container->set('Indigerd\Repository\Example\Domain\Relation\ArticleRelationCollection');
Yii::$container->set(
    'Indigerd\Repository\Example\Domain\Repository\ConfigValue\ArticleModelConfigValue',
    'Indigerd\Repository\Example\Domain\Repository\ConfigValue\ArticleModelConfigValue',
    [\Indigerd\Repository\Example\Domain\Model\Article::class]
);

Yii::$container->set(
    'Indigerd\Repository\Example\Domain\Query\ConfigValue\ArticleCollectionConfigValue',
    'Indigerd\Repository\Example\Domain\Query\ConfigValue\ArticleCollectionConfigValue',
    ['articles']
);

Yii::$container->set(
    'Indigerd\Repository\Example\Domain\Query\ArticleQueryBuilder',
    function () {
        return new Indigerd\Repository\Example\Domain\Query\ArticleQueryBuilder(
            Yii::$app->db,
            Yii::$container->get('Indigerd\Repository\Example\Domain\Query\ConfigValue\ArticleCollectionConfigValue')
        );
    }
);

Yii::$container->set('Indigerd\Repository\Example\Domain\Repository\ArticleRepository');