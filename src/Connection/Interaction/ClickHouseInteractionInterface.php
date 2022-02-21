<?php

namespace Mapper\Connection\Interaction;

interface ClickHouseInteractionInterface
{
    public function checkTableExists(string $table): bool;

    public function getTableColumnsInfo(string $table): array;

    public function flush(array $buffer): void;
}
