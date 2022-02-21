<?php

namespace Mapper\Annotations;

use Exception;
use Webmozart\Assert\Assert;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
class PropertyColumn
{
    private ?string $tableColumn;
    private ?string $viewColumn;

    /**
     * @throws Exception
     */
    public function __construct(?string $tableColumn = null, ?string $viewColumn = null)
    {
        Assert::nullOrStringNotEmpty($tableColumn);
        Assert::nullOrStringNotEmpty($viewColumn);

        $this->tableColumn = $tableColumn;
        $this->viewColumn = $viewColumn;
    }

    public function getTableColumn(): ?string
    {
        return $this->tableColumn;
    }

    public function getViewColumn(): ?string
    {
        return $this->viewColumn;
    }
}
