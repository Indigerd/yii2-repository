<?php declare(strict_types=1);

namespace Indigerd\Repository\Test;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Exception\InvalidModelClassException;
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
            'title' => $title,
            'category' => []
        ];
        $conditions = ['id' => $id];
        $with = ['category'];
        $relations = ['category' => $this->articleRelation];
        $this->tableGateway
            ->expects($this->once())
            ->method('queryOne')
            ->with($this->equalTo($conditions), $this->equalTo($relations))
            ->will($this->returnValue($data));
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo($this->modelClass), $data)
            ->will($this->returnValue($article));
        $this->hydrator
            ->expects($this->once())
            ->method('addStrategy');
        $result = $this->repository->findOne($conditions, $with);
        $this->assertEquals($article, $result);
    }

    public function testFindAll()
    {
        $conditions = [];
        $order = ['id' => 'desc'];
        $limit = 20;
        $offset = 0;
        $with = [];
        $relations = [];
        $id = '1';
        $title = 'Article title';
        $resultSet = [
            [
                'id' => $id,
                'title' => $title
            ]
        ];
        $article = new Article();
        $article->setId($id);
        $article->setTitle($title);
        $this->tableGateway
            ->expects($this->once())
            ->method('queryAll')
            ->with(
                $this->equalTo($conditions),
                $this->equalTo($order),
                $this->equalTo($limit),
                $this->equalTo($offset),
                $this->equalTo($relations)
            )->will($this->returnValue($resultSet));
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo(Article::class), $this->equalTo($resultSet[0]))
            ->will($this->returnValue($article));
        $result = $this->repository->findAll($conditions, $order, $limit, $offset, $with);
        $this->assertEquals([$article], $result);
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

    public function testDeleteAll()
    {
        $conditions = ['field' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('deleteAll')
            ->with($this->equalTo($conditions));
        $this->repository->deleteAll($conditions);
    }

    public function testUpdateAll()
    {
        $data = ['field' => 'value'];
        $conditions = ['condition' => 'value'];
        $count = 10;
        $this->tableGateway
            ->expects($this->once())
            ->method('updateAll')
            ->with($this->equalTo($data), $this->equalTo($conditions))
            ->will($this->returnValue($count));
        $result = $this->repository->updateAll($data, $conditions);
        $this->assertEquals($count, $result);
    }

    public function testDelete()
    {
        $id = (string)1;
        $model = new Article();
        $model->setId($id);
        $extract = ['id' => $id];
        $this->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo($model))
            ->will($this->returnValue($extract));
        $this->tableGateway
            ->expects($this->once())
            ->method('deleteOne')
            ->with($this->equalTo($extract));
        $this->repository->delete($model);
    }

    public function testValidateModelException()
    {
        $model = new \stdClass();
        $this->expectException(InvalidModelClassException::class);
        $this->repository->delete($model);
    }

    public function testUpdate()
    {
        $id = (string)1;
        $title = 'Article title';
        $model = new Article();
        $model->setId($id);
        $model->setTitle($title);
        $extract = ['id' => $id, 'title' => $title];
        $this->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo($model))
            ->will($this->returnValue($extract));
        $this->tableGateway
            ->expects($this->once())
            ->method('updateOne')
            ->with($this->equalTo($extract));
        $this->repository->update($model);
    }

    public function testInsert()
    {
        $id = (string)1;
        $title = 'Article title';
        $model = new Article();
        $model->setTitle($title);
        $extract = ['title' => $title];
        $keys = ['id' => $id];
        $this->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo($model))
            ->will($this->returnValue($extract));
        $this->tableGateway
            ->expects($this->once())
            ->method('insert')
            ->with($this->equalTo($extract))
            ->will($this->returnValue($keys));
        $this->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->equalTo($model), $this->equalTo($keys));
        $this->repository->insert($model);
    }

    public function testAggregateCount()
    {
        $aggregateValue = '1';
        $field = 'field';
        $conditions = ['condition' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregateCount')
            ->with($this->equalTo($field), $this->equalTo($conditions))
            ->will($this->returnValue($aggregateValue));
        $result = $this->repository->aggregateCount($field, $conditions);
        $this->assertEquals($aggregateValue, $result);
    }

    public function testAggregateSum()
    {
        $aggregateValue = '1';
        $field = 'field';
        $conditions = ['condition' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregateSum')
            ->with($this->equalTo($field), $this->equalTo($conditions))
            ->will($this->returnValue($aggregateValue));
        $result = $this->repository->aggregateSum($field, $conditions);
        $this->assertEquals($aggregateValue, $result);
    }

    public function testAggregateAverage()
    {
        $aggregateValue = '1';
        $field = 'field';
        $conditions = ['condition' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregateAverage')
            ->with($this->equalTo($field), $this->equalTo($conditions))
            ->will($this->returnValue($aggregateValue));
        $result = $this->repository->aggregateAverage($field, $conditions);
        $this->assertEquals($aggregateValue, $result);
    }

    public function testAggregateMin()
    {
        $aggregateValue = '1';
        $field = 'field';
        $conditions = ['condition' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregateMin')
            ->with($this->equalTo($field), $this->equalTo($conditions))
            ->will($this->returnValue($aggregateValue));
        $result = $this->repository->aggregateMin($field, $conditions);
        $this->assertEquals($aggregateValue, $result);
    }

    public function testAggregateMax()
    {
        $aggregateValue = '1';
        $field = 'field';
        $conditions = ['condition' => 'value'];
        $this->tableGateway
            ->expects($this->once())
            ->method('aggregateMax')
            ->with($this->equalTo($field), $this->equalTo($conditions))
            ->will($this->returnValue($aggregateValue));
        $result = $this->repository->aggregateMax($field, $conditions);
        $this->assertEquals($aggregateValue, $result);
    }
}
