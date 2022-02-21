<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\PropertyColumn;

/**
 * @CommonInfo(tableName="some_table")
 */
class InvalidEntityForEvaluation
{
    /**
     * @PropertyColumn
     */
    private string $unInitProperty;
}
