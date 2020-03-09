<?php declare(strict_types=1);

namespace Indigerd\Repository\Example\Domain\Model;

class Article
{
    protected $id;

    protected $title;

    protected $content;

    protected $category;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(ArticleCategory $category): void
    {
        $this->category = $category;
    }
}
