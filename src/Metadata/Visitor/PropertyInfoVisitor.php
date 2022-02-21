<?php

namespace Mapper\Metadata\Visitor;

use Webmozart\Assert\Assert;

class PropertyInfoVisitor implements MetadataVisitorInterface
{
    private string $propertyName;

    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function visit($data): PropertyInfoPlain
    {
        Assert::keyExists(
            $data['properties'],
            $this->propertyName,
            "Cannot query info of property: '$this->propertyName'"
        );

        $propertyData = $data['properties'][$this->propertyName]['info'];
        $propertyPlain = new PropertyInfoPlain($propertyData['table_column'], $propertyData['view_column']);

        if (array_key_exists('evaluation', $propertyData)) {
            $propertyPlain
                ->setEvaluationRule($propertyData['evaluation']['rule'])
                ->setEvaluationRuleClass($propertyData['evaluation']['class']);
        }

        return $propertyPlain;
    }
}
