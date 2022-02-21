<?php

namespace Mapper\Metadata\Exception;

use Exception;

class NullableAnnotationException extends Exception
{
    public function __construct(string $property, string $entity)
    {
        parent::__construct(
            "Property: '$property' of entity: '$entity' has problem with nullability"
        );
    }
}
