<?php

namespace Mapper\Evaluation;

interface EvaluatorInterface
{
    public function evaluate($entity, array $metadata): array;
}
