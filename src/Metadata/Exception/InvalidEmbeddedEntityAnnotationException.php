<?php

namespace Mapper\Metadata\Exception;

use Exception;
use Mapper\Annotations\EmbeddedClass;

class InvalidEmbeddedEntityAnnotationException extends Exception
{
    public function __construct(string $entity)
    {
        parent::__construct(
            "Embedded entity: '$entity' must not has any other annotations except '".EmbeddedClass::class."' annotation"
        );
    }
}
