<?php

namespace Mapper\Evaluation\Exception;

use Exception;

class EvaluatorException extends Exception
{
    public function __construct(string $property, string $entity)
    {
        parent::__construct("Cannot evaluate property: '$property' of entity: '$entity'");
    }
}
