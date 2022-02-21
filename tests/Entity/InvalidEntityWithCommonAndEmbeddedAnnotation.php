<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\EmbeddedClass;

/**
 * @CommonInfo(tableName="some_table")
 * @EmbeddedClass
 */
class InvalidEntityWithCommonAndEmbeddedAnnotation
{
}
