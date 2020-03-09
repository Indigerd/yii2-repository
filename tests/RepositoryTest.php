<?php declare(strict_types=1);

namespace Indigerd\Repository\Test;

use Indigerd\Hydrator\Hydrator;
use Indigerd\Repository\Relation\Relation;
use Indigerd\Repository\Relation\RelationCollection;
use Indigerd\Repository\Repository;
use Indigerd\Repository\TableGateway\SqlTableGateway;
use Indigerd\Repository\Test\Fixture\Article;
use Indigerd\Repository\Test\Fixture\ArticleCategoryRelation;
use Indigerd\Repository\Test\Fixture\ArticleRelationCollection;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /** @var  Repository */
    protected $repository;

    protected $tableGateway;

    protected $hydrator;

    protected $modelClass = Article::class;

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
}
