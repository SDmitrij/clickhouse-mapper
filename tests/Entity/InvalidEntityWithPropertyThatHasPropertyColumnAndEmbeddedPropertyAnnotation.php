<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\EmbeddedProperty;
use Mapper\Annotations\PropertyColumn;

/**
 * @CommonInfo(tableName="some_table")
 */
class InvalidEntityWithPropertyThatHasPropertyColumnAndEmbeddedPropertyAnnotation
{
    /**
     * @PropertyColumn
     * @EmbeddedProperty(embeddedClass="Entity\Status")
     */
    private string $invalidProperty;
    /**
     * @PropertyColumn
     */
    private string $validProperty;
}
