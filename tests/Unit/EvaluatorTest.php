<?php

namespace Mapper\Test\Unit;

use Exception;
use Mapper\Evaluation\EvaluatorInterface;
use Mapper\Evaluation\Exception\EvaluatorException;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Test\Builder\OrderBuilder;
use Mapper\Test\Entity\InvalidEntityForEvaluation;
use Mapper\Test\WithContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EvaluatorTest extends TestCase
{
    use WithContainerTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
