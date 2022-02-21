<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\EmbeddedProperty;
use Mapper\Annotations\CommonInfo;

/**
 * @CommonInfo(tableName="some_table")
 */
class InvalidEntityThatReferenceToInvalidEmbedded
{
    /**
     * @EmbeddedProperty(embeddedClass="Mapper\Test\Entity\InvalidEmbeddedWithCommonInfoAnnotation")
     */
    private InvalidEmbeddedWithCommonInfoAnnotation $invalid;
}
