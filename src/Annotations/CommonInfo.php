<?php

namespace Mapper\Annotations;

use Webmozart\Assert\Assert;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
class CommonInfo
{
    private string $tableName;
    private ?string $viewName;

    public function __construct(string $tableName, ?string $viewName = null)
    {
        Assert::stringNotEmpty($tableName);
        Assert::nullOrStringNotEmpty($viewName);

        $this->tableName = $tableName;
        $this->viewName = $viewName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getViewName(): ?string
    {
        return $this->viewName;
    }
}
