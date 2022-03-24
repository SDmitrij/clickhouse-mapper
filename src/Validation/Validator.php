<?php

namespace Mapper\Validation;

use Mapper\Connection\Interaction\ClickHouseInteractionInterface;
use Mapper\Helper\StringHelper;
use Psr\Cache\CacheItemPoolInterface;
use Webmozart\Assert\Assert;

class Validator implements ValidatorInterface
{
    private ClickHouseInteractionInterface $clickHouseInteraction;
    private CacheItemPoolInterface $validationCache;

    public function __construct(ClickHouseInteractionInterface $clickHouseInteraction, CacheItemPoolInterface $cache)
    {
        $this->clickHouseInteraction = $clickHouseInteraction;
        $this->validationCache = $cache;
    }

    public function isValid(string $entityClass, array $metadata): bool
    {
        Assert::eq(
            $metadata['common']['entity'],
            $entityClass,
            "Invalid metadata passed for entity: '$entityClass'"
        );

        return $this->validationCache->get(
            strtolower(StringHelper::getClassShortName($entityClass)).'-validation', function () use ($metadata) {
                $this->validate($metadata);

                return true;
            });
    }

    private function validate(array $metadata): void
    {
        $validateColumns = function (array $metadata, array $clickHouseColumns, string $descriptor) use (&$validateColumns) {
            foreach ($metadata['properties'] as $propertyName => $propertyInfo) {
                Assert::keyExists(
                    $clickHouseColumns,
                    $propertyInfo['info'][$descriptor],
                    "Property: $propertyName of entity: '".$metadata['common']['entity']."' does not maps clickhouse table"
                );

                if (str_contains($clickHouseColumns[$propertyInfo['info'][$descriptor]], 'Nullable')) {
                    Assert::true(
                        $propertyInfo['info']['nullable'],
                        "Invalid types map, property: '$propertyName' of entity: '".$metadata['common']['entity']."' has not nullable type"
                    );
                }
            }
        };

        Assert::true(
            $this->clickHouseInteraction->checkTableExists($metadata['common']['table']),
            "Table: '".$metadata['common']['table']."' does not exists in the target ClickHouse database"
        );

        $validateColumns(
            $metadata,
            $this->clickHouseInteraction->getTableColumnsInfo($metadata['common']['table']),
            'table_column'
        );

        if (null !== $metadata['common']['view']) {
            Assert::true($this->clickHouseInteraction->checkTableExists($metadata['common']['view']));
            $validateColumns(
                $metadata,
                $this->clickHouseInteraction->getTableColumnsInfo($metadata['common']['view']),
                'view_column'
            );
        }
    }
}
