<?php

namespace Mapper\Connection\Interaction;

use ClickHouseDB\Client;

class ClickHouseInteraction implements ClickHouseInteractionInterface
{
    private Client $clickHouseClient;

    public function __construct(Client $clickHouseClient)
    {
        $this->clickHouseClient = $clickHouseClient;
    }

    public function checkTableExists(string $table): bool
    {
        $stmt = $this->clickHouseClient->select("EXISTS TABLE $table");
        $sample = $stmt->fetchRow();

        return 1 === $sample['result'];
    }

    public function getTableColumnsInfo(string $table): array
    {
        $result = [];
        $stmt = $this->clickHouseClient->select("DESCRIBE TABLE $table");

        while ($column = $stmt->fetchRow()) {
            $result[$column['name']] = $column['type'];
        }

        return $result;
    }

    public function flush(array $buffer): void
    {
        foreach ($buffer as $entity) {
            $this->clickHouseClient->insert(
                $entity['metadata']['common']['table'],
                array_values($entity['values']),
                $entity['metadata']['common']['insert_columns']
            );
        }
    }
}
