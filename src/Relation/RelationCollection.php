<?php

namespace Indigerd\Repository\Relation;

class RelationCollection
{
    protected $relations = [];

    public function __construct(Relation ...$relations)
    {
        $this->relations = $relations;
    }

    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    public function getRelationByProperty(string $property): ?Relation
    {
        foreach ($this->relations as $relation) {
            if ($relation->getProperty() == $property) {
                return $relation;
            }
        }
    }
}
