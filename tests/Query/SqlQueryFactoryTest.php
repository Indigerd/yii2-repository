<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\Query;

use Indigerd\Repository\Query\SqlQueryFactory;
use PHPUnit\Framework\TestCase;
use yii\db\Query;

class SqlQueryFactoryTest extends TestCase
{
    protected $factory;

    public function setUp(): void
    {
        $this->factory = new SqlQueryFactory();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(Query::class, $this->factory->create());
    }
}
