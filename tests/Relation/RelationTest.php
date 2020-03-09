<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\Relation;

use PHPUnit\Framework\TestCase;
use Indigerd\Repository\Relation\Relation;

class RelationTest extends TestCase
{
    /** @var  Relation */
    protected $relation;

    protected $property = 'category';

    protected $field = 'category_id';

    protected $relatedField = 'id';

    protected $relatedCollection = 'article_categories';

    protected $relatedModel = \Indigerd\Repository\Example\Domain\Model\ArticleCategory::class;

    protected $relationType = 'inner';

    public function setUp(): void
    {
        $this->relation = new Relation(
            $this->property,
            $this->field,
            $this->relatedField,
            $this->relatedCollection,
            $this->relatedModel,
            $this->relationType
        );
    }

    public function testGetProperty()
    {
        $this->assertEquals($this->property, $this->relation->getProperty());
    }

    public function testGetField()
    {
        $this->assertEquals($this->field, $this->relation->getField());
    }

    public function testGetRelatedField()
    {
        $this->assertEquals($this->relatedField, $this->relation->getRelatedField());
    }

    public function testGetRelatedCollection()
    {
        $this->assertEquals($this->relatedCollection, $this->relation->getRelatedCollection());
    }

    public function testGetRelatedModel()
    {
        $this->assertEquals($this->relatedModel, $this->relation->getRelatedModel());
    }

    public function testGetRelationType()
    {
        $this->assertEquals($this->relationType, $this->relation->getRelationType());
    }
}
