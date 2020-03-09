<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\Relation;

use Indigerd\Repository\Relation\Relation;
use PHPUnit\Framework\TestCase;
use Indigerd\Repository\Relation\RelationCollection;
use Indigerd\Repository\Example\Domain\Relation\ArticleCategoryRelation;

class RelationCollectionTest extends TestCase
{
    /** @var  RelationCollection */
    protected $collection;

    public function setUp(): void
    {
        $this->collection = new RelationCollection();
    }

    public function testAddRelation()
    {
        $relation = new ArticleCategoryRelation();
        $this->collection->addRelation($relation);
        $this->assertEquals($relation, $this->collection->getRelationByProperty($relation->getProperty()));
    }
}
