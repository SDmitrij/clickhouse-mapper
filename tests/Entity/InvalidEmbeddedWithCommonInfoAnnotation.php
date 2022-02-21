<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\EmbeddedClass;
use Mapper\Annotations\CommonInfo;

/**
 * @EmbeddedClass
 * @CommonInfo(tableName="some_table")
 */
class InvalidEmbeddedWithCommonInfoAnnotation
{
}
