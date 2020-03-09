<?php declare(strict_types=1);

namespace Indigerd\Repository\Example\console\controllers;

use Indigerd\Repository\Example\Domain\Repository\ArticleRepository;
use yii\console\Controller;
use yii\base\Module;

class ArticleController extends Controller
{
    protected $repository;

    public function __construct(string $id, Module $module, ArticleRepository $repository, array $config = [])
    {
        $this->repository = $repository;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $articles = $this->repository->findAll([], [], 0, 0, ['category']);
        print_r($articles);
    }
}
