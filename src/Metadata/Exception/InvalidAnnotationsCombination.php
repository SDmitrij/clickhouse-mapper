<?php

namespace Mapper\Metadata\Exception;

use Exception;

class InvalidAnnotationsCombination extends Exception
{
    public function __construct(string $property, string $entity)
    {
        parent::__construct("Invalid combination of annotations, property: '$property', entity: '$entity'");
    }
}
