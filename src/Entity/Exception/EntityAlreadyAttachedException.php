<?php

namespace Mapper\Entity\Exception;

use Exception;

class EntityAlreadyAttachedException extends Exception
{
    public function __construct(string $entity, string $entityValuesHash)
    {
        parent::__construct("Entity: '$entity' with values hash: '$entityValuesHash' has already attached");
    }
}
