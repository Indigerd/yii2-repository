<?php

namespace Indigerd\Repository\Relation;

use Indigerd\Repository\Config\ConfigValueInterface;

class Relation
{
    protected $property;

    protected $field;

    protected $relatedField;

    protected $relatedCollection;

    protected $relatedModel;

    protected $relationType;

    public function __construct(
        ConfigValueInterface $property,
        ConfigValueInterface $field,
        ConfigValueInterface $relatedField,
        ConfigValueInterface $relatedCollection,
        ConfigValueInterface $relatedModel,
        ConfigValueInterface $relationType
    ) {
        $this->property = $property->getValue();
        $this->field = $field->getValue();
        $this->relatedField = $relatedField->getValue();
        $this->relatedCollection = $relatedCollection->getValue();
        $this->relatedModel = $relatedModel->getValue();
        $this->relationType = $relationType->getValue();
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getRelatedField(): string
    {
        return $this->relatedField;
    }

    public function getRelatedCollection(): string
    {
        return $this->getRelatedCollection();
    }

    public function getRelatedModel(): string
    {
        return $this->relatedModel;
    }

    public function getRelationType(): string
    {
        return $this->relationType;
    }
}
