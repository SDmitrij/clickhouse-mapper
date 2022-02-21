<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mapper\Entity;

use JsonException;
use Mapper\Connection\Interaction\ClickHouseInteractionInterface;
use Mapper\Entity\Exception\EntityAlreadyAttachedException;
use Mapper\Evaluation\EvaluatorInterface;
use Mapper\Helper\JsonHelper;
use Mapper\Metadata\MetadataManagerInterface;
use Mapper\Validation\ValidatorInterface;
use Webmozart\Assert\Assert;

class EntityManager implements EntityManagerInterface
{
    private array $buffer;

    private ClickHouseInteractionInterface $clickHouseInteraction;
    private ValidatorInterface $validator;
    private MetadataManagerInterface $metadataManager;
    private EvaluatorInterface $evaluator;

    public function __construct(
        ClickHouseInteractionInterface $clickHouseInteraction,
        ValidatorInterface $validator,
        MetadataManagerInterface $metadataManager,
        EvaluatorInterface $evaluator
    ) {
        $this->clickHouseInteraction = $clickHouseInteraction;
        $this->validator = $validator;
        $this->metadataManager = $metadataManager;
        $this->evaluator = $evaluator;

        $this->buffer = [];
    }

    /**
     * @throws EntityAlreadyAttachedException
     * @throws JsonException
     */
    public function attach($entity): void
    {
        $entityClass = get_class($entity);

        if (array_key_exists($entityClass, $this->buffer)) {
            $entityValues = $this->evaluator->evaluate($entity, $this->buffer[$entityClass]['metadata']);
            $valuesIdentifier = $this->calculateValuesId($entityValues);
            try {
                if (array_key_exists($valuesIdentifier, $this->buffer[$entityClass]['values'])) {
                    throw new EntityAlreadyAttachedException($entityClass, $valuesIdentifier);
                }
            } catch (EntityAlreadyAttachedException $e) {
                $this->release();

                throw $e;
            }

            $this->buffer[$entityClass]['values'][$valuesIdentifier] = $entityValues;

            return;
        }

        $this->metadataManager->loadFor($entityClass);
        Assert::true(
            $this->validator->isValid($entityClass, $this->metadataManager->get()),
            "Invalid entity: '$entityClass' passed to entity manager"
        );

        $this->buffer[$entityClass]['metadata'] = $this->metadataManager->get();
        $entityValues = $this->evaluator->evaluate($entity, $this->buffer[$entityClass]['metadata']);

        $valuesIdentifier = $this->calculateValuesId($entityValues);
        $this->buffer[$entityClass]['values'][$valuesIdentifier] = $entityValues;
    }

    public function detach($entity)
    {
    }

    public function release(): void
    {
        if (0 === count($this->buffer)) {
            return;
        }

        $this->clickHouseInteraction->flush($this->buffer);
        $this->clearBuffer();
    }

    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * @throws JsonException
     */
    private function calculateValuesId(array $values): int
    {
        return crc32(JsonHelper::toJson($values));
    }

    private function clearBuffer(): void
    {
        foreach ($this->buffer as &$entity) {
            $entity['values'] = [];
        }
    }
}
