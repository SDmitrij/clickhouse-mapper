<?php

namespace Mapper\Annotations;

use Webmozart\Assert\Assert;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
class EmbeddedProperty
{
    private string $embeddedClass;

    public function __construct(string $embeddedClass)
    {
        Assert::stringNotEmpty($embeddedClass);
        Assert::classExists($embeddedClass);

        $this->embeddedClass = $embeddedClass;
    }

    public function getEmbeddedClass(): string
    {
        return $this->embeddedClass;
    }
}
