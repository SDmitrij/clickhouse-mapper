<?php

namespace Mapper\Test\Entity;

use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\CustomEvaluationRule;
use DateTime;

/**
 * @CommonInfo(tableName="some_table")
 */
class EntityWithPropertyMarkedWithInvalidEvaluationRuleAnnotation
{
    /**
     * @CustomEvaluationRule(rule="ruleMethod", class="Some\Class");
     */
    private DateTime $dateTime;
}
