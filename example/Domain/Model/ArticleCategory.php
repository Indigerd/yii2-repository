<?php declare(strict_types=1);

namespace Indigerd\Repository\Example\Domain\Model;

class ArticleCategory
{
    protected $id;

    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
}
