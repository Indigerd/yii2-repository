<?php declare(strict_types=1);

namespace Indigerd\Repository\Test\Fixture;

class Article
{
    protected $id;

    protected $title;

    protected $category;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setCategory(ArticleCategory $category)
    {
        $this->category = $category;
    }

    public function getCategory(): ArticleCategory
    {
        return $this->category;
    }
}
