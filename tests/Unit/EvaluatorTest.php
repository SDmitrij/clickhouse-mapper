<?php

namespace Mapper\Test\Unit;

use Mapper\Evaluation\EvaluatorInterface;
use Mapper\Evaluation\Exception\EvaluatorException;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Test\Builder\OrderBuilder;
use Mapper\Test\Entity\InvalidEntityForEvaluation;
use Mapper\Test\WithContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mapper\Evaluation\Evaluator
 */
class EvaluatorTest extends TestCase
{
    use WithContainerTrait;

    public function testEvaluateSuccess(): void
    {
        foreach ((new OrderBuilder())->build() as $order) {
            /** @var EvaluatorInterface $evaluator */
            $evaluator = $this->getCertain(EvaluatorInterface::class);
            /** @var MetadataManagerInterface $metadataManager */
            $metadataManager = $this->getCertain(MetadataManagerInterface::class);
            $metadataManager->loadFor(get_class($order));

            $evaluated = $evaluator->evaluate($order, $metadataManager->get());
            $this->assertNotEmpty($evaluated);
        }
    }

    public function testEvaluatorException(): void
    {
        $invalid = new InvalidEntityForEvaluation();
        /** @var EvaluatorInterface $evaluator */
        $evaluator = $this->getCertain(EvaluatorInterface::class);
        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->getCertain(MetadataManagerInterface::class);
        $metadataManager->loadFor(get_class($invalid));

        $this->expectException(EvaluatorException::class);
        $evaluator->evaluate($invalid, $metadataManager->get());
    }
}
