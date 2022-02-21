<?php

namespace Mapper\Metadata;

use Doctrine\Common\Annotations\PsrCachedReader;
use Exception;
use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\CustomEvaluationRule;
use Mapper\Annotations\EmbeddedClass;
use Mapper\Annotations\EmbeddedProperty;
use Mapper\Annotations\Nullable;
use Mapper\Annotations\PropertyColumn;
use Mapper\Entity\EntityTypesEnum;
use Mapper\Helper\StringHelper;
use Mapper\Metadata\Exception\InvalidAnnotationsCombination;
use Mapper\Metadata\Exception\InvalidBaseEntityAnnotationException;
use Mapper\Metadata\Exception\InvalidEmbeddedEntityAnnotationException;
use Mapper\Metadata\Exception\NullableAnnotationException;
use Mapper\Metadata\Exception\PropertyTypeException;
use Mapper\Metadata\Visitor\CommonInfoPlain;
use Mapper\Metadata\Visitor\MetadataVisitorInterface;
use Mapper\Metadata\Visitor\PropertyInfoPlain;
use Psr\Cache\InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

class MetadataManager implements MetadataManagerInterface
{
    private PsrCachedReader $annotationReader;
    private CacheInterface $metadataCache;

    private ?array $metadata = null;
    private ?MetadataVisitorInterface $visitor = null;
    private ?string $entity = null;

    public function __construct(PsrCachedReader $reader, CacheInterface $cache)
    {
        $this->annotationReader = $reader;
        $this->metadataCache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     * @noinspection PhpUnusedParameterInspection
     */
    public function loadFor(string $entityClass): void
    {
        Assert::classExists($entityClass, "Entity: '$entityClass' does not exists");

        if ($this->entity !== $entityClass) {
            $this->entity = $entityClass;
        } else {
            return;
        }

        $this->metadata = $this->metadataCache->get(
            StringHelper::getClassShortName($entityClass),
            function (ItemInterface $item) use ($entityClass) {
                return $this->parseMetadata($entityClass);
            }
        );
    }

    /**
     * @return array|CommonInfoPlain|PropertyInfoPlain
     */
    public function get()
    {
        Assert::notNull($this->entity, 'Specify entity class to load metadata for it');
        Assert::notNull($this->metadata, "There is no any metadata for entity: '$this->entity'");

        if (null === $this->visitor) {
            return $this->metadata;
        }

        return $this->visitor->visit($this->metadata);
    }

    public function accept(MetadataVisitorInterface $metadataVisitor): self
    {
        $this->visitor = $metadataVisitor;

        return $this;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function parseMetadata(string $class): array
    {
        $entity = new ReflectionClass($class);

        $this->validateEntityAnnotations($entity, EntityTypesEnum::BASE);
        $metadata = $this->getMetadataTemplate($entity);

        $parser = function (ReflectionClass $entity, array &$metadata) use (&$parser) {
            foreach ($entity->getProperties(ReflectionProperty::IS_PRIVATE) as $property) {
                $propertyName = $property->getName();
                $entityName = $entity->getName();

                if (null === $property->getType()) {
                    throw new PropertyTypeException($propertyName, $entityName);
                }
                $propertyColumn = $this->annotationReader->getPropertyAnnotation(
                    $entity->getProperty($propertyName),
                    PropertyColumn::class
                );
                $embedded = $this->annotationReader->getPropertyAnnotation(
                    $entity->getProperty($propertyName), EmbeddedProperty::class
                );

                if (null !== $propertyColumn && null !== $embedded) {
                    throw new InvalidAnnotationsCombination($propertyName, $entityName);
                } elseif (null === $propertyColumn && null !== $embedded) {
                    $embeddedClass = new ReflectionClass($embedded->getEmbeddedClass());
                    $metadata['common']['embedded'][$propertyName] = $embedded->getEmbeddedClass();

                    $this->validateEntityAnnotations($embeddedClass, EntityTypesEnum::EMBEDDED);

                    $parser($embeddedClass, $metadata);
                    continue;
                } elseif (null === $propertyColumn && null === $embedded) {
                    continue;
                }

                $propertyInfoArray = [
                    'table_column' => $propertyColumn->getTableColumn() ?? StringHelper::camelToSnake($propertyName),
                    'view_column' => (null === $metadata['common']['view'])
                        ? $propertyColumn->getViewColumn() : StringHelper::camelToSnake($propertyName),
                    'nullable' => $this->isNullable($property),
                ];
                $metadata['common']['insert_columns'][] = $propertyInfoArray['table_column'];

                $rule = $this->annotationReader->getPropertyAnnotation(
                    $property,
                    CustomEvaluationRule::class
                );
                if (null !== $rule) {
                    $propertyInfoArray['evaluation'] = ['rule' => $rule->getRule(), 'class' => $rule->getClass()];
                }

                $metadata['properties'][$propertyName]['info'] = $propertyInfoArray;
            }
        };

        $parser($entity, $metadata);

        return $metadata;
    }

    /**
     * @throws InvalidBaseEntityAnnotationException
     * @throws InvalidEmbeddedEntityAnnotationException
     */
    private function validateEntityAnnotations(ReflectionClass $entity, string $entityType): void
    {
        $entityName = $entity->getName();
        if (EntityTypesEnum::EMBEDDED === $entityType) {
            if (null === $this->annotationReader->getClassAnnotation($entity, EmbeddedClass::class)) {
                throw new InvalidEmbeddedEntityAnnotationException($entityName);
            } elseif (null !== $this->annotationReader->getClassAnnotation($entity, CommonInfo::class)) {
                throw new InvalidEmbeddedEntityAnnotationException($entityName);
            }
        } elseif (EntityTypesEnum::BASE === $entityType) {
            if (null === $this->annotationReader->getClassAnnotation($entity, CommonInfo::class)) {
                throw new InvalidBaseEntityAnnotationException($entityName);
            } elseif (null !== $this->annotationReader->getClassAnnotation($entity, EmbeddedClass::class)) {
                throw new InvalidBaseEntityAnnotationException($entityName);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function isNullable(ReflectionProperty $property): bool
    {
        $nullable = $this->annotationReader->getPropertyAnnotation($property, Nullable::class);

        if (null === $nullable) {
            if ($property->getType()->allowsNull()) {
                throw new NullableAnnotationException($property->getName(), $property->getDeclaringClass()->getName());
            } else {
                return false;
            }
        } else {
            if (!$property->getType()->allowsNull()) {
                throw new NullableAnnotationException($property->getName(), $property->getDeclaringClass()->getName());
            } else {
                return true;
            }
        }
    }

    private function getMetadataTemplate(ReflectionClass $entity): array
    {
        $common = $this->annotationReader->getClassAnnotation($entity, CommonInfo::class);

        return [
            'common' => [
                'entity' => $entity->getName(),
                'table' => $common->getTableName(),
                'view' => $common->getViewName(),
                'insert_columns' => [],
            ],
            'properties' => [],
        ];
    }
}
