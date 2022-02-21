<?php

namespace Mapper\Annotations;

use Webmozart\Assert\Assert;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
class CustomEvaluationRule
{
    private string $rule;
    private string $class;

    public function __construct(string $rule, string $class)
    {
        Assert::stringNotEmpty($rule);
        Assert::stringNotEmpty($class);
        Assert::classExists($class);
        Assert::methodExists($class, $rule);

        $this->rule = $rule;
        $this->class = $class;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}