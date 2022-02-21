<?php

namespace Mapper\Metadata\Visitor;

class CommonInfoPlain
{
    private string $tableName;
    private ?string $viewName;

    public function __construct(string $tableName, ?string $viewName)
    {
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
