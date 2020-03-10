<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\Query;

use Indigerd\Repository\Query\MongoQueryFactory;
use PHPUnit\Framework\TestCase;
use yii\mongodb\Query;

class MongoQueryFactoryTest extends TestCase
{
    protected $factory;

    public function setUp(): void
    {
        $this->factory = new MongoQueryFactory();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(Query::class, $this->factory->create());
    }
}
