<?php

namespace Mapper\Evaluation;

use Mapper\Evaluation\Exception\EvaluatorException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Webmozart\Assert\Assert;

class Evaluator implements EvaluatorInterface
{
    public function evaluate($entity, array $metadata): array
    {
        Assert::object($entity, 'Expect object');
        Assert::eq(
            $metadata['common']['entity'],
            get_class($entity),
            "Invalid metadata passed for entity: '".get_class($entity)."'"
        );

        /**
         * @throws ReflectionException
         * @throws EvaluatorException
         */
        $readEntityValues = function ($entity, array &$result) use (
            &$readEntityValues,
            $metadata
        ) {
            $reflectionClass = new ReflectionClass($entity);
            foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE) as $property) {
                if (array_key_exists('embedded', $metadata['common'])) {
                    if (array_key_exists($property->getName(), $metadata['common']['embedded'])) {
                        $property->setAccessible(true);
                        $readEntityValues($property->getValue($entity), $result);
                        continue;
                    }
                }

                $property->setAccessible(true);
                if (!$property->isInitialized($entity)) {
                    if (!$metadata['properties'][$property->getName()]['info']['nullable']) {
                        throw new EvaluatorException($property->getName(), $reflectionClass->getName());
                    } else {
                        $property->setValue($entity);
                    }
                }

                $currentValue = $property->getValue($entity);

                if (array_key_exists('evaluation', $metadata['properties'][$property->getName()]['info'])) {
                    $method = new ReflectionMethod(
                        $metadata['properties'][$property->getName()]['info']['evaluation']['class'],
                        $metadata['properties'][$property->getName()]['info']['evaluation']['rule']
                    );
                    $result[] = $method->invoke(null, $currentValue);
                } else {
                    $result[] = $currentValue;
                }
            }
        };

        $result = [];
        $readEntityValues($entity, $result);

        return $result;
    }
}
