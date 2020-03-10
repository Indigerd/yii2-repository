<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\DataProvider;

use Indigerd\Repository\Repository;
use Indigerd\Repository\DataProvider\DataProvider;
use Indigerd\Repository\DataProvider\DataProviderFactory;
use PHPUnit\Framework\TestCase;

class DataProviderFactoryTest extends TestCase
{
    protected $factory;

    public function setUp(): void
    {
        $this->factory = new DataProviderFactory();
    }

    public function testCreate()
    {
        $repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $this->assertInstanceOf(DataProvider::class, $this->factory->create($repository));
    }
}
