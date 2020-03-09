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
    'Indigerd\Repository\Example\Domain\TableGateway\ArticleTableGateway',
    function () {
        return new \Indigerd\Repository\Example\Domain\TableGateway\ArticleTableGateway(
            Yii::$app->db,
            new \Indigerd\Repository\Query\SqlQueryFactory(),
            'articles'
        );
    }
);

Yii::$container->set(
    'Indigerd\Repository\Example\Domain\Repository\ArticleRepository',
    function () {
        return new \Indigerd\Repository\Example\Domain\Repository\ArticleRepository(
            Yii::$container->get('Indigerd\Repository\Example\Domain\TableGateway\ArticleTableGateway'),
            Yii::$container->get('Indigerd\Hydrator\Hydrator'),
            \Indigerd\Repository\Example\Domain\Model\Article::class,
            Yii::$container->get('Indigerd\Repository\Example\Domain\Relation\ArticleRelationCollection')
        );
    }
);