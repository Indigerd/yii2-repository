<?php
return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'Indigerd\Repository\Example\console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => '@console/migrations',
            'migrationTable' => '{{%system_db_migration}}'
        ],
        'article' => [
            'class' => 'Indigerd\Repository\Example\console\controllers\ArticleController'
        ]
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=repo',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
    ],
];
