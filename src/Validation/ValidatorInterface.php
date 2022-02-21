<?php

namespace Mapper\Validation;

interface ValidatorInterface
{
    public function isValid(string $entityClass, array $metadata): bool;
}
