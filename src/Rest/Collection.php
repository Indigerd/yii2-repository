<?php declare(strict_types=1);

namespace Indigerd\Repository\Rest;

class Collection implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var int
     */
    protected $totalPages = 1;

    /**
     * @var int
     */
    protected $totalCount = 0;

    public function __construct(object ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return mixed
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
}
