<?php

namespace Mapper\Metadata\Exception;

use Exception;

class PropertyTypeException extends Exception
{
    public function __construct(string $property, string $entity)
    {
        parent::__construct("Property: '$property' of entity: '$entity' must be strongly typed");
    }
}
