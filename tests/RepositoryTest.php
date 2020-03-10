<?php declare(strict_types=1);

namespace Indigerd\Repository\Test;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Exception\NotFoundException;
use Indigerd\Repository\Relation\Relation;
use Indigerd\Repository\Relation\RelationCollection;
use Indigerd\Repository\Repository;
use Indigerd\Repository\TableGateway\SqlTableGateway;
use Indigerd\Repository\Test\Fixture\Article;
use Indigerd\Repository\Test\Fixture\ArticleCategoryRelation;
use Indigerd\Repository\Test\Fixture\ArticleRelationCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /** @var  Repository */
    protected $repository;

    /** @var MockObject */
    protected $tableGateway;

    /** @var MockObject */
    protected $hydrator;

    protected $modelClass = Article::class;

    /** @var MockObject */
    protected $relationCollection;

    /** @var  Relation */
    protected $articleRelation;

    public function setUp(): void
    {
        $this->tableGateway = $this->getMockBuilder(SqlTableGateway::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'queryOne',
                'queryAll',
                'insert',
                'updateOne',
                'deleteOne',
                'updateAll',
                'deleteAll',
                'aggregate',
                'aggregateCount',
                'aggregateSum',
                'aggregateAverage',
                'aggregateMin',
                'aggregateMax'
            ])
            ->getMock();
        $this->hydrator = $this->getMockBuilder(Hydrator::class)
            ->disableOriginalConstructor()
            ->setMethods(['addStrategy', 'hydrate', 'extract'])
            ->getMock();
        $this->articleRelation = new ArticleCategoryRelation();
        $this->relationCollection = new ArticleRelationCollection($this->articleRelation);
        $this->repository = new Repository(
            $this->tableGateway,
            $this->hydrator,
            $this->modelClass,
            $this->relationCollection
        );
    }

    public function testAggregate()
    {
        $expression = 'AGG_EXPRESSION';
        $conditions = ['field1' => 'conditionValue1'];
        $aggrValue = 'aggrvalue';
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregate')
            ->with($this->equalTo($expression), $this->equalTo($conditions))
            ->will($this->returnValue($aggrValue));
        $this->assertEquals($aggrValue, $this->repository->aggregate($expression, $conditions));
    }

    public function testFindOne()
    {
        $id = (string)1;
        $title = 'Article title';
        $article = new Article;
        $article->setId($id);
        $article->setTitle($title);
        $data = [
            'id' => $id,
            'title' => $title
        ];
        $conditions = ['id' => $id];
        $with = [];
        $this->tableGateway
            ->expects($this->once())
            ->method('queryOne')
            ->with($this->equalTo($conditions), $this->equalTo($with))
            ->will($this->returnValue($data));
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo($this->modelClass), $data)
            ->will($this->returnValue($article));
        $result = $this->repository->findOne($conditions, $with);
        $this->assertEquals($article, $result);
    }

    public function testFindOneNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $id = (string)2;
        $conditions = ['id' => $id];
        $with = [];
        $this->tableGateway
            ->expects($this->once())
            ->method('queryOne')
            ->with($this->equalTo($conditions), $this->equalTo($with))
            ->will($this->returnValue(null));
        $this->repository->findOne($conditions, $with);
    }

    public function testCreate()
    {
        $title = 'New Article';
        $data = ['title' => $title];
        $article = new Article();
        $article->setTitle($title);
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo($this->modelClass), $data)
            ->will($this->returnValue($article));
        $result = $this->repository->create($data);
        $this->assertInstanceOf(Article::class, $result);
        $this->assertEquals($title, $result->getTitle());
    }

    public function testPopulate()
    {
        $title = 'Title';
        $newTitle = 'New title';
        $data = ['title' => $newTitle];
        $article = new Article();
        $article->setTitle($title);
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo($article), $data);
        $this->repository->populate($article, $data);
    }
}
