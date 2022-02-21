<?php

namespace Mapper\Metadata\Visitor;

class PropertyInfoPlain
{
    private string $tableColumn;
    private ?string $viewColumn;

    private ?string $evaluationRuleClass = null;
    private ?string $evaluationRule = null;

    public function __construct(string $tableColumn, ?string $viewColumn)
    {
        $this->tableColumn = $tableColumn;
        $this->viewColumn = $viewColumn;
    }

    public function getTableColumn(): string
    {
        return $this->tableColumn;
    }

    public function getViewColumn(): ?string
    {
        return $this->viewColumn;
    }

    public function getEvaluationRuleClass(): ?string
    {
        return $this->evaluationRuleClass;
    }

    public function setEvaluationRuleClass(?string $evaluationRuleClass): self
    {
        $this->evaluationRuleClass = $evaluationRuleClass;

        return $this;
    }

    public function getEvaluationRule(): ?string
    {
        return $this->evaluationRule;
    }

    public function setEvaluationRule(?string $evaluationRule): self
    {
        $this->evaluationRule = $evaluationRule;

        return $this;
    }
}
