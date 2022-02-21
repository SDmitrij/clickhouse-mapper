<?php

namespace Mapper\Metadata\Exception;

use Exception;
use Mapper\Annotations\CommonInfo;

class InvalidBaseEntityAnnotationException extends Exception
{
    public function __construct(string $entity)
    {
        parent::__construct("Base entity: '$entity' must not has any other annotations except '".CommonInfo::class."' annotation");
    }
}
